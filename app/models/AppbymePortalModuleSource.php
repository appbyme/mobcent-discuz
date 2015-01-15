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

    const SOURCE_TYPE_AID = 'aid';
    const SOURCE_TYPE_TID = 'tid';
    const SOURCE_TYPE_FID = 'fid';
    const SOURCE_TYPE_CATID = 'catid';
    const SOURCE_TYPE_URL = 'url';
    const SOURCE_TYPE_BID = 'bid';

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

    // 获得手动插入的信息
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

    // 获取添加的bid下面有多少条数据
    public static function getCountByBid($bid) {
        return (int)DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT count(*)
            FROM %t
            WHERE bid=%d
            AND idtype IN (%n)
            ',
            array('common_block_item', $bid, array('tid', 'aid'))
        );
    }

    // 获取 bid 下面的内容
    public static function getDataByBid($bid) {
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT id, idtype, title
            FROM %t
            WHERE bid=%d
            AND idtype IN (%n)
            ORDER BY displayorder ASC
            ',
            array('common_block_item', $bid, array('tid','aid'))
        );
    }

    public static function getSources($mid, $type=self::SOURCE_TYPE_NORMAL, $page=1, $pagesize=10, $params=array()) {
        $sqlParams = array('appbyme_portal_module_source', $mid, $type);
        $sql = '
            SELECT *
            FROM %t
            WHERE mid=%d AND type=%d
        ';
        if (is_array($params['idtype']) && $params['idtype']) {
            $sql .= ' AND idtype IN (%n) ';
            $sqlParams = array_merge($sqlParams, array($params['idtype']));
        }
        $sql .= ' ORDER BY displayorder ASC ';
        if ($page > 0 && $pagesize > 0) {
            $sql .= ' LIMIT %d, %d ';
            $sqlParams = array_merge($sqlParams, array(($page-1)*$pagesize, $pagesize));
        }
        
        return DbUtils::getDzDbUtils(true)->queryAll($sql, $sqlParams);
    }
}