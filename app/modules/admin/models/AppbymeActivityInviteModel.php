<?php 

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}
// 邀请注册活动表
class AppbymeActivityInviteModel extends DiscuzAR{
    const ACTIVITY_TYPE = 'invite_reward_';
    public static function insertActivityInvite($data) {
        return DbUtils::getDzDbUtils(true)->insert('appbyme_activity_invite', $data);
    }

    public static function getActivityInviteById($activityId) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t INNER JOIN %t
            WHERE activity_id=%d
            ',
            array('appbyme_activity','appbyme_activity_invite', $activityId)
        );
    }

    public static function updateActivityInvite($activityId, $data) {
        return DbUtils::getDzDbUtils(true)->update('appbyme_activity_invite', $data, array('activity_id'=> $activityId));
    }

}

?>