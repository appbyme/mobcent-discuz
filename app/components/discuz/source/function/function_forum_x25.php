<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

// function showmessagenoperm($type, $fid, $formula = '') {
function mobcent_showmessagenoperm($type, $fid, $formula = '') {
    global $_G;
    loadcache('usergroups');
    if($formula) {
        $formula = dunserialize($formula);
        $permmessage = stripslashes($formula['message']);
    }

    $usergroups = $nopermgroup = $forumnoperms = array();
    $nopermdefault = array(
        'viewperm' => array(),
        'getattachperm' => array(),
        'postperm' => array(7),
        'replyperm' => array(7),
        'postattachperm' => array(7),
    );
    $perms = array('viewperm', 'postperm', 'replyperm', 'getattachperm', 'postattachperm');

    foreach($_G['cache']['usergroups'] as $gid => $usergroup) {
        $usergroups[$gid] = $usergroup['type'];
        $grouptype = $usergroup['type'] == 'member' ? 0 : 1;
        $nopermgroup[$grouptype][] = $gid;
    }
    if($fid == $_G['forum']['fid']) {
        $forum = $_G['forum'];
    } else {
        $forum = C::t('forum_forumfield')->fetch($fid);
    }

    foreach($perms as $perm) {
        $permgroups = explode("\t", $forum[$perm]);
        $membertype = $forum[$perm] ? array_intersect($nopermgroup[0], $permgroups) : TRUE;
        $forumnoperm = $forum[$perm] ? array_diff(array_keys($usergroups), $permgroups) : $nopermdefault[$perm];
        foreach($forumnoperm as $groupid) {
            $nopermtype = $membertype && $groupid == 7 ? 'login' : ($usergroups[$groupid] == 'system' || $usergroups[$groupid] == 'special' ? 'none' : ($membertype ? 'upgrade' : 'none'));
            $forumnoperms[$fid][$perm][$groupid] = array($nopermtype, $permgroups);
        }
    }

    $v = $forumnoperms[$fid][$type][$_G['groupid']][0];
    $gids = $forumnoperms[$fid][$type][$_G['groupid']][1];
    $comma = $permgroups = '';
    if(is_array($gids)) {
        foreach($gids as $gid) {
            if($gid && $_G['cache']['usergroups'][$gid]) {
                $permgroups .= $comma.$_G['cache']['usergroups'][$gid]['grouptitle'];
                $comma = ', ';
            } elseif($_G['setting']['verify']['enabled'] && substr($gid, 0, 1) == 'v') {
                $vid = substr($gid, 1);
                $permgroups .= $comma.$_G['setting']['verify'][$vid]['title'];
                $comma = ', ';
            }

        }
    }

    $custom = 0;
    if($permmessage) {
        $message = $permmessage;
        $custom = 1;
    } else {
        if($v) {
            $message = $type.'_'.$v.'_nopermission';
        } else {
            $message = 'group_nopermission';
        }
    }
    
    // showmessage($message, NULL, array('fid' => $fid, 'permgroups' => $permgroups, 'grouptitle' => $_G['group']['grouptitle']), array('login' => 1), $custom);
    $customMsg = '';
    if ($custom) {
        $customMsg = $message;
        $message = 'error_custom';
    }
    return array(
        'message' => $message,
        'params' => array(
            '{customMsg}' => $customMsg,
            '{fid}' => $fid, 
            '{permgroups}' => $permgroups, 
            '{grouptitle}' => $_G['group']['grouptitle']
        ),
    );
}

