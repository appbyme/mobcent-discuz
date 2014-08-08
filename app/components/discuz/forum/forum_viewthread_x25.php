<?php

/**
 * copy from discuz DISCUZ_ROOT/forum.php
 * DISCUZ_ROOT/source/module/forum/forum_viewthread.php
 * and do some modification
 *
 * @author Xie Jianping <xiejianping@mobcent.com>
 */

global $_G;

require_once libfile('function/discuzcode');

function viewthread_updateviews($tableid) {
    global $_G;

    if(!$_G['setting']['preventrefresh'] || $_G['cookie']['viewid'] != 'tid_'.$_G['tid']) {
        if(!$tableid && $_G['setting']['optimizeviews']) {
            if($_G['forum_thread']['addviews']) {
                if($_G['forum_thread']['addviews'] < 100) {
                    C::t('forum_threadaddviews')->update_by_tid($_G['tid']);
                } else {
                    if(!discuz_process::islocked('update_thread_view')) {
                        $row = C::t('forum_threadaddviews')->fetch($_G['tid']);
                        C::t('forum_threadaddviews')->update($_G['tid'], array('addviews' => 0));
                        C::t('forum_thread')->increase($_G['tid'], array('views' => $row['addviews']+1), true);
                        discuz_process::unlock('update_thread_view');
                    }
                }
            } else {
                C::t('forum_threadaddviews')->insert(array('tid' => $_G['tid'], 'addviews' => 1), false, true);
            }
        } else {
            C::t('forum_thread')->increase($_G['tid'], array('views' => 1), true, $tableid);
        }
    }
    dsetcookie('viewid', 'tid_'.$_G['tid']);
}

