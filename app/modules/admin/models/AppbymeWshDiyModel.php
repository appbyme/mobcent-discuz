<?php
/**
 * WSH model类
 *
 * @author HanPengyu
 * @copyright 2012-2015 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class AppbymeWshDiyModel extends DiscuzAR {

    public function tableName() {
        return '{{appbyme_service}}';
    }

    public static function allModule() {
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT *
            FROM %t
            ',
            array('appbyme_service')
        );
    }

    public static function getModuleById($id) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE id=%d
            ',
            array('appbyme_service', $id)
        );
    }

    public static function insertModule($data) {
        return DbUtils::getDzDbUtils(true)->insert('appbyme_service', $data);
    }

    public static function updateModule($mid, $data) {
        return DbUtils::getDzDbUtils(true)->update('appbyme_service', $data, array('id' => $mid));
    }

    public static function delModule($mid) {
        return DbUtils::getDzDbUtils(true)->delete('appbyme_service', array('id' => $mid));
    }


}