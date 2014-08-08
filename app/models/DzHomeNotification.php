<?php

/**
 * 通知model类
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class DzHomeNotification extends DiscuzAR {

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{home_notification}}';
    }

    public function rules() {
        return array(
        );
    }

    // public function attributeLabels() {
    //  return array(
    //  );
    // }

    public static function getCountByUid($uid, $type, $isNew = null) {
        return (int)DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT COUNT(*)
            FROM %t
            WHERE uid=%d AND type=%s ' .
            ($isNew !== null ? ' AND new=1 ' : ' ') . '
            ',
            array('home_notification', $uid, $type)
        );
    }

    public static function getAllNotifyByUid($uid, $type, $page = 1, $pageSize = 10, $isNew = null) {
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT *
            FROM %t
            WHERE uid=%d AND type=%s ' .
            ($isNew !== null ? ' AND new=1 ' : ' ') . '
            ORDER BY dateline DESC
            LIMIT %d, %d
            ',
            array('home_notification', $uid, $type, ($page-1)*$pageSize, $pageSize)
        );
    }

    public static function updateReadStatus($uid) {
        DbUtils::getDzDbUtils(true)->query('
            UPDATE %t
            SET new=0
            WHERE uid=%d AND new=1
            ',
            array('home_notification', $uid)
        );
    }
}