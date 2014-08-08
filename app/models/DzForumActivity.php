<?php

/**
 * forum_activity model类
 *
 * @author 谢建平 <xiejianping@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class DzForumActivity extends DiscuzAR {
    
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{forum_activity}}';
    }

    public function rules() {
        return array(
        );
    }

    public static function getActivityByTid($tid) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE tid=%d
            ', 
            array('forum_activity', $tid)
        );
    }

    // 更新活动报名的人数
    public static function updateApplyNumberByTid($tid) {
        return DbUtils::getDzDbUtils(true)->query('
            UPDATE %t
            SET applynumber=%d
            WHERE tid=%d  
            ',
            array('forum_activity', DzForumActivityApply::getCountByTid($tid), $tid)
        );
    }
}