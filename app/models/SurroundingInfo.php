<?php

/**
 * 周边信息 model类
 *
 * @author 谢建平 <xiejianping@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class SurroundingInfo extends DiscuzAR {

    const TYPE_USER = 1;
    const TYPE_POST = 2;
    const TYPE_TOPIC = 3;

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{home_surrounding_user}}';
    }

    public function rules() {
        return array(
        );
    }

    // public function attributeLabels() {
    //  return array(
    //  );
    // }

    public static function getUserCountByUid($uid, $longitude, $latitude, $radius) {
        $range = self::_getRange($longitude, $latitude, $radius);
        return (int)DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT COUNT(*)
            FROM %t hsu
            INNER JOIN %t aus
            ON hsu.object_id=aus.uid
            WHERE hsu.type=%s 
            AND hsu.object_id!=%s
            AND hsu.longitude BETWEEN %s AND %s
            AND hsu.latitude BETWEEN %s AND %s
            AND aus.ukey=%s
            AND aus.uvalue=%s
            ', 
            array(
                'home_surrounding_user',
                'appbyme_user_setting',
                self::TYPE_USER,
                $uid,
                $range['longitude']['min'],
                $range['longitude']['max'],
                $range['latitude']['min'],
                $range['latitude']['max'],
                AppbymeUserSetting::KEY_GPS_LOCATION,
                AppbymeUserSetting::VALUE_GPS_LOCATION_ON
            )
        );
    }

    public static function getAllUsersByUid($uid, $longitude, $latitude, $radius, 
                                            $page=1, $pageSize=10) {
        $range = self::_getRange($longitude, $latitude, $radius);
        $select = '*, ' . self::_getSqlDistance($longitude, $latitude) . ' AS distance';
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT ' . $select . '
            FROM %t hsu
            INNER JOIN %t aus
            ON hsu.object_id=aus.uid
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
    
    public static function getTopicCountByTid($uid, $longitude, $latitude, $radius) {
        $range = self::_getRange($longitude, $latitude, $radius);
        return (int)DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT COUNT(*)
            FROM %t hsu
            INNER JOIN %t ft
            ON hsu.object_id=ft.tid
            INNER JOIN %t aus
            ON ft.authorid=aus.uid
            WHERE hsu.type=%s 
            AND hsu.longitude BETWEEN %s AND %s
            AND hsu.latitude BETWEEN %s AND %s
            AND aus.ukey=%s
            AND aus.uvalue=%s
            AND ft.displayorder>=%s
            AND ft.authorid!=%s
            ', 
            array(
                'home_surrounding_user',
                'forum_thread',
                'appbyme_user_setting',
                self::TYPE_TOPIC,
                $range['longitude']['min'],
                $range['longitude']['max'],
                $range['latitude']['min'],
                $range['latitude']['max'],
                AppbymeUserSetting::KEY_GPS_LOCATION,
                AppbymeUserSetting::VALUE_GPS_LOCATION_ON,
                DzForumThread::DISPLAY_ORDER_NORMAL, 
                $uid
            )
        );
    }
    
    public static function getAllTopicsByTid($uid, $longitude, $latitude, $radius,
                                             $page=1, $pageSize=10) {
        $range = self::_getRange($longitude, $latitude, $radius);
        $select = '*, ' . self::_getSqlDistance($longitude, $latitude) . ' AS distance ';
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT ' . $select . '
            FROM %t hsu
            INNER JOIN %t ft
            ON hsu.object_id=ft.tid
            INNER JOIN %t aus
            ON ft.authorid=aus.uid
            WHERE hsu.type=%s 
            AND hsu.longitude BETWEEN %s AND %s
            AND hsu.latitude BETWEEN %s AND %s
            AND aus.ukey=%s
            AND aus.uvalue=%s
            AND ft.displayorder>=%s
            AND ft.authorid!=%s
            ORDER BY distance ASC
            LIMIT %d, %d
            ', 
            array(
                'home_surrounding_user',
                'forum_thread',
                'appbyme_user_setting',
                self::TYPE_TOPIC,
                $range['longitude']['min'],
                $range['longitude']['max'],
                $range['latitude']['min'],
                $range['latitude']['max'],
                AppbymeUserSetting::KEY_GPS_LOCATION,
                AppbymeUserSetting::VALUE_GPS_LOCATION_ON,
                DzForumThread::DISPLAY_ORDER_NORMAL, 
                $uid,
                ($page-1)*$pageSize, $pageSize
            )
        );
    }
    
    public static function getLocationById($id, $type) {
        $location = DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT location
            FROM %t
            WHERE object_id=%d AND type=%s
        ', 
            array('home_surrounding_user', $id, $type)
        );
        return $location !== false ? $location : '';
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

    public static function saveUserLocation($uid, $longitude, $latitude, $location) {
        $count = (int)DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT COUNT(*) 
            FROM %t
            WHERE object_id=%d
            AND type=%d
            ',
            array('home_surrounding_user', $uid, self::TYPE_USER)
        );
        $data = array(
            'longitude' => $longitude,
            'latitude' => $latitude,
            'location' => $location,
            'type' => self::TYPE_USER,
            'object_id' => $uid,
        );
        if (!$count) {
            DbUtils::getDzDbUtils(true)->insert('home_surrounding_user', $data);
        } else {
            DbUtils::getDzDbUtils(true)->update('home_surrounding_user', $data,
                sprintf("object_id=%d AND type=%d", $uid, self::TYPE_USER)
            );
        }
        return true;
    }
}