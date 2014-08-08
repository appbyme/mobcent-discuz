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
}