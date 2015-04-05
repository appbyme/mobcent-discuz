<?php  

/**
 * 邀请注册验证兑换码
 *
 * @copyright 2012-2015 Appbyme
 */
if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class InviteCheckAction extends MobcentAction {
    public function run($code, $device='', $accessToken='', $accessSecret='', $activityId=1) {
        $res = $this->initWebApiArray();
        $res = $this->_inviteCheck($res, $code, $device, $accessToken, $accessSecret, $activityId);
        echo WebUtils::outputWebApi($res, '', false);
    }
 
    private function _inviteCheck($res, $code, $device, $accessToken, $accessSecret, $activityId) {
        global $_G;
        // 获取邀请注册活动的配置
        $config = ActivityUtils::getInviteConfig($activityId);

        // 验证是否能进行参数活动
        $checkInvite = ActivityUtils::checkInvite($config, $_G['uid'], $device);
        if ($checkInvite['rs'] == 0) {
            return $this->makeErrorInfo($res, $checkInvite['errcode']);
        }

        $isSelf = AppbymeActivityInviteUser::getCheckByUidCode($_G['uid'], $code);
        if ($isSelf) {
            // 输入的是自己的验证码
            return $this->makeErrorInfo($res, 'mobcent_check_code_self');
        }

        $checkCode = AppbymeActivityInviteUser::checkCode($code);

        if (!$checkCode) {
            // 兑换码验证失败
            return $this->makeErrorInfo($res, 'mobcent_check_code_self');
        }

        if ($checkCode) {
            AppbymeActivityInviteUser::checkCodeSuccess($activityId, $code, $_G['uid']);
        }
        return $res;
    }



}

?>