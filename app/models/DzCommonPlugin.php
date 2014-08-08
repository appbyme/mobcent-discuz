<?php

/**
 * 插件模块 model类
 *
 * @author 徐少伟 <xushaoweig@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class DzCommonPlugin extends DiscuzAR {

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{common_plugin}}';
    }

    public function rules() {
        return array(
        );
    }

    public static function isQQConnectionAvailable() {
        $count = (int)DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT COUNT(*)
            FROM %t
            WHERE identifier=%s
            AND available=%d
            ',
            array('common_plugin', 'qqconnect', 1)
        );
        return $count > 0;
    }

    public static function isDsuPaulsignAvailable() {
        $count = (int)DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT COUNT(*)
            FROM %t
            WHERE identifier=%s
            AND available=%d
        ',
            array('common_plugin', 'dsu_paulsign', 1)
        );
        return $count > 0;
    }
}