<?php

/**
 * 用户关系 model类
 *
 * @author 徐少伟 <xushaoweig@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class DzCommonUserList extends DiscuzAR {
    const TYPE_USER = 1;
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{home_friend}}';
    }

    public function rules() {
        return array(
        );
    }

    // 获取用户等级
    public static function getUserLevel($uid) {
        $icon = UserUtils::getUserLevelIcon($uid);
        return $icon['sun'] * 4 + $icon['moon'] * 2 + $icon['star'] * 1;
    }

    // 获取用户最后登录时间
    
/////////////////////////////////////////////////////////////////////
    // 查询用户关注好友的tid
    public static function _getFollowUsersDefault($uid, $page, $pageSize) {
        if ($page==0) {
            $sql = sprintf('
            SELECT followuid 
            FROM %%t
            WHERE uid=%%d AND status=0
            ORDER BY dateline DESC 
            ');
        }else{
            $sql = sprintf('
            SELECT followuid 
            FROM %%t
            WHERE uid=%%d AND status=0
            ORDER BY dateline DESC
            LIMIT %%d, %%d
            ');
        }
        return DbUtils::getDzDbUtils(true)->queryColumn(
            $sql,
            array('home_follow', $uid, $pageSize*($page-1), $pageSize)
        );
    }

    // 查询用户关注好友按最新注排序
    public static function _getFollowUsersByRegist($uid, $page, $pageSize) {
        if ($page==0) {
            $sql = sprintf('
            SELECT f.followuid
            FROM %%t f INNER JOIN %%t m 
            ON f.uid=m.uid
            WHERE f.uid=%%d
            AND f.status=0
            ORDER BY m.regdate DESC 
            ');
        }else{
            $sql = sprintf('
            SELECT f.followuid
            FROM %%t f INNER JOIN %%t m 
            ON f.uid=m.uid
            WHERE f.uid=%%d
            AND f.status=0
            ORDER BY m.regdate DESC
            LIMIT %%d, %%d
            ');
        }
        return DbUtils::getDzDbUtils(true)->queryColumn(
            $sql,
            array('home_follow', 'common_member', $uid, $pageSize*($page-1), $pageSize)
        );
    }

    // 查询用户关注好友按最后登陆排序
    public static function _getFollowUsersByLastVisit($uid, $page, $pageSize) {
        if ($page==0) {
            $sql = sprintf('
            SELECT f.followuid
            FROM %%t f INNER JOIN %%t m 
            ON f.uid=m.uid
            WHERE f.uid=%%d
            AND f.status=0
            ORDER BY m.lastvisit DESC 
            ');
        }else{
            $sql = sprintf('
            SELECT f.followuid
            FROM %%t f INNER JOIN %%t m 
            ON f.uid=m.uid
            WHERE f.uid=%%d
            AND f.status=0
            ORDER BY m.lastvisit DESC
            LIMIT %%d, %%d
            ');
        }
        return DbUtils::getDzDbUtils(true)->queryColumn(
            $sql,
            array('home_follow', 'common_member_status', $uid, $pageSize*($page-1), $pageSize)
        );
    }

    // 查询用户关注好友按最多粉丝排序
    public static function _getFollowUsersByFollower($uid, $page, $pageSize) {
        if ($page==0) {
            $sql = sprintf('
            SELECT f.followuid
            FROM %%t f INNER JOIN %%t m 
            ON f.uid=m.uid
            WHERE f.uid=%%d
            AND f.status=0
            ORDER BY m.follower DESC 
            ');
        }else{
            $sql = sprintf('
            SELECT f.followuid
            FROM %%t f INNER JOIN %%t m 
            ON f.uid=m.uid
            WHERE f.uid=%%d
            AND f.status=0
            ORDER BY m.follower DESC
            LIMIT %%d, %%d
            ');
        }
        return DbUtils::getDzDbUtils(true)->queryColumn(
            $sql,
            array('home_follow', 'common_member_count', $uid, $pageSize*($page-1), $pageSize)
        );
    }

    public static function _getFollowUsersByRange($uid, $page, $pageSize, $longitude, $latitude, $radius) {
        $range = self::_getRange($longitude, $latitude, $radius);
        $select = '*,' . self::_getSqlDistance($longitude, $latitude) . ' AS distance';
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT ' . $select . '
            FROM %t hsu INNER JOIN %t aus
            ON (hsu.object_id=aus.uid)
            INNER JOIN %t hf ON (aus.uid=hf.uid)
            WHERE hsu.type=%s
            AND hf.status = 0
            AND hsu.object_id!=%s
            AND hsu.longitude BETWEEN %s AND %s
            AND hsu.latitude BETWEEN %s AND %s
            AND aus.ukey=%s
            AND aus.uvalue=%s
            ORDER BY distance ASC
            LIMIT %d, %d
            ', 
            array(
                'home_surrounding_user',
                'appbyme_user_setting',
                'home_follow',
                self::TYPE_USER,
                $uid,
                $range['longitude']['min'],
                $range['longitude']['max'],
                $range['latitude']['min'],
                $range['latitude']['max'],
                AppbymeUserSetting::KEY_GPS_LOCATION,
                AppbymeUserSetting::VALUE_GPS_LOCATION_ON,
                ($page-1)*$pageSize, 
                $pageSize
            )
        );
    }

    // 查询用户关注好友的总数
    public static function _getFollowUsersCount($uid) {
        $count = DbUtils::getDzDbUtils(true)->queryRow('
            SELECT count(*) as num
            FROM %t
            WHERE uid=%d AND status=0
            ORDER BY dateline DESC
            ',
            array('home_follow', $uid)
        );
        return $count['num'];
    }

//////////////////////////////////////////////////////////////////////////
    // 获取用户粉丝默认排序
    public static function _getFollowedUsersDefault($uid, $page, $pageSize) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT uid
            FROM %t
            WHERE followuid=%d AND status=0
            ORDER BY dateline DESC 
            LIMIT %d, %d
            ',
            array('home_follow', $uid, $pageSize*($page-1), $pageSize)
        );
    }

    // 获取用户粉丝的详细信息按最新注册排序
    public static function _getFollowedUsersByRegist($uid, $page, $pageSize) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT f.uid
            FROM %t f INNER JOIN %t m 
            ON f.uid=m.uid
            WHERE f.followuid=%d
            AND f.status=0
            ORDER BY m.regdate DESC 
            LIMIT %d, %d
            ',
            array('home_follow', 'common_member', $uid, $pageSize*($page-1), $pageSize)
        );
    }

    // 获取用户粉丝的详细信息按最新登陆排序
    public static function _getFollowedUsersByLastVisit($uid, $page, $pageSize) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT f.uid
            FROM %t f INNER JOIN %t s 
            ON f.uid=s.uid
            WHERE f.followuid=%d
            AND f.status=0
            ORDER BY s.lastvisit DESC 
            LIMIT %d, %d
            ',
            array('home_follow', 'common_member_status', $uid, $pageSize*($page-1), $pageSize)
        );
    }

    // 获取用户粉丝的详细信息按最多粉丝排序
    public static function _getFollowedUsersByFollower($uid, $page, $pageSize) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT f.uid
            FROM %t f INNER JOIN %t c 
            ON f.uid=c.uid
            WHERE f.followuid=%d
            AND f.status=0
            ORDER BY c.follower DESC 
            LIMIT %d, %d
            ',
            array('home_follow', 'common_member_count', $uid, $pageSize*($page-1), $pageSize)
        );
    }

    // 获取用户粉丝的详细信息按距离排序
    public static function _getFollowedUsersByRange($uid, $page, $pageSize, $longitude, $latitude, $radius) {
        $range = self::_getRange($longitude, $latitude, $radius);
        $select = '*,' . self::_getSqlDistance($longitude, $latitude) . ' AS distance';
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT ' . $select . '
            FROM %t hsu INNER JOIN %t aus
            ON (hsu.object_id=aus.uid)
            INNER JOIN %t hf ON (aus.uid=hf.uid)
            WHERE hsu.type=%s
            AND hf.followuid=%d 
            AND hf.status = 0
            AND hsu.longitude BETWEEN %s AND %s
            AND hsu.latitude BETWEEN %s AND %s
            AND aus.ukey=%s
            AND aus.uvalue=%s
            ORDER BY distance ASC
            LIMIT %d, %d
            ', 
            array(
                'home_surrounding_user',
                'appbyme_user_setting',
                'home_follow',
                self::TYPE_USER,
                $uid,
                $range['longitude']['min'],
                $range['longitude']['max'],
                $range['latitude']['min'],
                $range['latitude']['max'],
                AppbymeUserSetting::KEY_GPS_LOCATION,
                AppbymeUserSetting::VALUE_GPS_LOCATION_ON,
                ($page-1)*$pageSize, 
                $pageSize
            )
        );
    }

    // 查询用户粉丝的总数
    public static function _getFollowedUsersCount($uid) {
        return DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT count(*) as num
            FROM %t
            WHERE followuid=%d AND status=0
            ',
            array('home_follow', $uid)
        );
    }
///////////////////////////////////////////////////////////////
    // 查询用户是否设置了关注
    public static function _getRecommendUsersSet($page, $pageSize) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT uid
            FROM %t
            WHERE status = 0
            ORDER BY displayorder ASC 
            LIMIT %d, %d
            ',
            array('home_specialuser', $pageSize*($page-1), $pageSize)
        );
    }

    // 用户推荐按默认排序
    public static function _getRecommendUsersSetByDefault($page, $pageSize) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT uid
            FROM %t
            WHERE status = 0
            ORDER BY displayorder ASC 
            LIMIT %d, %d
            ',
            array('home_specialuser', $pageSize*($page-1), $pageSize)
        );
    }

    // 用户推荐按最新注册排序
    public static function _getRecommendUsersSetByRegist($page, $pageSize) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT s.uid
            FROM %t s INNER JOIN %t m 
            ON s.uid=m.uid
            WHERE s.status = 0
            ORDER BY m.regdate DESC 
            LIMIT %d, %d
            ',
            array('home_specialuser', 'common_member', $pageSize*($page-1), $pageSize)
        );
    }

    // 用户推荐按最新登陆排序
    public static function _getRecommendUsersSetByLastVisit($page, $pageSize) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT s.uid
            FROM %t s INNER JOIN %t m 
            ON s.uid=m.uid
            WHERE s.status = 0
            ORDER BY m.lastvisit DESC
            LIMIT %d, %d
            ',
            array('home_specialuser', 'common_member_status', $pageSize*($page-1), $pageSize)
        );
    }

    // 用户推荐按最多粉丝排序
    public static function _getRecommendUsersSetByFollower($page, $pageSize) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT s.uid
            FROM %t s INNER JOIN %t m 
            ON s.uid=m.uid
            WHERE s.status = 0
            ORDER BY m.follower DESC
            LIMIT %d, %d
            ',
            array('home_specialuser', 'common_member_count', $pageSize*($page-1), $pageSize)
        );
    }

    // 用户推荐按距离排序
    public static function _getRecommendUsersSetByRange($uid, $page, $pageSize, $longitude, $latitude, $radius) {
        $range = self::_getRange($longitude, $latitude, $radius);
        $select = '*,' . self::_getSqlDistance($longitude, $latitude) . ' AS distance';
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT ' . $select . '
            FROM %t hsu INNER JOIN %t aus
            ON (hsu.object_id=aus.uid)
            INNER JOIN %t hs ON (aus.uid=hs.uid)
            WHERE hsu.type=%s
            AND hs.status = 0
            AND hsu.object_id!=%s
            AND hsu.longitude BETWEEN %s AND %s
            AND hsu.latitude BETWEEN %s AND %s
            AND aus.ukey=%s
            AND aus.uvalue=%s
            ORDER BY distance ASC
            LIMIT %d, %d
            ', 
            array(
                'home_surrounding_user',
                'appbyme_user_setting',
                'home_specialuser',
                self::TYPE_USER,
                $uid,
                $range['longitude']['min'],
                $range['longitude']['max'],
                $range['latitude']['min'],
                $range['latitude']['max'],
                AppbymeUserSetting::KEY_GPS_LOCATION,
                AppbymeUserSetting::VALUE_GPS_LOCATION_ON,
                ($page-1)*$pageSize, 
                $pageSize
            )
        );
    }

    // 用户设置了关注的用户数
    public static function _getRecommendUsersSetCount() {
        return DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT COUNT(*) as num
            FROM %t
            WHERE status = 0
            ',
            array('home_specialuser')
        );
    }

//////////////////////////////////////////////////////////////////
    // 用户好友列表默认排序
    public static function _getPostFuidListByDefault($uid) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT fuid
            FROM %t 
            WHERE uid = %d
            ',
            array('home_friend', $uid)
        );
    }

    // 用户好友按最新注册排序
    public static function _getPostFuidListByRegist($uid, $page, $pageSize) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT f.fuid
            FROM %t f INNER JOIN %t m
            ON f.fuid=m.uid
            WHERE f.uid=%d
            ORDER BY m.regdate DESC  
            LIMIT %d, %d
            ',
            array('home_friend', 'common_member', $uid, $pageSize*($page-1), $pageSize)
        );
    }

    // 用户好友按最新登陆排序
    public static function _getPostFuidListByLastVisit($uid, $page, $pageSize) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT f.fuid
            FROM %t f INNER JOIN %t m 
            ON f.fuid=m.uid
            WHERE f.uid=%d
            ORDER BY m.lastvisit DESC
            LIMIT %d, %d
            ',
            array('home_friend', 'common_member_status', $uid, $pageSize*($page-1), $pageSize)
        );
    }

    // 用户好友按最多粉丝排序
    public static function _getPostFuidListByFollower($uid, $page, $pageSize) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT f.fuid
            FROM %t f INNER JOIN %t m 
            ON f.fuid=m.uid
            WHERE f.uid=%d
            ORDER BY m.follower DESC
            LIMIT %d, %d
            ',
            array('home_friend', 'common_member_count', $uid, $pageSize*($page-1), $pageSize)
        );
    }

    // 用户好友列表按距离排序
    public static function _getPostFuidListByRange($uid, $page, $pageSize, $longitude, $latitude, $radius) {
        $range = self::_getRange($longitude, $latitude, $radius);
        $select = '*,' . self::_getSqlDistance($longitude, $latitude) . ' AS distance';
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT ' . $select . '
            FROM %t hsu INNER JOIN %t aus
            ON (hsu.object_id=aus.uid)
            INNER JOIN %t hf ON (aus.uid=hf.uid)
            WHERE hsu.type=%s
            AND hsu.object_id!=%s
            AND hsu.longitude BETWEEN %s AND %s
            AND hsu.latitude BETWEEN %s AND %s
            AND aus.ukey=%s
            AND aus.uvalue=%s
            ORDER BY distance ASC
            LIMIT %d, %d
            ', 
            array(
                'home_surrounding_user',
                'appbyme_user_setting',
                'home_friend',
                self::TYPE_USER,
                $uid,
                $range['longitude']['min'],
                $range['longitude']['max'],
                $range['latitude']['min'],
                $range['latitude']['max'],
                AppbymeUserSetting::KEY_GPS_LOCATION,
                AppbymeUserSetting::VALUE_GPS_LOCATION_ON,
                ($page-1)*$pageSize, 
                $pageSize
            )
        );
    }


    public static function getPostFuidListCount($uid) {
        return DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT count(*)
            FROM %t 
            WHERE uid = %d
            ',
            array('home_friend', $uid)
        );
    }
///////////////////////////////////////////////////////////// 
    // 获取用户未设置关注用户时的关注用户默认排序
    public static function _getRecommendUsersByDefault($uid, $page, $pageSize) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT uid 
            FROM %t 
            WHERE uid != %d
            ORDER BY credits DESC
            LIMIT %d, %d
            ',
            array('common_member', $uid, $pageSize*($page-1), $pageSize)
        );
    }

    // 所有用户按最新注册排序
    public static function _getRecommendUsersByRegist($uid, $page, $pageSize) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT uid 
            FROM %t 
            WHERE uid != %d
            ORDER BY regdate DESC
            LIMIT %d, %d
            ',
            array('common_member', $uid, $pageSize*($page-1), $pageSize)
        );
    }

    // 所有用户按最新登陆排序
    public static function _getRecommendUsersByLastVisit($uid, $page, $pageSize) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT m.uid
            FROM %t m INNER JOIN %t s
            ON m.uid=s.uid
            WHERE m.uid != %d
            ORDER BY s.lastvisit DESC
            LIMIT %d, %d
            ',
            array('common_member', 'common_member_status', $uid, $pageSize*($page-1), $pageSize)
        );
    }

    // 所有用户按最多粉丝排序
    public static function _getRecommendUsersByFollower($uid, $page, $pageSize) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT mem.uid
            FROM %t mem INNER JOIN %t count
            ON mem.uid=count.uid
            WHERE mem.uid != %d
            ORDER BY count.follower DESC
            LIMIT %d, %d
            ',
            array('common_member', 'common_member_count', $uid, $pageSize*($page-1), $pageSize)
        );
    }

    // 所有用户按距离排序
    public static function _getRecommendUsersByRange($uid, $page, $pageSize, $longitude, $latitude, $radius) {
        $range = self::_getRange($longitude, $latitude, $radius);
        $select = '*,' . self::_getSqlDistance($longitude, $latitude) . ' AS distance';
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT ' . $select . '
            FROM %t hsu INNER JOIN %t aus
            ON (hsu.object_id=aus.uid)
            INNER JOIN %t cm ON (aus.uid=cm.uid)
            WHERE hsu.type=%s 
            AND hsu.object_id!=%s
            AND hsu.longitude BETWEEN %s AND %s
            AND hsu.latitude BETWEEN %s AND %s
            AND aus.ukey=%s
            AND aus.uvalue=%s
            ORDER BY distance ASC
            LIMIT %d, %d
            ', 
            array(
                'home_surrounding_user',
                'appbyme_user_setting',
                'common_member',
                self::TYPE_USER,
                $uid,
                $range['longitude']['min'],
                $range['longitude']['max'],
                $range['latitude']['min'],
                $range['latitude']['max'],
                AppbymeUserSetting::KEY_GPS_LOCATION,
                AppbymeUserSetting::VALUE_GPS_LOCATION_ON,
                ($page-1)*$pageSize, 
                $pageSize
            )
        );
    }

    // 获取所有用户数
    public static function _getRecommendUsersCount($uid) {
        return DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT count(*) as num 
            FROM %t 
            WHERE uid != %d
            ',
            array('common_member', $uid)
        );
    }

    private static function _getRange($longitude, $latitude, $radius) {
        $lgRange = $radius * 180 / (EARTH_RADIUS * M_PI);
        $ltRange = $lgRange / cos($latitude * M_PI / 180);
        
        $range['longitude']['max'] = $longitude + $lgRange;
        $range['longitude']['min'] = $longitude - $lgRange;
        $range['latitude']['max'] = $latitude + $ltRange;
        $range['latitude']['min'] = $latitude - $ltRange;

        return $range;
    }

    private static function _getSqlDistance($longitude, $latitude) {
        return sprintf('SQRT(POW((%f-longitude)/0.012*1023,2)+POW((%f-latitude)/0.009*1001,2))', $longitude, $latitude);
    }

    public static function getUserLastVisit($uid) {
        return DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT lastvisit
            FROM %t 
            WHERE uid = %d
            ',
            array('common_member_status', $uid)
        );
    }

    public static function getUserLastRegdate($uid) {
        return DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT regdate
            FROM %t 
            WHERE uid = %d
            ',
            array('common_member', $uid)
        );
    }

    public static function getUserSightml($uid) {
        return DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT sightml
            FROM %t 
            WHERE uid = %d
            ',
            array('common_member_field_forum', $uid)
        );
    }
}