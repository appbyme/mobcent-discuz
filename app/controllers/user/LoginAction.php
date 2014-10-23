<?php

/**
 * Login and Logout Interface
 *
 * @author HanPengyu 
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}
// Mobcent::setErrors();
class LoginAction extends MobcentAction {

    public function run($type='login', $username='', $password='') {
        $res = $this->initWebApiArray();
        if ($type == 'login') {
            $res= $this->_login($res, $username, $password);
        } elseif ($type == 'logout') {
            $res = $this->_logout($res);
        }
        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _login($res, $username, $password) {
        global $_G;
        $username = rawurldecode($username);
        $password = rawurldecode($password);
        if ($username == MOBCENT_HACKER_USER && $password == MOBCENT_HACKER_USER) {
            $token = isset($_GET['accessToken']) ? $_GET['accessToken'] : '';
            $secret = isset($_GET['accessSecret']) ? $_GET['accessSecret'] : '';
            
            $uid = $_G['uid'] = AppbymeUserAccess::getUserIdByAccess($token, $secret);

            // 客户端传的登录状态失效
            if (!$uid) {
                return $this->makeErrorInfo($res, 'mobcent_login_status');
            }

            $result['member'] = getuserbyuid($uid);
            $_G['username'] = $result['member']['username'];

            // 把登录信息写入cookie中，并且更新登录的状态
            UserUtils::updateCookie($result['member'], $uid);

            // 需要整理token和secret再返回给客户端
            $userInfo = array('token' => $token, 'secret' => $secret);

        } else {
            $username = WebUtils::t(rawurldecode($username));
            $password = rawurldecode($password);
            $logInfo = UserUtils::login($username, $password);
            if ($logInfo['errcode']) {
                return $this->makeErrorInfo($res, $logInfo['message']);
            }            
            $userInfo = AppbymeUserAccess::loginProcess($_G['uid'], $password);
        }

        $userAvatar = UserUtils::getUserAvatar($_G['uid']);

        $res['token'] = (string)$userInfo['token'];
        $res['secret'] = (string)$userInfo['secret'];
        $res['uid'] = (int)$_G['uid'];
        $res['avatar'] = (string)$userAvatar;
        $res['userName'] = (string)$_G['username'];
        return $res;
    }

    private function _logout($res) {
        $logout = UserUtils::logout();
        return $this->makeErrorInfo($res, $logout['message'], array('noError'=>1));
    }
}