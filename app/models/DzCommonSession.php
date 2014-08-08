<?php

/**
 * 会员认证表 model类
 *
 * @author HanPengyu 
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class DzCommonSession extends DiscuzAR {
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{common_session}}';
    }

    public function rules() {
        return array(
        );
    }

    public static function getComSessByUid($uid) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE uid=%d
            ',
            array('common_session', $uid)
        );        
    }

    public static function insertComSess($data) {
        return DbUtils::getDzDbUtils(true)->insert('common_session', $data);
    }

    public static function delComSess($uid) {
        return DbUtils::getDzDbUtils(true)->delete('common_session', array('uid'=> $uid));
    }

}