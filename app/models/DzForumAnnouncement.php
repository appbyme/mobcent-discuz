<?php

/**
 * 公告model类
 *
 * @author 谢建平 <xiejianping@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class DzForumAnnouncement extends DiscuzAR {

    const TYPE_URL = 1;
    const TYPE_TEXT = 0;

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{forum_announcement}}';
    }

    public function rules() {
        return array(
        );
    }

    public static function getAnnouncements() {
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT * 
            FROM %t
            WHERE endtime > %d
            ORDER BY displayorder DESC, starttime DESC
            ',
            array('forum_announcement', time())
        );
    }

    public static function getAnnouncementByUid($uid) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT * 
            FROM %t 
            WHERE id = %d
            ',
            array('forum_announcement', $uid)
        );
    }
}