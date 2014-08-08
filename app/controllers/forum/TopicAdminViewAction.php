<?php

/**
 * 帖子管理html视图接口
 * 
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class TopicAdminViewAction extends CAction {

    public function run($fid, $tid, $pid, $act, $type='topic') {
        $app = Yii::app()->getController()->mobcentDiscuzApp;
        $app->loadForum($fid, $tid);

        if (!empty($_POST)) {
            // 把$_POST转成utf-8, 这是由于discuz源码会在mobile情况下把$_POST预先转码成对应的charset,
            $_POST = array_intersect_key($_REQUEST, $_POST);
            // 手动把转成utf-8的$_POST数据再次转成对应的charset
            foreach ($_POST as $key => $value) {
                if (is_string($value)) {
                    $_POST[$key] = WebUtils::t($value);
                }
            }
            $_GET = array_merge($_GET, $_POST);
        }
        $this->_adminTopic($fid, $tid, $pid, $act, $type);
    }

    private function _adminTopic($fid, $tid, $pid, $act, $type) {
        global $_G;

        $errorMsg = '';

        // 在DISCUZ_ROOT/source/module/forum/forum_topicadmin.php基础上进行二次开发
        loadcache(array('modreasons', 'stamptypeid', 'threadtableids'));
        
        require_once libfile('function/post');
        require_once libfile('function/misc');

        if(($_G['group']['reasonpm'] == 2 || $_G['group']['reasonpm'] == 3) || !empty($_GET['sendreasonpm'])) {
            $forumname = strip_tags($_G['forum']['name']);
            $sendreasonpm = 1;
        } else {
            $sendreasonpm = 0;
        }

        if ($type == 'topic') {
            // 在DISCUZ_ROOT/source/include/topicadmin/topicadmin_moderate.php基础上进行二次开发        
            $thread = $_G['forum_thread'];

            $threadlist[$thread['tid']] = $thread;

            $modpostsnum = count($threadlist);

            empty($threadlist[$_G['tid']]['displayorder']) ? $stickcheck[0] ='selected="selected"' : $stickcheck[$threadlist[$_G['tid']]['displayorder']] = 'selected="selected"';
            empty($threadlist[$_G['tid']]['digest']) ? $digestcheck[0] = 'selected="selected"' : $digestcheck[$threadlist[$_G['tid']]['digest']] = 'selected="selected"';

            if (!empty($_POST)) {
                $tidsarr = array_keys($threadlist);
                $moderatetids = dimplode($tidsarr);
                $reason = checkreasonpm();
                $stampstatus = 0;
                $stampaction = 'SPA';

                $operationMap = array('top' => 'stick', 'marrow' => 'digest', 'delete' => 'delete');
                $operation = $operationMap[$act];

                $updatemodlog = TRUE;

                switch ($act) {
                    case 'top':
                        $sticklevel = intval($_GET['sticklevel']);
                        // if($sticklevel < 0 || $sticklevel > 3 || $sticklevel > $_G['group']['allowstickthread']) {
                        //     showmessage('no_privilege_stickthread');
                        // }
                        $expiration = checkexpiration($_GET['expirationstick'], $operation);
                        $expirationstick = $sticklevel ? $_GET['expirationstick'] : 0;

                        $forumstickthreads = $_G['setting']['forumstickthreads'];
                        $forumstickthreads = isset($forumstickthreads) ? dunserialize($forumstickthreads) : array();
                        C::t('forum_thread')->update($tidsarr, array('displayorder'=>$sticklevel, 'moderated'=>1), true);
                        $delkeys = array_keys($threadlist);
                        foreach($delkeys as $k) {
                            unset($forumstickthreads[$k]);
                        }
                        C::t('common_setting')->update('forumstickthreads', $forumstickthreads);

                        $stickmodify = 0;
                        foreach($threadlist as $thread) {
                            $stickmodify = (in_array($thread['displayorder'], array(2, 3)) || in_array($sticklevel, array(2, 3))) && $sticklevel != $thread['displayorder'] ? 1 : $stickmodify;
                        }

                        if($_G['setting']['globalstick'] && $stickmodify) {
                            require_once libfile('function/cache');
                            updatecache('globalstick');
                        }

                        $modaction = $sticklevel ? ($expiration ? 'EST' : 'STK') : 'UST';
                        C::t('forum_threadmod')->update_by_tid_action($tidsarr, array('STK', 'UST', 'EST', 'UES'), array('status' => 0));

                        if(!$sticklevel) {
                            $stampaction = 'SPD';
                        }

                        $stampstatus = 1;

                        break;
                    case 'marrow':
                        $digestlevel = intval($_GET['digestlevel']);
                        // if($digestlevel < 0 || $digestlevel > 3 || $digestlevel > $_G['group']['allowdigestthread']) {
                        //     showmessage('no_privilege_digestthread');
                        // }
                        $expiration = checkexpiration($_GET['expirationdigest'], $operation);
                        $expirationdigest = $digestlevel ? $expirationdigest : 0;

                        C::t('forum_thread')->update($tidsarr, array('digest'=>$digestlevel, 'moderated'=>1), true);

                        foreach($threadlist as $thread) {
                            if($thread['digest'] != $digestlevel) {
                                if($digestlevel == $thread['digest']) continue;
                                $extsql = array();
                                if($digestlevel > 0 && $thread['digest'] == 0) {
                                    $extsql = array('digestposts' => 1);
                                }
                                if($digestlevel == 0 && $thread['digest'] > 0) {
                                    $extsql = array('digestposts' => -1);
                                }
                                if($digestlevel == 0) {
                                    $stampaction = 'SPD';
                                }
                                updatecreditbyaction('digest', $thread['authorid'], $extsql, '', $digestlevel - $thread['digest']);
                            }
                        }

                        $modaction = $digestlevel ? ($expiration ? 'EDI' : 'DIG') : 'UDG';
                        C::t('forum_threadmod')->update_by_tid_action($tidsarr, array('DIG', 'UDI', 'EDI', 'UED'), array('status' => 0));

                        $stampstatus = 2;
                        break;
                    case 'delete':
                        if(!$_G['group']['allowdelpost']) {
                            // showmessage('no_privilege_delpost');
                        }
                        loadcache('threadtableids');
                        $stickmodify = 0;
                        $deleteredirect = $remarkclosed = array();
                        foreach($threadlist as $thread) {
                            if($thread['digest']) {
                                updatecreditbyaction('digest', $thread['authorid'], array('digestposts' => -1), '', -$thread['digest']);
                            }
                            if(in_array($thread['displayorder'], array(2, 3))) {
                                $stickmodify = 1;
                            }
                            if($_G['forum']['status'] == 3 && $thread['closed'] > 1) {
                                $deleteredirect[] = $thread['closed'];
                            }
                            if($thread['isgroup'] == 1 && $thread['closed'] > 1) {
                                $remarkclosed[] = $thread['closed'];
                            }
                        }

                        $modaction = 'DEL';
                        require_once libfile('function/delete');
                        $tids = array_keys($threadlist);
                        if($_G['forum']['recyclebin']) {

                            deletethread($tids, true, true, true);
                            manage_addnotify('verifyrecycle', $modpostsnum);
                        } else {

                            deletethread($tids, true, true);
                            $updatemodlog = FALSE;
                        }

                        $forumstickthreads = $_G['setting']['forumstickthreads'];
                        $forumstickthreads = !empty($forumstickthreads) ? dunserialize($forumstickthreads) : array();
                        $delkeys = array_keys($threadlist);
                        foreach($delkeys as $k) {
                            unset($forumstickthreads[$k]);
                        }
                        C::t('common_setting')->update('forumstickthreads', $forumstickthreads);

                        C::t('forum_forum_threadtable')->delete_none_threads();
                        if(!empty($deleteredirect)) {
                            deletethread($deleteredirect);
                        }
                        if(!empty($remarkclosed)) {
                            C::t('forum_thread')->update($remarkclosed, array('closed'=>0));
                        }

                        if($_G['setting']['globalstick'] && $stickmodify) {
                            require_once libfile('function/cache');
                            updatecache('globalstick');
                        }

                        updateforumcount($_G['fid']);

                        if($_GET['crimerecord']) {
                            include_once libfile('function/member');
                            foreach($threadlist as $thread) {
                                crime('recordaction', $thread['authorid'], 'crime_delpost', lang('forum/misc', 'crime_postreason', array('reason' => $reason, 'tid' => $thread['tid'], 'pid' => 0)));
                            }
                        }
                        break;
                    default:
                        $errorMsg = '错误的动作参数';
                        break;
                }
                
                if ($errorMsg == '') {
                    if($updatemodlog) {
                        if($operation != 'delete') {
                            updatemodlog($moderatetids, $modaction, $expiration);
                        } else {
                            updatemodlog($moderatetids, $modaction, $expiration, 0, $reason);
                        }
                    }

                    updatemodworks($modaction, $modpostsnum);
                    foreach($threadlist as $thread) {
                        modlog($thread, $modaction);
                    }

                    if($sendreasonpm) {
                        $modactioncode = lang('forum/modaction');
                        $modtype = $modaction;
                        $modaction = $modactioncode[$modaction];
                        foreach($threadlist as $thread) {
                            if($operation == 'move') {
                                sendreasonpm($thread, 'reason_move', array('tid' => $thread['tid'], 'subject' => $thread['subject'], 'modaction' => $modaction, 'reason' => $reason, 'tofid' => $toforum['fid'], 'toname' => $toforum['name'], 'from_id' => 0, 'from_idtype' => 'movethread'));
                            } else {
                                sendreasonpm($thread, 'reason_moderate', array('tid' => $thread['tid'], 'subject' => $thread['subject'], 'modaction' => $modaction, 'reason' => $reason, 'from_id' => 0, 'from_idtype' => 'moderate_'.$modtype));
                            }
                        }
                    }

                    if($stampstatus) {
                        set_stamp($stampstatus, $stampaction, $threadlist, $expiration);
                    }

                    $this->getController()->redirect(WebUtils::createUrl_oldVersion('index/returnmobileview'));
                }
            }
        } else if ($type == 'post') {
            // 在DISCUZ_ROOT/source/include/topicadmin/topicadmin_delpost.php基础上进行二次开发
            $resultarray = array();

            $thread = $_G['forum_thread'];
            $topiclist = array($pid);
            $modpostsnum = 1;

            $pids = $posts = $authors = array();

            $posttable = getposttablebytid($_G['tid']);
            foreach(C::t('forum_post')->fetch_all('tid:'.$_G['tid'], $topiclist, false) as $post) {
                if($post['tid'] != $_G['tid']) {
                    continue;
                }
                if($post['first'] == 1) {
                    dheader("location: $_G[siteurl]forum.php?mod=topicadmin&action=moderate&operation=delete&optgroup=3&fid=$_G[fid]&moderate[]=$thread[tid]&inajax=yes".($_GET['infloat'] ? "&infloat=yes&handlekey={$_GET['handlekey']}" : ''));
                } else {
                    $authors[$post['authorid']] = 1;
                    $pids[] = $post['pid'];
                    $posts[] = $post;
                }
            }

            if (!empty($_POST)) {
                $reason = checkreasonpm();

                $uidarray = $puidarray = $auidarray = array();
                $losslessdel = $_G['setting']['losslessdel'] > 0 ? TIMESTAMP - $_G['setting']['losslessdel'] * 86400 : 0;

                if($pids) {
                    require_once libfile('function/delete');
                    if($_G['forum']['recyclebin']) {
                        deletepost($pids, 'pid', true, false, true);
                        manage_addnotify('verifyrecyclepost', $modpostsnum);
                    } else {
                        $logs = array();
                        $ratelog = C::t('forum_ratelog')->fetch_all_by_pid($pids);
                        $rposts = C::t('forum_post')->fetch_all('tid:'.$_G['tid'], $pids, false);
                        foreach(C::t('forum_ratelog')->fetch_all_by_pid($pids) as $rpid => $author) {
                            if($author['score'] > 0) {
                                $rpost = $rposts[$rpid];
                                updatemembercount($rpost['authorid'], array($author['extcredits'] => -$author['score']));
                                $author['score'] = $_G['setting']['extcredits'][$id]['title'].' '.-$author['score'].' '.$_G['setting']['extcredits'][$id]['unit'];
                                $logs[] = dhtmlspecialchars("$_G[timestamp]\t{$_G[member][username]}\t$_G[adminid]\t$rpost[author]\t$author[extcredits]\t$author[score]\t$thread[tid]\t$thread[subject]\t$delpostsubmit");
                            }
                        }
                        if(!empty($logs)) {
                            writelog('ratelog', $logs);
                            unset($logs);
                        }
                        deletepost($pids, 'pid', true);
                    }

                    if($_GET['crimerecord']) {
                        include_once libfile('function/member');

                        foreach($posts as $post) {
                            crime('recordaction', $post['authorid'], 'crime_delpost', lang('forum/misc', 'crime_postreason', array('reason' => $reason, 'tid' => $post['tid'], 'pid' => $post['pid'])));
                        }
                    }
                }

                updatethreadcount($_G['tid'], 1);
                updateforumcount($_G['fid']);

                $_G['forum']['threadcaches'] && deletethreadcaches($thread['tid']);

                $modaction = 'DLP';

                $resultarray = array(
                'redirect'  => "forum.php?mod=viewthread&tid=$_G[tid]&page=$_GET[page]",
                'reasonpm'  => ($sendreasonpm ? array('data' => $posts, 'var' => 'post', 'item' => 'reason_delete_post', 'notictype' => 'post') : array()),
                'reasonvar' => array('tid' => $thread['tid'], 'subject' => $thread['subject'], 'modaction' => $modaction, 'reason' => $reason),
                'modtids'   => 0,
                'modlog'    => $thread
                );
            }

            // 在DISCUZ_ROOT/source/module/forum/forum_topicadmin.php基础上进行二次开发
            if($resultarray) {

                if($resultarray['modtids']) {
                    updatemodlog($resultarray['modtids'], $modaction, $resultarray['expiration']);
                }

                updatemodworks($modaction, $modpostsnum);
                if(is_array($resultarray['modlog'])) {
                    if(isset($resultarray['modlog']['tid'])) {
                        modlog($resultarray['modlog'], $modaction);
                    } else {
                        foreach($resultarray['modlog'] as $thread) {
                            modlog($thread, $modaction);
                        }
                    }
                }

                if($resultarray['reasonpm']) {
                    $modactioncode = lang('forum/modaction');
                    $modaction = $modactioncode[$modaction];
                    foreach($resultarray['reasonpm']['data'] as $var) {
                        sendreasonpm($var, $resultarray['reasonpm']['item'], $resultarray['reasonvar'], $resultarray['reasonpm']['notictype']);
                    }
                }

                // showmessage((isset($resultarray['message']) ? $resultarray['message'] : 'admin_succeed'), $resultarray['redirect']);
                $this->getController()->redirect(WebUtils::createUrl_oldVersion('index/returnmobileview'));
            }

        }

        $this->getController()->renderPartial('topicAdmin', array(
            'formUrl' => WebUtils::createUrl_oldVersion('forum/topicadminview', array('fid' => $fid, 'tid' => $tid, 'pid' => $pid, 'act' => $act, 'type' => $type)),
            'errorMsg' => $errorMsg,
            'action' => $act,
            '_G' => $_G,
            'stickcheck' => $stickcheck,
            'digestcheck' => $digestcheck,
        ));
    }
}

function checkexpiration($expiration, $operation) {
    global $_G;
    if(!empty($expiration) && in_array($operation, array('recommend', 'stick', 'digest', 'highlight', 'close'))) {
        $expiration = strtotime($expiration) - $_G['setting']['timeoffset'] * 3600 + date('Z');
        if(dgmdate($expiration, 'Ymd') <= dgmdate(TIMESTAMP, 'Ymd') || ($expiration > TIMESTAMP + 86400 * 180)) {
            showmessage('admin_expiration_invalid', '', array('min'=>dgmdate(TIMESTAMP, 'Y-m-d'), 'max'=>dgmdate(TIMESTAMP + 86400 * 180, 'Y-m-d')));
        }
    } else {
        $expiration = 0;
    }
    return $expiration;
}

function set_stamp($typeid, $stampaction, &$threadlist, $expiration) {
    global $_G;
    $moderatetids = array_keys($threadlist);
    if(empty($threadlist)) {
        return false;
    }
    if(array_key_exists($typeid, $_G['cache']['stamptypeid'])) {
        if($stampaction == 'SPD') {
            C::t('forum_thread')->update($moderatetids, array('stamp'=>-1), true);
        } else {
            C::t('forum_thread')->update($moderatetids, array('stamp'=>$_G['cache']['stamptypeid'][$typeid]), true);
        }
        !empty($moderatetids) && updatemodlog($moderatetids, $stampaction, $expiration, 0, '', $_G['cache']['stamptypeid'][$typeid]);
    }
}