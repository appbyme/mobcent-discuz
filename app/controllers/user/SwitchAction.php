<?php

/**
 * 马甲切换接口
 *
 * @author HanPengyu
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}
// Mobcent::setErrors();
class SwitchAction extends MobcentAction {

    public function run ($username) {
        $res = $this->initWebApiArray();
        $res = $this->_switchVest($res, $username);
        echo WebUtils::outputWebApi($res, '', false);    
    }

    private function _switchVest ($res, $username) {
        global $_G;
        $username = WebUtils::t(rawurldecode($username));
        $_GET['username'] = $username;

        $myrepeatsusergroups = (array)dunserialize($_G['cache']['plugin']['myrepeats']['usergroups']);
        if(!in_array($_G['groupid'], $myrepeatsusergroups)) {
            $users = C::t('#myrepeats#myrepeats')->fetch_all_by_username($_G['username']);
            if(!$users) {
                return $this->makeErrorInfo($res, lang('plugin/myrepeats', 'usergroup_disabled'));
            } else {
                $permusers = array();
                foreach($users as $user) {
                    $permusers[] = $user['uid'];
                }
                $member = C::t('common_member')->fetch_by_username($_GET['username']);
                if(!$member || !in_array($member['uid'], $permusers)) {
                    return $this->makeErrorInfo($res, lang('plugin/myrepeats', 'usergroup_disabled'));
                }
            }
        }

        $user = C::t('#myrepeats#myrepeats')->fetch_all_by_uid_username($_G['uid'], $_GET['username']);
        $user = current($user);
        $olddiscuz_uid = $_G['uid'];
        $olddiscuz_user = $_G['username'];
        $olddiscuz_userss = $_G['member']['username'];        

        if(!$user) {
            $newuid = C::t('common_member')->fetch_uid_by_username($_GET['username']);
            if(C::t('#myrepeats#myrepeats')->count_by_uid_username($newuid, $olddiscuz_userss)) {
                // 第一次登录，需要输入密码
            }
            return $this->makeErrorInfo($res, lang('plugin/myrepeats', 'user_nonexistence'));
        } elseif($user['locked']) {
            return $this->makeErrorInfo($res, lang('plugin/myrepeats', 'user_locked', array('user' => $_GET['username'])));
        }

        list($password, $questionid, $answer) = explode("\t", authcode($user['logindata'], 'DECODE', $_G['config']['security']['authkey']));

        $logInfo = UserUtils::login($username, $password);
        if ($logInfo['errcode']) {
            return $this->makeErrorInfo($res, $logInfo['message']);
        }
        
        $userInfo = AppbymeUserAccess::loginProcess($_G['uid'], $password);
        $userAvatar = UserUtils::getUserAvatar($_G['uid']);

        $res['token'] = (string)$userInfo['token'];
        $res['secret'] = (string)$userInfo['secret'];
        $res['uid'] = (int)$_G['uid'];
        $res['avatar'] = (string)$userAvatar;
        $res['userName'] = (string)$_G['username'];
        return $res;        
    }
}