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

    if(!$post['hotrecommended']) {
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
    }

    if($post['existinfirstpage']) {
        if($_G['forum_pagebydesc']) {
            $_G['forum_ppp2']--;
        } else {
            if($ordertype != 1) {
                ++$_G['forum_numpost'];
            } else {
                --$_G['forum_numpost'];
            }
        }
    }

    if($maxposition) {
        $post['number'] = $post['position'];
    }

    if($post['hotrecommended']) {
        $post['number'] = -1;
    }

    if(!$_G['forum_thread']['special'] && !$rushreply && !$hiddenreplies && $_G['setting']['threadfilternum'] && getstatus($post['status'], 11)) {
        $post['isWater'] = true;
        if($_G['setting']['hidefilteredpost'] && !$_G['forum']['noforumhidewater']) {
            $post['inblacklist'] = true;
        }
    } else {
        $_G['allblocked'] = false;
    }

    if($post['inblacklist']) {
        $_G['blockedpids'][] = $post['pid'];
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
            $post['upgradeprogress'] = 100 - ceil($post['upgradecredit'] / ($_G['cache']['usergroups'][$post['groupid']]['creditslower'] - $_G['cache']['usergroups'][$post['groupid']]['creditshigher']) * 100);
            $post['upgradeprogress'] = min(max($post['upgradeprogress'], 2), 100);
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
        if((!empty($_G['setting']['guestviewthumb']['flag']) && !$_G['uid']) || $_G['group']['allowgetattach'] || $_G['group']['allowgetimage']) {
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
    $imgcontent = $post['first'] ? getstatus($_G['forum_thread']['status'], 15) : 0;
    if(!defined('IN_ARCHIVER')) {
        if($post['first']) {
            if(!defined('IN_MOBILE')) {
                $messageindex = false;
                if(strpos($post['message'], '[/index]') !== FALSE) {
                    $post['message'] = preg_replace("/\s?\[index\](.+?)\[\/index\]\s?/ies", "parseindex('\\1', '$post[pid]')", $post['message']);
                    $messageindex = true;
                    unset($_GET['threadindex']);
                }
                if(strpos($post['message'], '[page]') !== FALSE) {
                    if($_GET['cp'] != 'all') {
                        $postbg = '';
                        if(strpos($post['message'], '[/postbg]') !== FALSE) {
                            preg_match("/\s?\[postbg\]\s*([^\[\<\r\n;'\"\?\(\)]+?)\s*\[\/postbg\]\s?/is", $post['message'], $r);
                            $postbg = $r[0];
                        }
                        $messagearray = explode('[page]', $post['message']);
                        $cp = max(intval($_GET['cp']), 1);
                        $post['message'] = $messagearray[$cp - 1];
                        if($postbg && strpos($post['message'], '[/postbg]') === FALSE) {
                            $post['message'] = $postbg.$post['message'];
                        }
                        unset($postbg);
                    } else {
                        $cp = 0;
                        $post['message'] = preg_replace("/\s?\[page\]\s?/is", '', $post['message']);
                    }
                    if($_GET['cp'] != 'all' && strpos($post['message'], '[/index]') === FALSE && empty($_GET['threadindex']) && !$messageindex) {
                        $_G['forum_posthtml']['footer'][$post['pid']] .= '<div id="threadpage"></div><script type="text/javascript" reload="1">show_threadpage('.$post['pid'].', '.$cp.', '.count($messagearray).', '.($_GET['from'] == 'preview' ? '1' : '0').');</script>';
                    }
                }
            }
        }
        if(!empty($_GET['threadindex'])) {
            $_G['forum_posthtml']['header'][$post['pid']] .= '<div id="threadindex"></div><script type="text/javascript" reload="1">show_threadindex(0, '.($_GET['from'] == 'preview' ? '1' : '0').');</script>';
        }
        if(!$imgcontent) {
            $post['message'] = discuzcode($post['message'], $post['smileyoff'], $post['bbcodeoff'], $post['htmlon'] & 1, $_G['forum']['allowsmilies'], $forum_allowbbcode, ($_G['forum']['allowimgcode'] && $_G['setting']['showimages'] ? 1 : 0), $_G['forum']['allowhtml'], ($_G['forum']['jammer'] && $post['authorid'] != $_G['uid'] ? 1 : 0), 0, $post['authorid'], $_G['cache']['usergroups'][$post['groupid']]['allowmediacode'] && $_G['forum']['allowmediacode'], $post['pid'], $_G['setting']['lazyload'], $post['dbdateline'], $post['first']);
            if($post['first']) {
                $_G['relatedlinks'] = '';
                $relatedtype = !$_G['forum_thread']['isgroup'] ? 'forum' : 'group';
                if(!$_G['setting']['relatedlinkstatus']) {
                    $_G['relatedlinks'] = get_related_link($relatedtype);
                } else {
                    $post['message'] = parse_related_link($post['message'], $relatedtype);
                }
                if(strpos($post['message'], '[/begin]') !== FALSE) {
                    $post['message'] = preg_replace("/\[begin(=\s*([^\[\<\r\n]*?)\s*,(\d*),(\d*),(\d*),(\d*))?\]\s*([^\[\<\r\n]+?)\s*\[\/begin\]/ies", $_G['cache']['usergroups'][$post['groupid']]['allowbegincode'] ? "parsebegin('\\2', '\\7', '\\3', '\\4', '\\5', '\\6');" : '', $post['message']);
                }
            }
        }
    }
    if(defined('IN_ARCHIVER') || defined('IN_MOBILE') || !$post['first']) {
        if(strpos($post['message'], '[page]') !== FALSE) {
            $post['message'] = preg_replace("/\s?\[page\]\s?/is", '', $post['message']);
        }
        if(strpos($post['message'], '[/index]') !== FALSE) {
            $post['message'] = preg_replace("/\s?\[index\](.+?)\[\/index\]\s?/is", '', $post['message']);
        }
        if(strpos($post['message'], '[/begin]') !== FALSE) {
            $post['message'] = preg_replace("/\[begin(=\s*([^\[\<\r\n]*?)\s*,(\d*),(\d*),(\d*),(\d*))?\]\s*([^\[\<\r\n]+?)\s*\[\/begin\]/ies", '', $post['message']);
        }
    }
    if($imgcontent) {
        $post['message'] = '<img id="threadimgcontent" src="./'.stringtopic('', $post['tid']).'">';
    }
    $_G['forum_firstpid'] = intval($_G['forum_firstpid']);
    $post['numbercard'] = viewthread_numbercard($post);
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

function viewthread_baseinfo($post, $extra) {
    global $_G;
    list($key, $type) = $extra;
    $v = '';
    if(substr($key, 0, 10) == 'extcredits') {
        $i = substr($key, 10);
        $extcredit = $_G['setting']['extcredits'][$i];
        if($extcredit) {
            $v = $type ? ($extcredit['img'] ? $extcredit['img'].' ' : '').$extcredit['title'] : $post['extcredits'.$i].' '.$extcredit['unit'];
        }
    } elseif(substr($key, 0, 6) == 'field_') {
        $field = substr($key, 6);
        if(!empty($post['privacy']['profile'][$field])) {
            return '';
        }
        require_once libfile('function/profile');
        if($field != 'qq') {
            $v = profile_show($field, $post);
        } elseif(!empty($post['qq'])) {
            $v = '<a href="http://wpa.qq.com/msgrd?V=3&Uin='.$post['qq'].'&Site='.$_G['setting']['bbname'].'&Menu=yes&from=discuz" target="_blank" title="'.lang('spacecp', 'qq_dialog').'"><img src="'.STATICURL.'/image/common/qq_big.gif" alt="QQ" style="margin:0px;"/></a>';
        }
        if($v) {
            if(!isset($_G['cache']['profilesetting'])) {
                loadcache('profilesetting');
            }
            $v = $type ? $_G['cache']['profilesetting'][$field]['title'] : $v;
        }
    } elseif($key == 'eccredit_seller') {
        $v = $type ? lang('space', 'viewthread_userinfo_sellercredit') : '<a href="home.php?mod=space&uid='.$post['uid'].'&do=trade&view=eccredit#buyercredit" target="_blank" class="vm"><img src="'.STATICURL.'image/traderank/seller/'.countlevel($post['buyercredit']).'.gif" /></a>';
    } elseif($key == 'eccredit_buyer') {
        $v = $type ? lang('space', 'viewthread_userinfo_buyercredit') : '<a href="home.php?mod=space&uid='.$post['uid'].'&do=trade&view=eccredit#sellercredit" target="_blank" class="vm"><img src="'.STATICURL.'image/traderank/seller/'.countlevel($post['sellercredit']).'.gif" /></a>';
    } else {
        $v = getLinkByKey($key, $post);
        if($v !== '') {
            $v = $type ? lang('space', 'viewthread_userinfo_'.$key) : $v;
        }
    }
    return $v;
}

function viewthread_profile_nodeparse($param) {
    list($name, $s, $e, $extra, $post) = $param;
    if(strpos($name, ':') === false) {
        if(function_exists('profile_node_'.$name)) {
            return call_user_func('profile_node_'.$name, $post, $s, $e, explode(',', $extra));
        } else {
            return '';
        }
    } else {
        list($plugin, $pluginid) = explode(':', $name);
        if($plugin == 'plugin') {
            global $_G;
            static $pluginclasses;
            if(isset($_G['setting']['plugins']['profile_node'][$pluginid])) {
                @include_once DISCUZ_ROOT.'./source/plugin/'.$_G['setting']['plugins']['profile_node'][$pluginid].'.class.php';
                $classkey = 'plugin_'.$pluginid;
                if(!class_exists($classkey, false)) {
                    return '';
                }
                if(!isset($pluginclasses[$classkey])) {
                    $pluginclasses[$classkey] = new $classkey;
                }
                return call_user_func(array($pluginclasses[$classkey], 'profile_node'), $post, $s, $e, explode(',', $extra));
            }
        }
    }
}

function viewthread_profile_node($type, $post) {
    global $_G;
    $tpid = false;
    if($post['verifyicon']) {
        $tpid = isset($_G['setting']['profilenode']['groupid'][-$post['verifyicon'][0]]) ? $_G['setting']['profilenode']['groupid'][-$post['verifyicon'][0]] : false;
    }
    if($tpid === false) {
        $tpid = isset($_G['setting']['profilenode']['groupid'][$post['groupid']]) ? $_G['setting']['profilenode']['groupid'][$post['groupid']] : 0;
    }
    $template = $_G['setting']['profilenode']['template'][$tpid][$type];
    $code = $_G['setting']['profilenode']['code'][$tpid][$type];
    include_once template('forum/viewthread_profile_node');
    foreach($code as $k => $p) {
        $p[] = $post;
        $template = str_replace($k, call_user_func('viewthread_profile_nodeparse', $p), $template);
    }
    echo $template;
}

function viewthread_numbercard($post) {
    global $_G;
    if(!is_array($_G['setting']['numbercard'])) {
        $_G['setting']['numbercard'] = dunserialize($_G['setting']['numbercard']);
    }

    $numbercard = array();
    foreach($_G['setting']['numbercard']['row'] as $key) {
        if(substr($key, 0, 10) == 'extcredits') {
            $numbercard[] = array('link' => 'home.php?mod=space&uid='.$post['uid'].'&do=profile', 'value' => $post[$key], 'lang' => $_G['setting']['extcredits'][substr($key, 10)]['title']);
        } else {
            $getLink = getLinkByKey($key, $post, 1);
            $numbercard[] = array('link' => $getLink['link'], 'value' => $getLink['value'], 'lang' => lang('space', 'viewthread_userinfo_'.$key));
        }
    }
    return $numbercard;
}
function getLinkByKey($key, $post, $returnarray = 0) {
    switch($key) {
        case 'uid': $v = array('link' => '?'.$post['uid'], 'value' => $post['uid']);break;
        case 'posts': $v = array('link' => 'home.php?mod=space&uid='.$post['uid'].'&do=thread&type=reply&view=me&from=space', 'value' => $post['posts']);break;
        case 'threads': $v = array('link' => 'home.php?mod=space&uid='.$post['uid'].'&do=thread&type=thread&view=me&from=space', 'value' => $post['threads']);break;
        case 'digestposts': $v = array('link' => 'home.php?mod=space&uid='.$post['uid'].'&do=thread&type=thread&view=me&from=space', 'value' => $post['digestposts']);break;
        case 'feeds': $v = array('link' => 'home.php?mod=follow&uid='.$post['uid'].'&do=view', 'value' => $post['feeds']);break;
        case 'doings': $v = array('link' => 'home.php?mod=space&uid='.$post['uid'].'&do=doing&view=me&from=space', 'value' => $post['doings']);break;
        case 'blogs': $v = array('link' => 'home.php?mod=space&uid='.$post['uid'].'&do=blog&view=me&from=space', 'value' => $post['blogs']);break;
        case 'albums': $v = array('link' => 'home.php?mod=space&uid='.$post['uid'].'&do=album&view=me&from=space', 'value' => $post['albums']);break;
        case 'sharings': $v = array('link' => 'home.php?mod=space&uid='.$post['uid'].'&do=share&view=me&from=space', 'value' => $post['sharings']);break;
        case 'friends': $v = array('link' => 'home.php?mod=space&uid='.$post['uid'].'&do=friend&view=me&from=space', 'value' => $post['friends']);break;
        case 'follower': $v = array('link' => 'home.php?mod=follow&do=follower&uid='.$post['uid'], 'value' => $post['follower']);break;
        case 'following': $v = array('link' => 'home.php?mod=follow&do=following&uid='.$post['uid'], 'value' => $post['following']);break;
        case 'credits': $v = array('link' => 'home.php?mod=space&uid='.$post['uid'].'&do=profile', 'value' => $post['credits']);break;
        case 'digest': $v = array('value' => $post['digestposts']);break;
        case 'readperm': $v = array('value' => $post['readaccess']);break;
        case 'regtime': $v = array('value' => $post['regdate']);break;
        case 'lastdate': $v = array('value' => $post['lastdate']);break;
        case 'oltime': $v = array('value' => $post['oltime'].' '.lang('space', 'viewthread_userinfo_hour'));break;
    }
    if(!$returnarray) {
        if($v['link']) {
            $v = '<a href="'.$v['link'].'" target="_blank" class="xi2">'.$v['value'].'</a>';
        } else {
            $v = $v['value'];
        }
    }
    return $v;
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
        $query = C::t('common_tagitem')->select($tagidarray, $tid, $type, 'itemid', 'DESC', $limit, 0, '<>');
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
        rsort($relatearray);
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

function parseindex($nodes, $pid) {
    global $_G;
    $nodes = dhtmlspecialchars($nodes);
    $nodes = preg_replace('/(\**?)\[#(\d+)\](.+?)[\r\n]/', "<a page=\"\\2\" sub=\"\\1\">\\3</a>", $nodes);
    $nodes = preg_replace('/(\**?)\[#(\d+),(\d+)\](.+?)[\r\n]/', "<a tid=\"\\2\" pid=\"\\3\" sub=\"\\1\">\\4</a>", $nodes);
    $_G['forum_posthtml']['header'][$pid] .= '<div id="threadindex">'.$nodes.'</div><script type="text/javascript" reload="1">show_threadindex('.$pid.', '.($_GET['from'] == 'preview' ? '1' : '0').')</script>';
    return '';
}

function parsebegin($linkaddr, $imgflashurl, $w = 0, $h = 0, $type = 0, $s = 0) {
    static $begincontent;
    if($begincontent || $_GET['from'] == 'preview') {
        return '';
    }
    preg_match("/((https?){1}:\/\/|www\.)[^\[\"']+/i", $imgflashurl, $matches);
    $imgflashurl = $matches[0];
    $fileext = fileext($imgflashurl);
    $randomid = 'swf_'.random(3);
    $w = ($w >=400 && $w <=1024) ? $w : 900;
    $h = ($h >=300 && $h <=640) ? $h : 500;
    $s = $s ? $s*1000 : 5000;
    switch($fileext) {
        case 'jpg':
        case 'jpeg':
        case 'gif':
        case 'png':
            $content = '<img style="position:absolute;width:'.$w.'px;height:'.$h.'px;" src="'.$imgflashurl.'" />';
            break;
        case 'flv':
            $content = '<span id="'.$randomid.'" style="position:absolute;"></span>'.
                '<script type="text/javascript" reload="1">$(\''.$randomid.'\').innerHTML='.
                'AC_FL_RunContent(\'width\', \''.$w.'\', \'height\', \''.$h.'\', '.
                '\'allowNetworking\', \'internal\', \'allowScriptAccess\', \'never\', '.
                '\'src\', \''.STATICURL.'image/common/flvplayer.swf\', '.
                '\'flashvars\', \'file='.rawurlencode($imgflashurl).'\', \'quality\', \'high\', '.
                '\'wmode\', \'transparent\', \'allowfullscreen\', \'true\');</script>';
            break;
        case 'swf':
            $content = '<span id="'.$randomid.'" style="position:absolute;"></span>'.
                '<script type="text/javascript" reload="1">$(\''.$randomid.'\').innerHTML='.
                'AC_FL_RunContent(\'width\', \''.$w.'\', \'height\', \''.$h.'\', '.
                '\'allowNetworking\', \'internal\', \'allowScriptAccess\', \'never\', '.
                '\'src\', encodeURI(\''.$imgflashurl.'\'), \'quality\', \'high\', \'bgcolor\', \'#ffffff\', '.
                '\'wmode\', \'transparent\', \'allowfullscreen\', \'true\');</script>';
            break;
        default:
            $content = '';
    }
    if($content) {
        if($type == 1) {
            $content = '<div id="threadbeginid" style="display:none;">'.
                '<div class="flb beginidin"><span><div id="begincloseid" class="flbc" title="'.lang('core', 'close').'">'.lang('core', 'close').'</div></span></div>'.
                $content.'<div class="beginidimg" style=" width:'.$w.'px;height:'.$h.'px;">'.
                '<a href="'.$linkaddr.'" target="_blank" style="display: block; width:'.$w.'px; height:'.$h.'px;"></a></div></div>'.
                '<script type="text/javascript">threadbegindisplay(1, '.$w.', '.$h.', '.$s.');</script>';
        } else {
            $content = '<div id="threadbeginid">'.
                '<div class="flb beginidin">
                    <span><div id="begincloseid" class="flbc" title="'.lang('core', 'close').'">'.lang('core', 'close').'</div></span>
                </div>'.
                $content.'<div class="beginidimg" style=" width:'.$w.'px; height:'.$h.'px;">'.
                '<a href="'.$linkaddr.'" target="_blank" style="display: block; width:'.$w.'px; height:'.$h.'px;"></a></div>
                </div>'.
                '<script type="text/javascript">threadbegindisplay('.$type.', '.$w.', '.$h.', '.$s.');</script>';
        }
    }
    $begincontent = $content;
    return $content;
}

function _checkviewgroup() {
    global $_G;
    $_G['action']['action'] = 3;
    require_once libfile('function/group');
    $status = groupperm($_G['forum'], $_G['uid']);
    if($status == 1) {
        showmessage('forum_group_status_off');
    } elseif($status == 2) {
        showmessage('forum_group_noallowed', 'forum.php?mod=group&fid='.$_G['fid']);
    } elseif($status == 3) {
        showmessage('forum_group_moderated', 'forum.php?mod=group&fid='.$_G['fid']);
    }
}
