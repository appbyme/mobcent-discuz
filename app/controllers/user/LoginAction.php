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

    public function run($type='login', $username='', $password='', $mobile='', $code='') {
        $res = $this->initWebApiArray();
        if ($type == 'login') {
            $res= $this->_login($res, $username, $password, $mobile, $code);
        } elseif ($type == 'logout') {
            $res = $this->_logout($res);
        }
        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _login($res, $username, $password, $mobile, $code) {
        global $_G;
        $username = rawurldecode($username);
        $password = rawurldecode($password);
        if ($username == MOBCENT_HACKER_USER && $password == MOBCENT_HACKER_PASSWORD) {
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
            $username = WebUtils::t($username);
            $logInfo = UserUtils::login($username, $password);
            if ($logInfo['errcode']) {
                UserUtils::delUserAccessByUsername($username);
                return $this->makeErrorInfo($res, $logInfo['message']);
            }

            // 是否开启了登录手机验证
            $isLoginValidation = WebUtils::getDzPluginAppbymeAppConfig('mobcent_login_validation');
            if ($isLoginValidation) {
                $userMobileBind = AppbymeSendsms::getBindInfoByUid($_G['uid']);
                if (!$userMobileBind) { // 当前登录的用户没有绑定手机号码

                    if ($mobile == '' && $code == '') {
                        $res['isValidation'] = 1;
                        return $this->makeErrorInfo($res, '', array('noError' => 0, 'alert' => 0));
                    }

                    $checkInfo = UserUtils::checkMobileCode($res, $mobile, $code);
                    if ($checkInfo['rs'] == 0) {
                        return $this->makeErrorInfo($res, $checkInfo['errcode']);
                    }

                    $updataArr = array('uid' => $_G['uid']);
                    AppbymeSendsms::updateMobile($mobile, $updataArr);             
                }
            }


            $userInfo = AppbymeUserAccess::loginProcess($_G['uid'], $password);
        }

        $userAvatar = UserUtils::getUserAvatar($_G['uid']);

        $res['isValidation'] = 0;
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