// function formulaperm($formula) {
function mobcent_formulaperm($formula) {
    $msg = array('message' => '', 'params' => array());

    global $_G;
    if($_G['forum']['ismoderator']) {
        // return TRUE;
        return $msg;
    }

    $formula = dunserialize($formula);
    $medalperm = $formula['medal'];
    $permusers = $formula['users'];
    $permmessage = $formula['message'];
    if($_G['setting']['medalstatus'] && $medalperm) {
        $exists = 1;
        $_G['forum_formulamessage'] = '';
        $medalpermc = $medalperm;
        if($_G['uid']) {
            $memberfieldforum = C::t('common_member_field_forum')->fetch($_G['uid']);
            $medals = explode("\t", $memberfieldforum['medals']);
            unset($memberfieldforum);
            foreach($medalperm as $k => $medal) {
                foreach($medals as $r) {
                    list($medalid) = explode("|", $r);
                    if($medalid == $medal) {
                        $exists = 0;
                        unset($medalpermc[$k]);
                    }
                }
            }
        } else {
            $exists = 0;
        }
        if($medalpermc) {
            loadcache('medals');
            foreach($medalpermc as $medal) {
                if($_G['cache']['medals'][$medal]) {
                    $_G['forum_formulamessage'] .= '<img src="'.STATICURL.'image/common/'.$_G['cache']['medals'][$medal]['image'].'" style="vertical-align:middle;" />&nbsp;'.$_G['cache']['medals'][$medal]['name'].'&nbsp; ';
                }
            }
            // showmessage('forum_permforum_nomedal', NULL, array('forum_permforum_nomedal' => $_G['forum_formulamessage']), array('login' => 1));
            return array(
                'message' => 'forum_permforum_nomedal',
                'params' => array(
                    '{forum_permforum_nomedal}' => $_G['forum_formulamessage'],
                ),
            );
        }
    }
    $formulatext = $formula[0];
    $formula = $formula[1];
    if($_G['adminid'] == 1 || $_G['forum']['ismoderator'] || in_array($_G['groupid'], explode("\t", $_G['forum']['spviewperm']))) {
        // return FALSE;
        return $msg;
    }
    if($permusers) {
        $permusers = str_replace(array("\r\n", "\r"), array("\n", "\n"), $permusers);
        $permusers = explode("\n", trim($permusers));
        if(!in_array($_G['member']['username'], $permusers)) {
            // showmessage('forum_permforum_disallow', NULL, array(), array('login' => 1));
            return array(
                'message' => 'forum_permforum_disallow',
                'params' => array(),
            );
        }
    }
    if(!$formula) {
        // return FALSE;
        return $msg;
    }
    if(strexists($formula, '$memberformula[')) {
        preg_match_all("/\\\$memberformula\['(\w+?)'\]/", $formula, $a);
        $profilefields = array();
        foreach($a[1] as $field) {
            switch($field) {
                case 'regdate':
                    $formula = preg_replace("/\{(\d{4})\-(\d{1,2})\-(\d{1,2})\}/e", "'\'\\1-'.sprintf('%02d', '\\2').'-'.sprintf('%02d', '\\3').'\''", $formula);
                case 'regday':
                    break;
                case 'regip':
                case 'lastip':
                    $formula = preg_replace("/\{([\d\.]+?)\}/", "'\\1'", $formula);
                    $formula = preg_replace('/(\$memberformula\[\'(regip|lastip)\'\])\s*=+\s*\'([\d\.]+?)\'/', "strpos(\\1, '\\3')===0", $formula);
                case 'buyercredit':
                case 'sellercredit':
                    space_merge($_G['member'], 'status');break;
                case substr($field, 0, 5) == 'field':
                    space_merge($_G['member'], 'profile');
                    $profilefields[] = $field;break;
            }
        }
        $memberformula = array();
        if($_G['uid']) {
            $memberformula = $_G['member'];
            if(in_array('regday', $a[1])) {
                $memberformula['regday'] = intval((TIMESTAMP - $memberformula['regdate']) / 86400);
            }
            if(in_array('regdate', $a[1])) {
                $memberformula['regdate'] = date('Y-m-d', $memberformula['regdate']);
            }
            $memberformula['lastip'] = $memberformula['lastip'] ? $memberformula['lastip'] : $_G['clientip'];
        } else {
            if(isset($memberformula['regip'])) {
                $memberformula['regip'] = $_G['clientip'];
            }
            if(isset($memberformula['lastip'])) {
                $memberformula['lastip'] = $_G['clientip'];
            }
        }
    }
    @eval("\$formulaperm = ($formula) ? TRUE : FALSE;");
    if(!$formulaperm) {
        if(!$permmessage) {
            $language = lang('forum/misc');
            $search = array('regdate', 'regday', 'regip', 'lastip', 'buyercredit', 'sellercredit', 'digestposts', 'posts', 'threads', 'oltime');
            $replace = array($language['formulaperm_regdate'], $language['formulaperm_regday'], $language['formulaperm_regip'], $language['formulaperm_lastip'], $language['formulaperm_buyercredit'], $language['formulaperm_sellercredit'], $language['formulaperm_digestposts'], $language['formulaperm_posts'], $language['formulaperm_threads'], $language['formulaperm_oltime']);
            for($i = 1; $i <= 8; $i++) {
                $search[] = 'extcredits'.$i;
                $replace[] = $_G['setting']['extcredits'][$i]['title'] ? $_G['setting']['extcredits'][$i]['title'] : $language['formulaperm_extcredits'].$i;
            }
            if($profilefields) {
                loadcache(array('fields_required', 'fields_optional'));
                foreach($profilefields as $profilefield) {
                    $search[] = $profilefield;
                    $replace[] = !empty($_G['cache']['fields_optional']['field_'.$profilefield]) ? $_G['cache']['fields_optional']['field_'.$profilefield]['title'] : $_G['cache']['fields_required']['field_'.$profilefield]['title'];
                }
            }
            $i = 0;$_G['forum_usermsg'] = '';
            foreach($search as $s) {
                if(in_array($s, array('digestposts', 'posts', 'threads', 'oltime', 'extcredits1', 'extcredits2', 'extcredits3', 'extcredits4', 'extcredits5', 'extcredits6', 'extcredits7', 'extcredits8'))) {
                    $_G['forum_usermsg'] .= strexists($formulatext, $s) ? '<br />&nbsp;&nbsp;&nbsp;'.$replace[$i].': '.(@eval('return intval(getuserprofile(\''.$s.'\'));')) : '';
                } elseif(in_array($s, array('regdate', 'regip', 'regday'))) {
                    $_G['forum_usermsg'] .= strexists($formulatext, $s) ? '<br />&nbsp;&nbsp;&nbsp;'.$replace[$i].': '.(@eval('return $memberformula[\''.$s.'\'];')) : '';
                }
                $i++;
            }
            $search = array_merge($search, array('and', 'or', '>=', '<=', '=='));
            $replace = array_merge($replace, array('&nbsp;&nbsp;<b>'.$language['formulaperm_and'].'</b>&nbsp;&nbsp;', '&nbsp;&nbsp;<b>'.$language['formulaperm_or'].'</b>&nbsp;&nbsp;', '&ge;', '&le;', '='));
            $_G['forum_formulamessage'] = str_replace($search, $replace, $formulatext);
        } else {
            $_G['forum_formulamessage'] = $permmessage;
        }

        if(!$permmessage) {
            // showmessage('forum_permforum_nopermission', NULL, array('formulamessage' => $_G['forum_formulamessage'], 'usermsg' => $_G['forum_usermsg']), array('login' => 1));
            return array(
                'message' => 'forum_permforum_nopermission',
                'params' => array(
                    '{formulamessage}' => $_G['forum_formulamessage'], 
                    '{usermsg}' => $_G['forum_usermsg'],
                ),
            );
        } else {
            // showmessage('forum_permforum_nopermission_custommsg', NULL, array('formulamessage' => $_G['forum_formulamessage']), array('login' => 1));
            return array(
                'message' => 'forum_permforum_nopermission_custommsg',
                'params' => array(
                    '{formulamessage}' => $_G['forum_formulamessage'], 
                ),
            );
        }
    }
    // return TRUE;
    return $msg;
}