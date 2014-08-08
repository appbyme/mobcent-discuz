<?php

/**
 * forum_activityapply model类
 *
 * @author 谢建平 <xiejianping@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class DzForumActivityApply extends DiscuzAR {
    
    const STATUS_VERIFIED_NO = 0;
    const STATUS_VERIFIED_YES = 1;
    const STATUS_VERIFIED_IMPROVE = 2;

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{forum_activityapply}}';
    }

    public function rules() {
        return array(
        );
    }

    public static function getCountByTid($tid) {
        return (int)DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT COUNT(*)
            FROM %t
            WHERE tid=%s AND verified=%s 
        ',
            array('forum_activityapply', $tid, self::STATUS_VERIFIED_YES)
        );
    }

    public static function getApplyByTidUid($tid, $uid) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE tid=%s AND uid=%s
            ', 
            array('forum_activityapply', $tid, $uid)
        );
    }

    public static function updateApplyById($data, $id) {
        return DbUtils::getDzDbUtils(true)->update(
            'forum_activityapply', 
            $data, 
            'applyid='.$id
        );
    }

    public static function insertApply($data) {
        return DbUtils::getDzDbUtils(true)->insert('forum_activityapply', $data);
    }

    public static function deleteByTidUid($tid, $uid) {
        return DbUtils::getDzDbUtils(true)->query('
            DELETE FROM %t
            WHERE tid=%s AND uid=%s
            ', 
            array('forum_activityapply', $tid, $uid)
        );
    }
}