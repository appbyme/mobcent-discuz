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

// Mobcent::setErrors();
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

        $_GET['topiclist'] = array($_GET['pid']);
        // 在DISCUZ_ROOT/source/module/forum/forum_topicadmin.php基础上进行二次开发
        $_GET['topiclist'] = !empty($_GET['topiclist']) ? (is_array($_GET['topiclist']) ? array_unique($_GET['topiclist']) : $_GET['topiclist']) : array();
        loadcache(array('modreasons', 'stamptypeid', 'threadtableids'));

        require_once libfile('function/post');
        require_once libfile('function/misc');

        $modpostsnum = 0;
        $resultarray = $thread = array();

        if(($_G['group']['reasonpm'] == 2 || $_G['group']['reasonpm'] == 3) || !empty($_GET['sendreasonpm'])) {
            $forumname = strip_tags($_G['forum']['name']);
            $sendreasonpm = 1;
        } else {
            $sendreasonpm = 0;
        }

        if ($type == 'topic') {
            if ($act == 'band') {
                $resultarray = $this->_topicAdmin_band($fid, $tid, $pid, $act, $type, array(
                    'sendreasonpm' => $sendreasonpm,
                    'thread' => $_G['forum_thread'],
                ));
            } else {
                // 在DISCUZ_ROOT/source/include/topicadmin/topicadmin_moderate.php基础上进行二次开发
                $thread = $_G['forum_thread'];

                $threadlist[$thread['tid']] = $thread;

                $modpostsnum = count($threadlist);

                $stickcheck  = $closecheck = $digestcheck = array('', '', '', '', '');

                empty($threadlist[$_G['tid']]['displayorder']) ? $stickcheck[0] ='selected="selected"' : $stickcheck[$threadlist[$_G['tid']]['displayorder']] = 'selected="selected"';
                empty($threadlist[$_G['tid']]['digest']) ? $digestcheck[0] = 'selected="selected"' : $digestcheck[$threadlist[$_G['tid']]['digest']] = 'selected="selected"';

                empty($threadlist[$_G['tid']]['closed']) ? $closecheck[0] = 'checked="checked"' : $closecheck[1] = 'checked="checked"';

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
                            if($sticklevel < 0 || $sticklevel > 3 || $sticklevel > $_G['group']['allowstickthread']) {
                                // showmessage('no_privilege_stickthread');
                                $this->_exitWithHtmlAlert('no_privilege_stickthread');
                            }
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
                            if($digestlevel < 0 || $digestlevel > 3 || $digestlevel > $_G['group']['allowdigestthread']) {
                                // showmessage('no_privilege_digestthread');
                                $this->_exitWithHtmlAlert('no_privilege_digestthread');
                            }
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
                                $this->_exitWithHtmlAlert('no_privilege_delpost');
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
                        case 'close':
                            if(!$_G['group']['allowclosethread']) {
                                $this->_exitWithHtmlAlert('no_privilege_closethread');
                            }
                            $expiration = checkexpiration($_GET['expirationclose'], $operation);
                            $modaction = $expiration ? 'ECL' : 'CLS';

                            C::t('forum_thread')->update($tidsarr, array('closed'=>1, 'moderated'=>1), true);
                            C::t('forum_threadmod')->update_by_tid_action($tidsarr, array('CLS','OPN','ECL','UCL','EOP','UEO'), array('status' => 0));
                            break;
                        case 'open':
                            if(!$_G['group']['allowclosethread']) {
                                $this->_exitWithHtmlAlert('no_privilege_openthread');
                            }
                            $expiration = checkexpiration($_GET['expirationclose'], $operation);
                            $modaction = $expiration ? 'EOP' : 'OPN';

                            C::t('forum_thread')->update($tidsarr, array('closed'=>0, 'moderated'=>1), true);
                            C::t('forum_threadmod')->update_by_tid_action($tidsarr, array('CLS','OPN','ECL','UCL','EOP','UEO'), array('status' => 0));
                            break;
                        case 'move':
                            if(!$_G['group']['allowmovethread']) {
                                $this->_exitWithHtmlAlert('no_privilege_movethread');
                            }
                            $moveto = $_GET['moveto'];
                            $toforum = C::t('forum_forum')->fetch_info_by_fid($moveto);
                            if(!$toforum || ($_G['adminid'] != 1 && $toforum['status'] != 1) || $toforum['type'] == 'group') {
                                // showmessage('admin_move_invalid');
                                $this->_exitWithHtmlAlert('admin_move_invalid');
                            } elseif($_G['fid'] == $toforum['fid']) {
                                continue;
                            } else {
                                $moveto = $toforum['fid'];
                                $modnewthreads = (!$_G['group']['allowdirectpost'] || $_G['group']['allowdirectpost'] == 1) && $toforum['modnewposts'] ? 1 : 0;
                                $modnewreplies = (!$_G['group']['allowdirectpost'] || $_G['group']['allowdirectpost'] == 2) && $toforum['modnewposts'] ? 1 : 0;
                                if($modnewthreads || $modnewreplies) {
                                    // showmessage('admin_move_have_mod');
                                    $this->_exitWithHtmlAlert('admin_move_have_mod');
                                }
                            }

                            if($_G['adminid'] == 3) {
                                $priv = C::t('forum_forumfield')->check_moderator_for_uid($moveto, $_G['uid'], $_G['member']['accessmasks']);
                                if((($priv['postperm'] && !in_array($_G['groupid'], explode("\t", $priv['postperm']))) || ($_G['member']['accessmasks'] && ($priv['allowview'] || $priv['allowreply'] || $priv['allowgetattach'] || $priv['allowpostattach']) && !$priv['allowpost'])) && !$priv['istargetmod']) {
                                    // showmessage('admin_move_nopermission');
                                    $this->_exitWithHtmlAlert('admin_move_nopermission');
                                }
                            }

                            $moderate = array();
                            $stickmodify = 0;
                            $toforumallowspecial = array(
                                1 => $toforum['allowpostspecial'] & 1,
                                2 => $toforum['allowpostspecial'] & 2,
                                3 => isset($_G['setting']['extcredits'][$_G['setting']['creditstransextra'][2]]) && ($toforum['allowpostspecial'] & 4),
                                4 => $toforum['allowpostspecial'] & 8,
                                5 => $toforum['allowpostspecial'] & 16,
                                127 => $_G['setting']['threadplugins'] ? dunserialize($toforum['threadplugin']) : array(),
                            );
                            foreach($threadlist as $tid => $thread) {
                                $allowmove = 0;
                                if(!$thread['special']) {
                                    $allowmove = 1;
                                } else {
                                    if($thread['special'] != 127) {
                                        $allowmove = $toforum['allowpostspecial'] ? $toforumallowspecial[$thread['special']] : 0;
                                    } else {
                                        if($toforumallowspecial[127]) {
                                            $posttable = getposttablebytid($thread['tid']);
                                            $message = C::t('forum_post')->fetch_threadpost_by_tid_invisible($thread['tid']);
                                            $message = $message['message'];
                                            $sppos = strrpos($message, chr(0).chr(0).chr(0));
                                            $specialextra = substr($message, $sppos + 3);
                                            $allowmove = in_array($specialextra, $toforumallowspecial[127]);
                                        } else {
                                            $allowmove = 0;
                                        }
                                    }
                                }

                                if($allowmove) {
                                    $moderate[] = $tid;
                                    if(in_array($thread['displayorder'], array(2, 3))) {
                                        $stickmodify = 1;
                                    }
                                    if($_GET['appbyme_movetype'] == 'redirect') {
                                    // if($_GET['type'] == 'redirect') {

                                        $insertdata = array(
                                                'fid' => $thread['fid'],
                                                'readperm' => $thread['readperm'],
                                                'author' => $thread['author'],
                                                'authorid' => $thread['authorid'],
                                                'subject' => $thread['subject'],
                                                'dateline' => $thread['dateline'],
                                                'lastpost' => $thread['dblastpost'],
                                                'lastposter' => $thread['lastposter'],
                                                'views' => 0,
                                                'replies' => 0,
                                                'displayorder' => 0,
                                                'digest' => 0,
                                                'closed' => $thread['tid'],
                                                'special' => 0,
                                                'attachment' => 0,
                                                'typeid' => $_GET['threadtypeid']
                                            );
                                        $newtid = C::t('forum_thread')->insert($insertdata, true);
                                        if($newtid) {
                                            C::t('forum_threadclosed')->insert(array('tid' => $thread['tid'], 'redirect' => $newtid), true, true);
                                        }
                                    }
                                }
                            }

                            if(!$moderatetids = implode(',', $moderate)) {
                                showmessage('admin_moderate_invalid');
                            }
                            $fieldarr = array(
                                    'fid' => $moveto,
                                    'isgroup' => 0,
                                    'typeid' => $_GET['threadtypeid'],
                                    'moderated' => 1
                                );
                            if($_G['adminid'] == 3) {
                                $fieldarr['displayorder'] = 0;
                            }
                            C::t('forum_thread')->update($tidsarr, $fieldarr, true);
                            C::t('forum_forumrecommend')->update($tidsarr, array('fid' => $moveto));
                            loadcache('posttableids');
                            $posttableids = $_G['cache']['posttableids'] ? $_G['cache']['posttableids'] : array('0');
                            foreach($posttableids as $id) {
                                C::t('forum_post')->update_by_tid($id, $tidsarr, array('fid' => $moveto));
                            }
                            $typeoptionvars = C::t('forum_typeoptionvar')->fetch_all_by_tid_optionid($tidsarr);
                            foreach($typeoptionvars as $typeoptionvar) {
                                C::t('forum_typeoptionvar')->update_by_tid($typeoptionvar['tid'], array('fid' => $moveto));
                                C::t('forum_optionvalue')->update($typeoptionvar['sortid'], $typeoptionvar['tid'], $_G['fid'], "fid='$moveto'");
                            }

                            if($_G['setting']['globalstick'] && $stickmodify) {
                                require_once libfile('function/cache');
                                updatecache('globalstick');
                            }
                            $modaction = 'MOV';
                            $_G['toforum'] = $toforum;
                            updateforumcount($moveto);
                            updateforumcount($_G['fid']);
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
                } else {
                    if ($act == 'move') {
                        require_once libfile('function/forumlist');
                        $forumselect = forumselect(FALSE, 0, $threadlist[$_G['tid']]['fid'], $_G['adminid']==1 ? TRUE : FALSE);
                    }
                }
            }
        } else if ($type == 'post') {
            if ($act == 'band') {
                $resultarray = $this->_topicAdmin_band($fid, $tid, $pid, $act, $type, array(
                    'sendreasonpm' => $sendreasonpm,
                    'thread' => $_G['forum_thread'],
                ));
            } else {
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
            }
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

        $this->getController()->renderPartial('topicAdmin', array(
            'formUrl' => WebUtils::createUrl_oldVersion('forum/topicadminview', array('fid' => $fid, 'tid' => $tid, 'pid' => $pid, 'act' => $act, 'type' => $type)),
            'errorMsg' => $errorMsg,
            'action' => $act,
            '_G' => $_G,
            'stickcheck' => $stickcheck,
            'digestcheck' => $digestcheck,
            'closecheck' => $closecheck,
            'forumselect' => WebUtils::u($forumselect),
        ));
    }

    private function _topicAdmin_band($fid, $tid, $pid, $act, $type, $params=array())
    {
        extract($params);
        global $_G;
        // 在DISCUZ_ROOT/source/include/topicadmin/topicadmin_banpost.php基础上进行二次开发
        if(!$_G['group']['allowbanpost']) {
            $this->_exitWithHtmlAlert('no_privilege_banpost');
        }
        $topiclist = $_GET['topiclist'];
        $modpostsnum = count($topiclist);
        if(!($banpids = dimplode($topiclist))) {
            $this->_exitWithHtmlAlert('admin_banpost_invalid');
        } elseif(!$_G['group']['allowbanpost'] || !$_G['tid']) {
            $this->_exitWithHtmlAlert('admin_nopermission');
        }

        $posts = $authors = array();
        $banstatus = 0;
        foreach(C::t('forum_post')->fetch_all('tid:'.$_G['tid'], $topiclist) as $post) {
            if($post['tid'] != $_G['tid']) {
                continue;
            }
            $banstatus = ($post['status'] & 1) || $banstatus;
            $authors[$post['authorid']] = 1;
            $posts[] = $post;
        }

        $authorcount = count(array_keys($authors));

        if(!empty($_POST)) {
            $banned = intval($_GET['banned']);
            $modaction = $banned ? 'BNP' : 'UBN';

            $reason = checkreasonpm();

            include_once libfile('function/member');

            $pids = $comma = '';

            foreach($posts as $k => $post) {
                if($banned) {
                    C::t('forum_postcomment')->delete_by_rpid($post['pid']);
                    C::t('forum_post')->increase_status_by_pid('tid:'.$_G['tid'], $post['pid'], 1, '|', true);
                    crime('recordaction', $post['authorid'], 'crime_banpost', lang('forum/misc', 'crime_postreason', array('reason' => $reason, 'tid' => $_G['tid'], 'pid' => $post['pid'])));

                } else {
                    C::t('forum_post')->increase_status_by_pid('tid:'.$_G['tid'], $post['pid'], 1, '^', true);
                }
                $pids .= $comma.$post['pid'];
                $comma = ',';
            }

            $resultarray = array(
                'redirect'  => "forum.php?mod=viewthread&tid=$_G[tid]&page=$page",
                'reasonpm'  => ($sendreasonpm ? array('data' => $posts, 'var' => 'post', 'item' => 'reason_ban_post', 'notictype' => 'post') : array()),
                'reasonvar' => array('tid' => $thread['tid'], 'subject' => $thread['subject'], 'modaction' => $modaction, 'reason' => $reason),
                'modtids'   => 0,
                'modlog'    => $thread
            );
            return $resultarray;
        }

        $banid = $checkunban = $checkban = '';
        foreach($topiclist as $id) {
            $banid .= '<input type="hidden" name="topiclist[]" value="'.$id.'" />';
        }

        $banstatus ? $checkunban = 'checked="checked"' : $checkban = 'checked="checked"';

        if($modpostsnum == 1 || $authorcount == 1) {
            include_once libfile('function/member');
            $crimenum = crime('getcount', $posts[0]['authorid'], 'crime_banpost');
            $crimeauthor = $posts[0]['author'];
        }

        $this->getController()->renderPartial('topicAdmin', array(
            'formUrl' => WebUtils::createUrl_oldVersion('forum/topicadminview', array('fid' => $fid, 'tid' => $tid, 'pid' => $pid, 'act' => $act, 'type' => $type)),
            'errorMsg' => $errorMsg,
            'action' => $act,
            '_G' => $_G,
            'banid' => $banid,
            'checkunban' => $checkunban,
            'checkban' => $checkban,
            'modpostsnum' => $modpostsnum,
            'crimenum' => $crimenum,
            'crimeauthor' => $crimeauthor,
        ));
        exit;
    }

    private function _exitWithHtmlAlert($message)
    {
        $message = lang('message', $message);
        $location = WebUtils::createUrl_oldVersion('index/returnmobileview');
        $htmlString = sprintf('
            <script>
                alert("%s");
                location.href = "%s";
            </script>',
            $message, $location
        );
        echo $htmlString;
        exit;
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
