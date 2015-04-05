<?php 

/**
 * 邀请注册兑换
 *
 * @copyright 2012-2015 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

// Mobcent::setErrors();
class InviteExchangeAction extends MobcentAction {

    public function run($type, $mobile='', $activityId=1) {
        $res = $this->initWebApiArray();
        $res = $this->_inviteExchange($res, $mobile, $type, $activityId);
        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _inviteExchange($res, $mobile, $type, $activityId) {

        global $_G;
        $config = ActivityUtils::getInviteConfig($activityId);

        // 是否结束
        if (time() > $config['stop_time']) {
            return $this->makeErrorInfo($res, 'mobcent_activity_end');
        }

        // 兑换金额是否超过最低兑换值
        $exchangeInfo = AppbymeActivityInviteUser::getExchangeInfo($_G['uid']);
        if ($exchangeInfo['available_reward'] < $config['exchange_min']) {
            return $this->makeErrorInfo($res, 'mobcent_exchange_min');
        }

        if (!in_array($type, array('mobile', 'forum'))) {
            return $this->makeErrorInfo($res, 'mobcent_exchange_type_error');
        }
        $exchange = array('exchange_type' => $type, 'mobile' => $mobile, 'exchange_status' => 1);
        $excInfo = AppbymeActivityInviteUser::inviteExchange($_G['uid'], $exchange);

        if (!$excInfo) {
            return $this->makeErrorInfo($res, 'mobcent_exchange_error');   
        }
        return $res;
    }

}

?>