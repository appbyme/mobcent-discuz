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

    public function run($username, $password, $email, $mobile='', $code='', $isValidation=0) {
        $username = WebUtils::t(rawurldecode($username));
        $password = rawurldecode($password);
        $email = rawurldecode($email);
        $res = $this->initWebApiArray();
        $res = $this->_register($res, $username, $password, $email, $mobile, $code, $isValidation);
        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _register($res, $username, $password, $email, $mobile, $code, $isValidation) {
        if ($isValidation) {
            // 是否开启注册手机验证
            $isRegisterValidation = WebUtils::getDzPluginAppbymeAppConfig('mobcent_register_validation');
            if ($isRegisterValidation) {
                $checkInfo = UserUtils::checkMobileCode($res, $mobile, $code);
                if ($checkInfo['rs'] == 0) {
                    return $this->makeErrorInfo($res, $checkInfo['errcode']);
                }
            }   
        }

        $regInfo = UserUtils::register($username, $password, $email);
        if ($regInfo['errcode']) {
            return $this->makeErrorInfo($res, $regInfo['message']);
        }

        if ($isValidation) {
            if ($isRegisterValidation) {
                // 注册完毕之后更新手机验证信息
                $updataArr = array('uid' => $regInfo['info']['uid']);
                AppbymeSendsms::updateMobile($mobile, $updataArr);
            }       
        }

        $userInfo = AppbymeUserAccess::registerProcess($regInfo['info']['uid'], $password);
        $res['token'] = (string)$userInfo['token'];
        $res['secret'] = (string)$userInfo['secret'];
        $res['uid'] = (int)$regInfo['info']['uid'];
        return $res;
    }
}