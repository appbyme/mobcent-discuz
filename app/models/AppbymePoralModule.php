<?php

/**
 * 门户模块 model类
 *
 * @author HanPengyu
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class AppbymePoralModule extends DiscuzAR {

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{appbyme_portal_module}}';
    }

    public function rules() {
        return array(
        );
    }

    public static function getModuleList() {
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT *
            FROM %t
            ORDER BY displayorder ASC
            ',
            array('appbyme_portal_module')
        );
    }

    // 
    public static function getModuleParam($mid) {
        return DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT param
            FROM %t
            WHERE mid=%d
            ',
            array('appbyme_portal_module', $mid)
        );
    }

}