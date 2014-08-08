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

function viewthread_updateviews($threadtable) {
    global $_G;
    if($_G['setting']['delayviewcount'] == 1 || $_G['setting']['delayviewcount'] == 3) {
        $_G['forum_logfile'] = './data/cache/forum_threadviews_'.intval(getglobal('config/server/id')).'.log';
        if(substr(TIMESTAMP, -2) == '00') {
            require_once libfile('function/misc');
            updateviews($threadtable, 'tid', 'views', $_G['forum_logfile']);
        }
        if(@$fp = fopen(DISCUZ_ROOT.$_G['forum_logfile'], 'a')) {
            fwrite($fp, "$_G[tid]\n");
            fclose($fp);
        } elseif($_G['adminid'] == 1) {
            showmessage('view_log_invalid', '', array('logfile' => $_G['forum_logfile']));
        }
    } else {

        DB::query("UPDATE LOW_PRIORITY ".DB::table($threadtable)." SET views=views+1 WHERE tid='$_G[tid]'", 'UNBUFFERED');

    }
}

function viewthread_procpost($post, $lastvisit, $ordertype, $special = 0) {
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
            $post['number'] = $post['first'] == 1 ? 1 : $_G['forum_numpost'] - $_G['forum_ppp2']--;
        }
    } else {
        if($ordertype != 1) {
            $post['number'] = ++$_G['forum_numpost'];
        } else {
            $post['number'] = $post['first'] == 1 ? 1 : --$_G['forum_numpost'];
        }
    }

    $_G['forum_postcount']++;

    $post['dbdateline'] = $post['dateline'];
    if($_G['setting']['dateconvert']) {
        $post['dateline'] = dgmdate($post['dateline'], 'u');
    } else {
        $dformat = getglobal('setting/dateformat');
        $tformat = getglobal('setting/timeformat');
        $post['dateline'] = dgmdate($post['dateline'], $dformat.' '.str_replace(":i", ":i:s", $tformat));
    }
    $post['groupid'] = $_G['cache']['usergroups'][$post['groupid']] ? $post['groupid'] : 7;

    if($post['username']) {

        $_G['forum_onlineauthors'][] = $post['authorid'];
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
        $post['lastdate'] = dgmdate($post['lastactivity'], 'd');

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
            $_G['forum_attachpids'] .= ",$post[pid]";
            $post['attachment'] = 0;
            if(preg_match_all("/\[attach\](\d+)\[\/attach\]/i", $post['message'], $matchaids)) {
                $_G['forum_attachtags'][$post['pid']] = $matchaids[1];
            }
        } else {
            $post['message'] = preg_replace("/\[attach\](\d+)\[\/attach\]/i", '', $post['message']);
        }
    }

    $_G['forum_ratelogpid'] .= ($_G['setting']['ratelogrecord'] && $post['ratetimes']) ? ','.$post['pid'] : '';
    if($_G['setting']['commentnumber'] && ($post['first'] && $_G['setting']['commentfirstpost'] || !$post['first'])) {
        $_G['forum_commonpid'] .= $post['comment'] ? ','.$post['pid'] : '';
    }
    $post['allowcomment'] = $_G['setting']['commentnumber'] && in_array(1, $_G['setting']['allowpostcomment']) && ($_G['setting']['commentpostself'] || $post['authorid'] != $_G['uid']) &&
        ($post['first'] && $_G['setting']['commentfirstpost'] && in_array($_G['group']['allowcommentpost'], array(1, 3)) ||
        (!$post['first'] && in_array($_G['group']['allowcommentpost'], array(2, 3))));
    $_G['forum']['allowbbcode'] = $_G['forum']['allowbbcode'] ? -$post['groupid'] : 0;
    $post['signature'] = $post['usesig'] ? ($_G['setting']['sigviewcond'] ? (strlen($post['message']) > $_G['setting']['sigviewcond'] ? $post['signature'] : '') : $post['signature']) : '';
    if(!defined('IN_ARCHIVER')) {
        $post['message'] = discuzcode($post['message'], $post['smileyoff'], $post['bbcodeoff'], $post['htmlon'] & 1, $_G['forum']['allowsmilies'], $_G['forum']['allowbbcode'], ($_G['forum']['allowimgcode'] && $_G['setting']['showimages'] ? 1 : 0), $_G['forum']['allowhtml'], ($_G['forum']['jammer'] && $post['authorid'] != $_G['uid'] ? 1 : 0), 0, $post['authorid'], $_G['cache']['usergroups'][$post['groupid']]['allowmediacode'] && $_G['forum']['allowmediacode'], $post['pid'], $_G['setting']['lazyload']);
        if($post['first']) {
            if(!$_G['forum_thread']['isgroup']) {
                $_G['relatedlinks'] = getrelatedlink('forum');
            } else {
                $_G['relatedlinks'] = getrelatedlink('group');
            }
        }
    }
    $_G['forum_firstpid'] = intval($_G['forum_firstpid']);
    $post['custominfo'] = viewthread_custominfo($post);
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

            viewthread_updateviews($_G['forum_thread']['threadtable']);
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

    $lastmod = DB::fetch_first("SELECT uid AS moduid, username AS modusername, dateline AS moddateline, action AS modaction, magicid, stamp, reason
        FROM ".DB::table('forum_threadmod')."
        WHERE tid='$thread[tid]' ORDER BY dateline DESC LIMIT 1");
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
        DB::query("UPDATE ".DB::table($thread['threadtable'])." SET moderated='0' WHERE tid='$thread[tid]'", 'UNBUFFERED');
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
                $v = '<dt>'.($extcredit['img'] ? $extcredit['img'].' ' : '').$extcredit['title'].'</dt><dd>'.$post['extcredits'.$i].' '.$extcredit['unit'].'</dd>';
            } elseif(substr($key, 0, 6) == 'field_') {
                $field = substr($key, 6);
                if(!empty($post['privacy']['profile'][$field])) {
                    continue;
                }
                require_once libfile('function/profile');
                $v = profile_show($field, $post);
                if($v) {
                    $v = '<dt>'.$_G['cache']['custominfo']['profile'][$key][0].'</dt><dd title="'.htmlspecialchars(strip_tags($v)).'">'.$v.'</dd>';
                }
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

function getrelateitem($tagarray, $tid = 0, $type = 'tid') {
    global $_G;
    $tagidarray = $relatearray = $relateitem = array();
    $limit = $_G['setting']['relatenum'];
    $limitsum = 2 * $limit;
    if(!$limit) {
        return '';
    }
    foreach($tagarray as $var) {
        $tagidarray[] = $var['0'];
    }
    if(!$tagidarray) {
        return '';
    }
    $query = DB::query("SELECT itemid FROM ".DB::table('common_tagitem')." WHERE tagid IN (".dimplode($tagidarray).") AND idtype='$type' LIMIT $limitsum");
    $i = 1;
    while($result = DB::fetch($query)) {
        if($result['itemid'] != $tid) {
            if($i > $limit) {
                break;
            }
            if($relatearray[$result[itemid]] == '') {
                $i++;
            }
            if($result['itemid']) {
                $relatearray[$result[itemid]] = $result['itemid'];
            }

        }
    }
    if(!empty($relatearray)) {
        $query = DB::query("SELECT tid,subject,displayorder FROM ".DB::table('forum_thread')." WHERE tid IN (".dimplode($relatearray).")");
        while($result = DB::fetch($query)) {
            if($result['displayorder'] >= 0) {
                $relateitem[] = $result;
            }
        }
    }
    return $relateitem;
}

function viewthread_oldtopics($tid = 0) {
    global $_G;

    $oldthreads = array();

    $oldtopics = isset($_G['cookie']['oldtopics']) ? $_G['cookie']['oldtopics'] : 'D';

    if($_G['setting']['visitedthreads']) {
        $oldtids = array_slice(explode('D', $oldtopics), 0, $_G['setting']['visitedthreads']);
        $oldtidsnew = array();
        foreach($oldtids as $oldtid) {
            $oldtid && $oldtidsnew[] = $oldtid;
        }
        if($oldtidsnew) {
            $query = DB::query("SELECT tid, subject FROM ".DB::table('forum_thread')." WHERE tid IN (".dimplode($oldtidsnew).")");
            while($oldthread = DB::fetch($query)) {
                $oldthreads[$oldthread['tid']] = $oldthread['subject'];
            }
        }
        array_unshift($oldtidsnew, $tid);
        dsetcookie('oldtopics', implode('D', array_slice($oldtidsnew, 0, $_G['setting']['visitedthreads'])), 3600);     ;
    }

    if($_G['member']['lastvisit'] < $_G['forum_thread']['lastpost'] && (!isset($_G['cookie']['fid'.$_G['fid']]) || $_G['forum_thread']['lastpost'] > $_G['cookie']['fid'.$_G['fid']])) {
        dsetcookie('fid'.$_G['fid'], $_G['forum_thread']['lastpost'], 3600);
    }

    return $oldthreads;
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
    if($_G['gp_authorid'] || $_G['gp_ordertype'] == 1 || $_G['gp_checkrush']) {
        return $post;
    }
    if(in_array($post['number'], $rushids)) {
        $post['rewardfloor'] = 1;
    }
    return $post;
}

function viewthread_is_search_referer() {
    $regex = "((http|https)\:\/\/)?";
    $regex .= "([a-z]*.)?(ask.com|yahoo.com|cn.yahoo.com|bing.com|baidu.com|soso.com|google.com|google.cn)(.[a-z]{2,3})?\/";
    if(preg_match("/^$regex/", $_SERVER['HTTP_REFERER'])) {
        return true;
    }
    return false;
}