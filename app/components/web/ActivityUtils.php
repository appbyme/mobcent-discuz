<?php  
    
if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class ActivityUtils {

    // public static function checkInvite($config, $exchangeInfo) {
    public static function checkInvite($config, $uid, $device) {

        $res = array('rs' => 1, 'errcode' => '');
        // 活动没有开始
        if (time() < $config['start_time']) {
            return array('rs' => 0, 'errcode' => 'mobcent_activity_no_start');
        }

        // 活动已经结束
        if (time() > $config['stop_time']) {
            return array('rs' => 0, 'errcode' => 'mobcent_activity_end');
        }

        // 用户
        if ($config['limit_user']) {
            $userExchange = AppbymeActivityInviteUser::getExchangeInfo($uid);
            if ($userExchange['joining']) {
                return array('rs' => 0, 'errcode' => 'mobcent_invite_user_ed');
            }
        }

        // 设备
        if ($config['limit_device']) {
            $deviceExchange = AppbymeActivityInviteUser::getExchangeInfoByDevice($device);
            if ($deviceExchange['joining']) {
                return array('rs' => 0, 'errcode' => 'mobcent_invite_device_ed');
            }
        }

        return $res;
    }

    // 通过活动ID获取邀请注册配置
    public static function getInviteConfig($activityId) {
         $key = CacheUtils::getActivityInviteKey(array('invite', $activityId));
         $config = Yii::app()->cache->get($key);
         if( $config === false ) {
            $config = AppbymeActivityInviteModel::getActivityInviteById($activityId); 
            Yii::app()->cache->set($key, $config);
        }
        return $config;
    }

}

?>