function viewthread_procpost($post, $lastvisit, $ordertype, $maxposition = 0) {
    global $_G, $rushreply;

    if(!$_G['forum_newpostanchor'] && $post['dateline'] > $lastvisit) {
        $post['newpostanchor'] = '<a name="newpost"></a>';
        $_G['forum_newpostanchor'] = 1;
    } else {
        $post['newpostanchor'] = '';
    }

    $post['lastpostanchor'] = ($ordertype != 1 && $_G['forum_numpost'] == $_G['forum_thread']['replies']) || ($ordertype == 1 && $_G['forum_numpost'] == $_G['forum_thread']['replies'] + 2) ? '<a name="lastpost"></a>' : '';

    if($_G['forum_pagebydesc']) {
        if($ordertype != 1) {
            $post['number'] = $_G['forum_numpost'] + $_G['forum_ppp2']--;
        } else {
            $post['number'] = $post['first'] == 1 ? 1 : ($_G['forum_numpost'] - 1) - $_G['forum_ppp2']--;
        }
    } else {
        if($ordertype != 1) {
            $post['number'] = ++$_G['forum_numpost'];
        } else {
            $post['number'] = $post['first'] == 1 ? 1 : --$_G['forum_numpost'];
            $post['number'] = $post['number'] - 1;
        }
    }

    if($maxposition) {
        $post['number'] = $post['position'];
    }
    $_G['forum_postcount']++;

    $post['dbdateline'] = $post['dateline'];
    $post['dateline'] = dgmdate($post['dateline'], 'u', '9999', getglobal('setting/dateformat').' H:i:s');
    $post['groupid'] = $_G['cache']['usergroups'][$post['groupid']] ? $post['groupid'] : 7;

    if($post['username']) {

        $_G['forum_onlineauthors'][$post['authorid']] = 0;
        $post['usernameenc'] = rawurlencode($post['username']);
        $post['readaccess'] = $_G['cache']['usergroups'][$post['groupid']]['readaccess'];
        if($_G['cache']['usergroups'][$post['groupid']]['userstatusby'] == 1) {
            $post['authortitle'] = $_G['cache']['usergroups'][$post['groupid']]['grouptitle'];
            $post['stars'] = $_G['cache']['usergroups'][$post['groupid']]['stars'];
        }
        $post['upgradecredit'] = false;
        if($_G['cache']['usergroups'][$post['groupid']]['type'] == 'member' && $_G['cache']['usergroups'][$post['groupid']]['creditslower'] != 999999999) {
            $post['upgradecredit'] = $_G['cache']['usergroups'][$post['groupid']]['creditslower'] - $post['credits'];
        }

        $post['taobaoas'] = addslashes($post['taobao']);
        $post['regdate'] = dgmdate($post['regdate'], 'd');
        $post['lastdate'] = dgmdate($post['lastvisit'], 'd');

        $post['authoras'] = !$post['anonymous'] ? ' '.addslashes($post['author']) : '';

        if($post['medals']) {
            loadcache('medals');
            foreach($post['medals'] = explode("\t", $post['medals']) as $key => $medalid) {
                list($medalid, $medalexpiration) = explode("|", $medalid);
                if(isset($_G['cache']['medals'][$medalid]) && (!$medalexpiration || $medalexpiration > TIMESTAMP)) {
                    $post['medals'][$key] = $_G['cache']['medals'][$medalid];
                    $post['medals'][$key]['medalid'] = $medalid;
                    $_G['medal_list'][$medalid] = $_G['cache']['medals'][$medalid];
                } else {
                    unset($post['medals'][$key]);
                }
            }
        }

        $post['avatar'] = avatar($post['authorid']);
        $post['groupicon'] = $post['avatar'] ? g_icon($post['groupid'], 1) : '';
        $post['banned'] = $post['status'] & 1;
        $post['warned'] = ($post['status'] & 2) >> 1;

    } else {
        if(!$post['authorid']) {
            $post['useip'] = substr($post['useip'], 0, strrpos($post['useip'], '.')).'.x';
        }
    }
    $post['attachments'] = array();
    $post['imagelist'] = $post['attachlist'] = '';

    if($post['attachment']) {
        if($_G['group']['allowgetattach'] || $_G['group']['allowgetimage']) {
            $_G['forum_attachpids'][] = $post['pid'];
            $post['attachment'] = 0;
            if(preg_match_all("/\[attach\](\d+)\[\/attach\]/i", $post['message'], $matchaids)) {
                $_G['forum_attachtags'][$post['pid']] = $matchaids[1];
            }
        } else {
            $post['message'] = preg_replace("/\[attach\](\d+)\[\/attach\]/i", '', $post['message']);
        }
    }

    if($_G['setting']['ratelogrecord'] && $post['ratetimes']) {
        $_G['forum_cachepid'][$post['pid']] = $post['pid'];
    }
    if($_G['setting']['commentnumber'] && ($post['first'] && $_G['setting']['commentfirstpost'] || !$post['first']) && $post['comment']) {
        $_G['forum_cachepid'][$post['pid']] = $post['pid'];
    }
    $post['allowcomment'] = $_G['setting']['commentnumber'] && in_array(1, $_G['setting']['allowpostcomment']) && ($_G['setting']['commentpostself'] || $post['authorid'] != $_G['uid']) &&
        ($post['first'] && $_G['setting']['commentfirstpost'] && in_array($_G['group']['allowcommentpost'], array(1, 3)) ||
        (!$post['first'] && in_array($_G['group']['allowcommentpost'], array(2, 3))));
    $forum_allowbbcode = $_G['forum']['allowbbcode'] ? -$post['groupid'] : 0;
    $post['signature'] = $post['usesig'] ? ($_G['setting']['sigviewcond'] ? (strlen($post['message']) > $_G['setting']['sigviewcond'] ? $post['signature'] : '') : $post['signature']) : '';
    if(!defined('IN_ARCHIVER')) {
        $post['message'] = discuzcode($post['message'], $post['smileyoff'], $post['bbcodeoff'], $post['htmlon'] & 1, $_G['forum']['allowsmilies'], $forum_allowbbcode, ($_G['forum']['allowimgcode'] && $_G['setting']['showimages'] ? 1 : 0), $_G['forum']['allowhtml'], ($_G['forum']['jammer'] && $post['authorid'] != $_G['uid'] ? 1 : 0), 0, $post['authorid'], $_G['cache']['usergroups'][$post['groupid']]['allowmediacode'] && $_G['forum']['allowmediacode'], $post['pid'], $_G['setting']['lazyload'], $post['dbdateline']);
        if($post['first']) {
            $_G['relatedlinks'] = '';
            $relatedtype = !$_G['forum_thread']['isgroup'] ? 'forum' : 'group';
            if(!$_G['setting']['relatedlinkstatus']) {
                $_G['relatedlinks'] = get_related_link($relatedtype);
            } else {
                $post['message'] = parse_related_link($post['message'], $relatedtype);
            }

        }
    }
    $_G['forum_firstpid'] = intval($_G['forum_firstpid']);
    $post['custominfo'] = viewthread_custominfo($post);
    $post['mobiletype'] = getstatus($post['status'], 4) ? base_convert(getstatus($post['status'], 10).getstatus($post['status'], 9).getstatus($post['status'], 8), 2, 10) : 0;
    return $post;
}

