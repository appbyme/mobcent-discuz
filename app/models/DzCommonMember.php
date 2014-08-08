<?php

/**
 * 用户model类
 *
 * @author HanPengyu
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class DzCommonMember extends DiscuzAR {

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{common_member}}';
    }

    public function rules() {
        return array(
        );
    }

    public static function getUidByUsername($username) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE username=%s
            ',
            array('common_member', $username)
        );
    }    

    public static function updateMember($data, $condition) {
        return DbUtils::getDzDbUtils(true)->update('common_member', $data, $condition);
    }

}