<?php 
/**
 * 验证手机短信验证码接口
 *
 * @author HanPengyu
 * @copyright 2012-2015 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class CheckMobileCodeAction extends MobcentAction {
    public function run($mobile, $code) {
        $res = $this->initWebApiArray();
        $res = $this->_checkMobileCode($res, $mobile, $code);
        echo WebUtils::outputWebApi($res, '', true);
    }

    private function _checkMobileCode($res, $mobile, $code) {
        $checkInfo = UserUtils::checkMobileCode($res, $mobile, $code);
        if ($checkInfo['rs'] == 0) {
            return $this->makeErrorInfo($res, $checkInfo['errcode']);
        }
        return $res;
    }
}

?>