function viewthread_loadcache() {
    global $_G;
    $_G['forum']['livedays'] = ceil((TIMESTAMP - $_G['forum']['dateline']) / 86400);
    $_G['forum']['lastpostdays'] = ceil((TIMESTAMP - $_G['forum']['lastthreadpost']) / 86400);
    $threadcachemark = 100 - (
    $_G['forum']['displayorder'] * 15 +
    $_G['thread']['digest'] * 10 +
    min($_G['thread']['views'] / max($_G['forum']['livedays'], 10) * 2, 50) +
    max(-10, (15 - $_G['forum']['lastpostdays'])) +
    min($_G['thread']['replies'] / $_G['setting']['postperpage'] * 1.5, 15));
    if($threadcachemark < $_G['forum']['threadcaches']) {

        $threadcache = getcacheinfo($_G['tid']);

        if(TIMESTAMP - $threadcache['filemtime'] > $_G['setting']['cachethreadlife']) {
            @unlink($threadcache['filename']);
            define('CACHE_FILE', $threadcache['filename']);
        } else {
            readfile($threadcache['filename']);

            viewthread_updateviews($_G['forum_thread']['threadtableid']);
            $_G['setting']['debug'] && debuginfo();
            $_G['setting']['debug'] ? die('<script type="text/javascript">document.getElementById("debuginfo").innerHTML = " '.($_G['setting']['debug'] ? 'Updated at '.gmdate("H:i:s", $threadcache['filemtime'] + 3600 * 8).', Processed in '.$debuginfo['time'].' second(s), '.$debuginfo['queries'].' Queries'.($_G['gzipcompress'] ? ', Gzip enabled' : '') : '').'";</script>') : die();
        }
    }
}

function viewthread_lastmod(&$thread) {
    global $_G;
    if(!$thread['moderated']) {
        return array();
    }
    $lastmod = array();
    $lastlog = C::t('forum_threadmod')->fetch_by_tid($thread['tid']);
    if($lastlog) {
        $lastmod = array(
                    'moduid' => $lastlog['uid'],
                    'modusername' => $lastlog['username'],
                    'moddateline' => $lastlog['dateline'],
                    'modaction' => $lastlog['action'],
                    'magicid' => $lastlog['magicid'],
                    'stamp' => $lastlog['stamp'],
                    'reason' => $lastlog['reason']
                );
    }
    if($lastmod) {
        $modactioncode = lang('forum/modaction');
        $lastmod['modusername'] = $lastmod['modusername'] ? $lastmod['modusername'] : 'System';
        $lastmod['moddateline'] = dgmdate($lastmod['moddateline'], 'u');
        $lastmod['modactiontype'] = $lastmod['modaction'];
        if($modactioncode[$lastmod['modaction']]) {
            $lastmod['modaction'] = $modactioncode[$lastmod['modaction']].($lastmod['modaction'] != 'SPA' ? '' : ' '.$_G['cache']['stamps'][$lastmod['stamp']]['text']);
        } elseif(substr($lastmod['modaction'], 0, 1) == 'L' && preg_match('/L(\d\d)/', $lastmod['modaction'], $a)) {
            $lastmod['modaction'] = $modactioncode['SLA'].' '.$_G['cache']['stamps'][intval($a[1])]['text'];
        } else {
            $lastmod['modaction'] = '';
        }
        if($lastmod['magicid']) {
            loadcache('magics');
            $lastmod['magicname'] = $_G['cache']['magics'][$lastmod['magicid']]['name'];
        }
    } else {
        C::t('forum_thread')->update($thread['tid'], array('moderated' => 0), false, false, $thread['threadtableid']);
        $thread['moderated'] = 0;
    }
    return $lastmod;
}

