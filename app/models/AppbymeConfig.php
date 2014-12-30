<?php

/**
 * 安米插件配置model
 *
 * @author 徐少伟 <xushaowei@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class AppbymeConfig extends DiscuzAR {

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{appbyme_config}}';
    }

    public function rules() {
        return array(
        );
    }

    public static function getDownloadOptions() {
        $data = DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT cvalue
            FROM %t
            WHERE ckey = %s
            ',
            array('appbyme_config', 'app_download_options')
        );
        return $data ? (array)unserialize($data) : array();
    }

    public static function saveDownloadOptions($appInfo)
    {
        $appDownloadOptions = array('ckey' => 'app_download_options', 'cvalue' => serialize($appInfo));
        $tempData = DB::fetch_first("SELECT * FROM ".DB::table('appbyme_config')." WHERE ckey='app_download_options'");
        if (empty($tempData)) {
            DB::insert('appbyme_config', $appDownloadOptions);
        } else {
            DB::update('appbyme_config', $appDownloadOptions, array('ckey' => 'app_download_options'));
        }
    }

    public static function saveForumkey($forumKey)
    {
        $appForumKey = array('ckey' => 'app_forumkey', 'cvalue' => $forumKey);
        $tempData = DB::fetch_first("SELECT * FROM ".DB::table('appbyme_config')." WHERE ckey='app_forumkey'");
        if (empty($tempData)) {
            DB::insert('appbyme_config', $appForumKey);
        } else {
            DB::update('appbyme_config', $appForumKey, array('ckey' => 'app_forumkey'));
        }
    }

    public static function getForumkey() {
        return (string)DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT cvalue
            FROM %t
            WHERE ckey = %s
            ',
            array('appbyme_config', 'app_forumkey')
        );
    }
    
    public static function setAPNsCertfilePassword($password) {
        $key = 'certfile_apns_passphrase';
        $data = array(
            'ckey' => $key, 
            'cvalue' => base64_encode($password),
        );
        $tempData = DbUtils::getDzDbUtils(true)->queryRow('
            SELECT * 
            FROM %t 
            WHERE ckey=%s
            ',
            array('appbyme_config', $key)
        );
        if (empty($tempData)) {
            DbUtils::getDzDbUtils(true)->insert('appbyme_config', $data);
        } else {
            DbUtils::getDzDbUtils(true)->update('appbyme_config', $data, array('ckey' => $key));
        }
    }

    public static function getAPNsCertfilePassword() {
        $data = DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT cvalue
            FROM %t
            WHERE ckey = %s
            ',
            array('appbyme_config', 'certfile_apns_passphrase')
        );
        return (string)base64_decode($data);
    }
}