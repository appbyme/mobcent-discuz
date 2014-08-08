<?php

/**
 * 门户模块数据 model类
 *
 * @author HanPengyu
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class AppbymePortalModuleSource extends DiscuzAR {

    const SOURCE_TYPE_NORMAL = 1;
    const SOURCE_TYPE_SLIDER = 2;

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{appbyme_portal_module_source}}';
    }

    public function rules() {
        return array(
        );
    }

    // 通过mid来获取数据
    public static function getPortalByMid($mid, $type=self::SOURCE_TYPE_NORMAL){
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT *
            FROM %t
            WHERE mid=%d
            AND type=%d
            ORDER BY displayorder ASC
            ',
            array('appbyme_portal_module_source', $mid, $type)
        );      
    }

    // 查询手动插入条数
    public static function getHandCount($mid) {
        return (int)DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT count(*)
            FROM %t
            WHERE mid=%d 
            AND type=%d
            AND idtype IN (%n)
            ',
            array('appbyme_portal_module_source', $mid, self::SOURCE_TYPE_NORMAL, array('tid','aid'))
        );
    }

    // 获得自动插入的信息
    public static function getAutoAdd($mid) {
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT *
            FROM %t
            WHERE mid=%d
            AND type=%d
            AND idtype IN (%n)
            ORDER BY displayorder ASC
            ',
            array('appbyme_portal_module_source', $mid, self::SOURCE_TYPE_NORMAL, array('fid','catid'))
        );          
    }

    // 获得自动插入的信息
    public static function getHandData($mid, $offset, $limit) {
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT *
            FROM %t
            WHERE mid=%d 
            AND type=%d
            AND idtype IN (%n)
            ORDER BY displayorder ASC
            LIMIT %d,%d
            ',
            array('appbyme_portal_module_source', $mid, self::SOURCE_TYPE_NORMAL, array('tid','aid'), $offset, $limit)
        );          
    }

}