function viewthread_custominfo($post) {
    global $_G;

    $types = array('left', 'menu');
    foreach($types as $type) {
        if(!is_array($_G['cache']['custominfo']['setting'][$type])) {
            continue;
        }
        $data = '';
        foreach($_G['cache']['custominfo']['setting'][$type] as $key => $order) {
            $v = '';
            if(substr($key, 0, 10) == 'extcredits') {
                $i = substr($key, 10);
                $extcredit = $_G['setting']['extcredits'][$i];
                if($extcredit) {
                    $v = '<dt>'.($extcredit['img'] ? $extcredit['img'].' ' : '').$extcredit['title'].'</dt><dd>'.$post['extcredits'.$i].' '.$extcredit['unit'].'</dd>';
                }
            } elseif(substr($key, 0, 6) == 'field_') {
                $field = substr($key, 6);
                if(!empty($post['privacy']['profile'][$field])) {
                    continue;
                }
                require_once libfile('function/profile');
                $v = profile_show($field, $post);
                if($v) {
                    $v = '<dt>'.$_G['cache']['custominfo']['profile'][$key][0].'</dt><dd title="'.dhtmlspecialchars(strip_tags($v)).'">'.$v.'</dd>';
                }
            } elseif($key == 'creditinfo') {
                $v = '<dt>'.lang('space', 'viewthread_userinfo_buyercredit').'</dt><dd><a href="home.php?mod=space&uid='.$post['uid'].'&do=trade&view=eccredit#buyercredit" target="_blank" class="vm"><img src="'.STATICURL.'image/traderank/seller/'.countlevel($post['buyercredit']).'.gif" /></a></dd>';
                $v .= '<dt>'.lang('space', 'viewthread_userinfo_sellercredit').'</dt><dd><a href="home.php?mod=space&uid='.$post['uid'].'&do=trade&view=eccredit#sellercredit" target="_blank" class="vm"><img src="'.STATICURL.'image/traderank/seller/'.countlevel($post['sellercredit']).'.gif" /></a></dd>';
            } else {
                switch($key) {
                    case 'uid': $v = $post['uid'];break;
                    case 'posts': $v = '<a href="home.php?mod=space&uid='.$post['uid'].'&do=thread&type=reply&view=me&from=space" target="_blank" class="xi2">'.$post['posts'].'</a>';break;
                    case 'threads': $v = '<a href="home.php?mod=space&uid='.$post['uid'].'&do=thread&type=thread&view=me&from=space" target="_blank" class="xi2">'.$post['threads'].'</a>';break;
                    case 'doings': $v = '<a href="home.php?mod=space&uid='.$post['uid'].'&do=doing&view=me&from=space" target="_blank" class="xi2">'.$post['doings'].'</a>';break;
                    case 'blogs': $v = '<a href="home.php?mod=space&uid='.$post['uid'].'&do=blog&view=me&from=space" target="_blank" class="xi2">'.$post['blogs'].'</a>';break;
                    case 'albums': $v = '<a href="home.php?mod=space&uid='.$post['uid'].'&do=album&view=me&from=space" target="_blank" class="xi2">'.$post['albums'].'</a>';break;
                    case 'sharings': $v = '<a href="home.php?mod=space&uid='.$post['uid'].'&do=share&view=me&from=space" target="_blank" class="xi2">'.$post['sharings'].'</a>';break;
                    case 'friends': $v = '<a href="home.php?mod=space&uid='.$post['uid'].'&do=friend&view=me&from=space" target="_blank" class="xi2">'.$post['friends'].'</a>';break;
                    case 'follower': $v = '<a href="home.php?mod=follow&do=follower&uid='.$post['uid'].'" target="_blank" class="xi2">'.$post['follower'].'</a>';break;
                    case 'following': $v = '<a href="home.php?mod=follow&do=following&uid='.$post['uid'].'" target="_blank" class="xi2">'.$post['following'].'</a>';break;
                    case 'digest': $v = $post['digestposts'];break;
                    case 'credits': $v = $post['credits'];break;
                    case 'readperm': $v = $post['readaccess'];break;
                    case 'regtime': $v = $post['regdate'];break;
                    case 'lastdate': $v = $post['lastdate'];break;
                    case 'oltime': $v = $post['oltime'].' '.lang('space', 'viewthread_userinfo_hour');break;
                }
                if($v !== '') {
                    $v = '<dt>'.lang('space', 'viewthread_userinfo_'.$key).'</dt><dd>'.$v.'</dd>';
                }
            }
            $data .= $v;
        }
        $return[$type] = $data;
    }
    return $return;
}
function countlevel($usercredit) {
    global $_G;

    $rank = 0;
    if($usercredit){
        foreach($_G['setting']['ec_credit']['rank'] AS $level => $credit) {
            if($usercredit <= $credit) {
                $rank = $level;
                break;
            }
        }
    }
    return $rank;
}
function remaintime($time) {
    $days = intval($time / 86400);
    $time -= $days * 86400;
    $hours = intval($time / 3600);
    $time -= $hours * 3600;
    $minutes = intval($time / 60);
    $time -= $minutes * 60;
    $seconds = $time;
    return array((int)$days, (int)$hours, (int)$minutes, (int)$seconds);
}

