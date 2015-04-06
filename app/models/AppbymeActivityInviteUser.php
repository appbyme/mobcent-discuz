<?php 

/**
 * 参加邀请注册活动Model
 *
 * @author 
 * @copyright 2012-2015 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}
// 邀请注册用户表
class AppbymeActivityInviteUser extends DiscuzAR {

    public static function insertUser($data) {
        return DbUtils::getDzDbUtils(true)->insert('appbyme_activity_invite_user', $data);
    }

    public static function getExchangeInfo($uid) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE uid=%d
            ',
            array('appbyme_activity_invite_user', $uid)
        );
    }

    // 设备是否兑换过
    public static function getExchangeInfoByDevice($device) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE device=%s

            ',
            array('appbyme_activity_invite_user', $device)
        );
    }

    // 验证兑换码
    public static function checkCode($code) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE exchange_num=%s
            ',
            array('appbyme_activity_invite_user', $code)
        );    
    }

    // 验证输入的验证码是否是自己的
    public static function getCheckByUidCode($uid, $code) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE uid=%d
            AND exchange_num=%s
            ',
            array('appbyme_activity_invite_user', $uid, $code)
        );        
    }
    // 兑换码验证成功之后
    public static function checkCodeSuccess($activityId, $code, $uid) {
        $config = ActivityUtils::getInviteConfig($activityId);

        $sql1 = 'UPDATE %t SET';
        $sql1 .= ' invite_count=invite_count+1,';
        $sql1 .= 'reward_sum=reward_sum+'.$config['invite_reward'].',';
        $sql1 .= 'available_reward=available_reward+'.$config['invite_reward'];
        $sql1 .= ' WHERE exchange_num=%s';

        $sql2 = 'UPDATE %t SET';
        $sql2 .= ' joining=1';
        // $sql2 .= 'reward_sum=reward_sum+'.$config['invite_reward'].',';
        // $sql2 .= 'available_reward=available_reward+'.$config['invite_reward'];
        $sql2 .= ' WHERE uid=%d';

        DbUtils::getDzDbUtils(true)->query($sql1, array('appbyme_activity_invite_user', $code));
        DbUtils::getDzDbUtils(true)->query($sql2, array('appbyme_activity_invite_user', $uid));
    }

    // 兑换
    public static function inviteExchange($uid, $data) {
        return DbUtils::getDzDbUtils(true)->update('appbyme_activity_invite_user', $data, array('uid'=> $uid));
    }

    // 执行兑换
    public function execExchange($uid, $exchangeNum) {
        $updateSql = 'UPDATE %t SET available_reward=available_reward-'.$exchangeNum.',';
        $updateSql .= 'exchange_status=0, exchange_type=%s';
        $updateSql .= ' WHERE uid=%d';
        return DbUtils::getDzDbUtils(true)->query($updateSql, array('appbyme_activity_invite_user', '', $uid));
    }

    // 标记用户
    public function flagUser($uid, $data) {
        return DbUtils::getDzDbUtils(true)->update('appbyme_activity_invite_user', $data, array('uid'=> $uid));
    }

    // 统计
    public static function search($type, $username, $page, $pageSize, $rootUrl) {

        $countArr = array('appbyme_activity_invite_user');
        $countSql = 'SELECT count(*) AS count FROM %t WHERE 1';
        if ($type !== 'all') {
            $countSql .= ' AND exchange_type=%s';
            $countArr[] = $type;
        }

        if ($username !== '') {
            $countSql .= ' AND username=%s';
            $countArr[] = $username;
        }
        $count = DbUtils::getDzDbUtils(true)->queryScalar($countSql, $countArr);

        $maxPage = (int)ceil($count/$pageSize);     

        if ($page <= 0) {
            $page = 1;
        }

        $offset = ($page - 1) * $pageSize;
        $searchArr = array('appbyme_activity_invite_user');
        $sql = 'SELECT * FROM %t WHERE 1';
        if ($type !== 'all') {
            $sql .= ' AND exchange_type=%s';
            $searchArr[] = $type;
        }

        if ($username !== '') {
            $sql .= ' AND username=%s';
            $searchArr[] = $username;
        }

        $sql .= ' LIMIT %d,%d';
        $searchArr[] = $offset;
        $searchArr[] = $pageSize;

        $list = DbUtils::getDzDbUtils(true)->queryAll($sql, $searchArr);

        $prev = ($page - 1) <= 0 ? 1 : $page-1;
        $next = ($page + 1) > $maxPage ? $maxPage : $page + 1;

        $prevPage = $rootUrl.'/mobcent/app/web/index.php?r=admin/reward/rewardcount&type='.$type.'&username='.$username.'&page='.$prev;
        $nextPage = $rootUrl.'/mobcent/app/web/index.php?r=admin/reward/rewardcount&type='.$type.'&username='.$username.'&page='.$next;

        return array('searchList' => $list, 'page' => $page, 'maxPage' => $maxPage, 'count' => $count, 'prev' => $prevPage, 'next' => $nextPage);
    }
}

?>