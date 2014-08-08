<?php

/**
 * Registe Interface
 *
 * @author HanPengyu 
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}
// Mobcent::setErrors();
class RegisterAction extends MobcentAction{

    public function run($username, $password, $email) {
        $username = WebUtils::t(rawurldecode($username));
        $password = rawurldecode($password);
        $res = $this->initWebApiArray();
        $res = $this->_register($res, $username, $password, $email);
        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _register($res, $username, $password, $email) {
        $regInfo = UserUtils::register($username, $password, $email);
        if ($regInfo['errcode']) {
            return $this->makeErrorInfo($res, $regInfo['message']);
        }
        $userInfo = AppbymeUserAccess::registerProcess($regInfo['info']['uid'], $password);
        $res['token'] = (string)$userInfo['token'];
        $res['secret'] = (string)$userInfo['secret'];
        $res['uid'] = (int)$regInfo['info']['uid'];
        return $res;
    }
}