function getrelateitem($tagarray, $tid, $relatenum, $relatetime, $relatecache = '', $type = 'tid') {
    $tagidarray = $relatearray = $relateitem = array();
    $updatecache = 0;
    $limit = $relatenum;
    if(!$limit) {
        return '';
    }
    foreach($tagarray as $var) {
        $tagidarray[] = $var['0'];
    }
    if(!$tagidarray) {
        return '';
    }
    if(empty($relatecache)) {
        $thread = C::t('forum_thread')->fetch($tid);
        $relatecache = $thread['relatebytag'];
    }
    if($relatecache) {
        $relatecache = explode("\t", $relatecache);
        if(TIMESTAMP > $relatecache[0] + $relatetime * 60) {
            $updatecache = 1;
        } else {
            if(!empty($relatecache[1])) {
                $relatearray = explode(',', $relatecache[1]);
            }
        }
    } else {
        $updatecache = 1;
    }
    if($updatecache) {
        $query = C::t('common_tagitem')->select($tagidarray, $tid, $type, '', '', $limit, 0, '<>');
        foreach($query as $result) {
            if($result['itemid']) {
                $relatearray[] = $result['itemid'];
            }
        }
        if($relatearray) {
            $relatebytag = implode(',', $relatearray);
        }
        C::t('forum_thread')->update($tid, array('relatebytag'=>TIMESTAMP."\t".$relatebytag));
    }


    if(!empty($relatearray)) {
        foreach(C::t('forum_thread')->fetch_all_by_tid($relatearray) as $result) {
            if($result['displayorder'] >= 0) {
                $relateitem[] = $result;
            }
        }
    }
    return $relateitem;
}

function rushreply_rule () {
    global $rushresult;
    if(!empty($rushresult['rewardfloor'])) {
        $rushresult['rewardfloor'] = preg_replace('/\*+/', '*', $rushresult['rewardfloor']);
        $rewardfloorarr = explode(',', $rushresult['rewardfloor']);
        if($rewardfloorarr) {
            foreach($rewardfloorarr as $var) {
                $var = trim($var);
                if(strlen($var) > 1) {
                    $var = str_replace('*', '[^,]?[\d]*', $var);
                } else {
                    $var = str_replace('*', '\d+', $var);
                }
                $preg[] = "(,$var,)";
            }
            $preg_str = "/".implode('|', $preg)."/";
        }
    }
    return $preg_str;
}

function checkrushreply($post) {
    global $_G, $rushids;
    if($_GET['authorid']) {
        return $post;
    }
    if(in_array($post['number'], $rushids)) {
        $post['rewardfloor'] = 1;
    }
    return $post;
}