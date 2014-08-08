<?php

/**
 * 私信管理接口
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class PMAdminAction extends MobcentAction {

    public function run($json) {
        $res = $this->initWebApiArray();

        // $json = "{
        //     'action': 'send',
        //     'toUid': 1,
        //     'plid': 0,
        //     'pmid': 67,
        //     'msg': {
        //         'type': 'text', 
        //         'content': 'http://localhost/31g/mobcent/app/data/phiz/default/10.png',
        //     },
        // }";
        $json = rawurldecode($json);
        $json = WebUtils::jsonDecode($json);
        
        !isset($json['action']) && $json['action'] = 'send';
        !isset($json['toUid']) && $json['toUid'] = 0;
        !isset($json['plid']) && $json['plid'] = 0;
        !isset($json['pmid']) && $json['pmid'] = 0;

        switch ($json['action']) {
            case 'send': $res = $this->_pmSend($res, $json); break;
            default: $res = $this->makeErrorInfo($res, 'mobcent_error_params'); break;
        }

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _pmSend($res, $data) {
        $touid = (int)$data['toUid'];
        $pmid = (int)$data['pmid'];
        $_GET['topmuid'] = $touid;
        $_POST['message'] = $this->_transMessage($data['msg']);
        $_POST['subject'] = '';
        $users = array();
        $type = 0;

        global $_G;

        require_once libfile('function/spacecp');
        // require_once libfile('function/magic');

        loaducenter();

        // 在DISCUZ_ROOT/source/include/spacecp/spacecp_pm.php基础上二次开发
        $waittime = interval_check('post');
        if ($waittime > 0) {
            // showmessage('message_can_not_send_2', '', array(), array('return' => true));
            return $this->makeErrorInfo($res, lang('message', 'message_can_not_send_2'));
        }

        if (($checkMessage = mobcent_cknewuser()) != '') {
            return $this->makeErrorInfo($res, WebUtils::emptyHtml($checkMessage));
        }
        
        if (!checkperm('allowsendpm')) {
            // showmessage('no_privilege_sendpm', '', array(), array('return' => true));
            return $this->makeErrorInfo($res, 'no_privilege_sendpm');
        }

        if ($touid) {
            if (isblacklist($touid)) {
                // showmessage('is_blacklist', '', array(), array('return' => true));
                return $this->makeErrorInfo($res, lang('message', 'is_blacklist'));
            }
        }

        // !($_G['group']['exempt'] & 1) && checklowerlimit('sendpm', 0, $coef);

        $message = (!empty($_POST['messageappend']) ? $_POST['messageappend']."\n" : '').trim($_POST['message']);
        if(empty($message)) {
            // showmessage('unable_to_send_air_news', '', array(), array('return' => true));
            return $this->makeErrorInfo($res, lang('message', 'unable_to_send_air_news'));
        }
        // $message = censor($message);
        
        loadcache(array('smilies', 'smileytypes'));
        foreach($_G['cache']['smilies']['replacearray'] AS $key => $smiley) {
            $_G['cache']['smilies']['replacearray'][$key] = '[img]'.$_G['siteurl'].'static/image/smiley/'.$_G['cache']['smileytypes'][$_G['cache']['smilies']['typearray'][$key]]['directory'].'/'.$smiley.'[/img]';
        }
        $message = preg_replace($_G['cache']['smilies']['searcharray'], $_G['cache']['smilies']['replacearray'], $message);
                $subject = '';
        if($type == 1) {
            $subject = dhtmlspecialchars(trim($_POST['subject']));
        }

        include_once libfile('function/friend');
        $return = 0;
        if($touid || $pmid) {
            if($touid) {
                if(($value = getuserbyuid($touid))) {
                    $value['onlyacceptfriendpm'] = $value['onlyacceptfriendpm'] ? $value['onlyacceptfriendpm'] : ($_G['setting']['onlyacceptfriendpm'] ? 1 : 2);
                    if($_G['group']['allowsendallpm'] || $value['onlyacceptfriendpm'] == 2 || ($value['onlyacceptfriendpm'] == 1 && friend_check($touid))) {
                        $return = sendpm($touid, $subject, $message, '', 0, 0, $type);
                    } else {
                        // showmessage('message_can_not_send_onlyfriend', '', array(), array('return' => true));
                        return $this->makeErrorInfo($res, lang('message', 'message_can_not_send_onlyfriend'));
                    }
                } else {
                    // showmessage('message_bad_touid', '', array(), array('return' => true));
                    return $this->makeErrorInfo($res, lang('message', 'message_bad_touid'));
                }
            } else {
                $topmuid = intval($_GET['topmuid']);
                $return = sendpm($topmuid, $subject, $message, '', $pmid, 0);
            }

        } elseif($users) {
            $newusers = $uidsarr = $membersarr = array();
            if($users) {
                $membersarr = C::t('common_member')->fetch_all_by_username($users);
                foreach($membersarr as $aUsername=>$aUser) {
                    $uidsarr[] = $aUser['uid'];
                }
            }
            if(empty($membersarr)) {
                showmessage('message_bad_touser', '', array(), array('return' => true));
            }
            if(isset($membersarr[$_G['uid']])) {
                showmessage('message_can_not_send_to_self', '', array(), array('return' => true));
            }

            friend_check($uidsarr);

            foreach($membersarr as $key => $value) {

                $value['onlyacceptfriendpm'] = $value['onlyacceptfriendpm'] ? $value['onlyacceptfriendpm'] : ($_G['setting']['onlyacceptfriendpm'] ? 1 : 2);
                if($_G['group']['allowsendallpm'] || $value['onlyacceptfriendpm'] == 2 || ($value['onlyacceptfriendpm'] == 1 && $_G['home_friend_'.$value['uid'].'_'.$_G['uid']])) {
                    $newusers[$value['uid']] = $value['username'];
                    unset($users[array_search($value['username'], $users)]);
                }
            }

            if(empty($newusers)) {
                showmessage('message_can_not_send_onlyfriend', '', array(), array('return' => true));
            }

            foreach($newusers as $key=>$value) {
                if(isblacklist($key)) {
                    showmessage('is_blacklist', '', array(), array('return' => true));
                }
            }
            $coef = count($newusers);
            $return = sendpm(implode(',', $newusers), $subject, $message, '', 0, 1, $type);
        } else {
            // showmessage('message_can_not_send_9', '', array(), array('return' => true));
            return $this->makeErrorInfo($res, lang('message', 'message_can_not_send_9'));
        }

        if($return > 0) {
            include_once libfile('function/stat');
            updatestat('sendpm', 0, $coef);

            C::t('common_member_status')->update($_G['uid'], array('lastpost' => TIMESTAMP));
            !($_G['group']['exempt'] & 1) && updatecreditbyaction('sendpm', 0, array(), '', $coef);
            if(!empty($newusers)) {
                if($type == 1) {
                    $returnurl = 'home.php?mod=space&do=pm&filter=privatepm';
                } else {
                    $returnurl = 'home.php?mod=space&do=pm';
                }
                showmessage(count($users) ? 'message_send_result' : 'do_success', $returnurl, array('users' => implode(',', $users), 'succeed' => count($newusers)));
            } else {
                if(!defined('IN_MOBILE')) {
                    // showmessage('do_success', 'home.php?mod=space&do=pm&subop=view&touid='.$touid, array('pmid' => $return), $_G['inajax'] ? array('msgtype' => 3, 'showmsg' => false) : array());
                } else {
                    // showmessage('do_success', 'home.php?mod=space&do=pm&subop=view'.(intval($_POST['touid']) ? '&touid='.intval($_POST['touid']) : ( intval($_POST['plid']) ? '&plid='.intval($_POST['plid']).'&daterange=1&type=1' : '' )));
                }
                $res = $this->makeErrorInfo($res, 'do_success', array('noError' => 1, 'alert' => 0));
                $msgInfo = uc_pm_viewnode($_G['uid'], $type, $return);
                $res['body']['plid'] = (int)$msgInfo['plid'];
                $res['body']['pmid'] = (int)$msgInfo['pmid'];
                $res['body']['sendTime'] = $msgInfo['dateline'].'000';
            }
        } else {
            if(in_array($return, range(-16, -1))) {
                // showmessage('message_can_not_send_'.abs($return));
                return $this->makeErrorInfo($res, lang('message', 'message_can_not_send_'.abs($return)));
            } else {
                // showmessage('message_can_not_send', '', array(), array('return' => true));
                return $this->makeErrorInfo($res, lang('message', 'message_can_not_send'));
            }
        }

        return $res;
    }

    private function _transMessage($msg) {
        $msgString = '';
        $msg['content'] = rawurldecode($msg['content']);
        switch ($msg['type']) {
            case 'text': 
                $msg['content'] = WebUtils::t($msg['content']);
                $msgString .= WebUtils::transMobcentPhiz($msg['content']);
                break;
            case 'image': $msgString .= sprintf('[img]%s[/img]', $msg['content']); break; 
            case 'audio': $msgString .= sprintf('[url=%s]%s[/url]', $msg['content'], 'audio'); break;
            default: break;
        }
        return $msgString;
    }
}