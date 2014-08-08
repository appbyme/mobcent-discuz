<?php

/**
 * 用户设置 model类
 *
 * @author 谢建平 <xiejianping@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class AppbymeUserSetting extends DiscuzAR {

    const KEY_GPS_LOCATION = 'hidden';
    const VALUE_GPS_LOCATION_ON = 0;
    const VALUE_GPS_LOCATION_OFF = 1;

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{appbyme_user_setting}}';
    }

    public function rules() {
        return array(
            array('uid, ukey, uvalue', 'safe'),
        );
    }

    // public function attributeLabels() {
    //  return array(
    //  );
    // }

    /**
     * saveNewSetting
     * 
     * @param int $uid.
     * @param array $settings.
     * @param bool $return.
     *
     * @return bool|array.
     */
    public static function saveNewSettings($uid, $settings, $return = false) {
        // save new settings
        foreach ($settings as $key => $value) {
            $config = DbUtils::getDzDbUtils(true)->queryRow('
                SELECT * 
                FROM %t
                WHERE uid=%d
                AND ukey=%s
                ',
                array('appbyme_user_setting', $uid, $key)
            );
            if (empty($config)) {
                DbUtils::getDzDbUtils(true)->insert('appbyme_user_setting', array(
                    'uid' => $uid,
                    'ukey' => $key,
                    'uvalue' => $value,
                ));
            } else {
                DbUtils::getDzDbUtils(true)->update('appbyme_user_setting', array(
                    'uvalue' => $value,
                ), sprintf("uid=%d AND ukey='%s'", $uid, $key));
            }
        }

        if (!$return)
            return true;

        // return user settings
        $newSettings = array();
        $configs = DbUtils::getDzDbUtils(true)->queryAll('
            SELECT * 
            FROM %t
            WHERE uid=%d
        ',
            array('appbyme_user_setting', $uid)
        );
        foreach ($configs as $config) {
            $newSettings[$config['ukey']] = $config['uvalue'];
        }

        return $newSettings;
    }

    public static function isGPSLocationOn($uid) {
        $config = DbUtils::getDzDbUtils(true)->queryRow('
            SELECT * 
            FROM %t
            WHERE uid=%d
            AND ukey=%s
        ',
            array('appbyme_user_setting', $uid, self::KEY_GPS_LOCATION)
        );
        return !(!empty($config) && $config['uvalue'] == 1);
    }
}