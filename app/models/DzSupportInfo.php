<?php

/**
 * 赞接口 model类
 *
 * @author 徐少伟 <xushaowei@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class DzSupportInfo extends DiscuzAR {

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{forum_hotreply_member}}';
    }

    public function rules() {
        return array(
        );
    }

    public static function getSupportPostsByUidAndTid($uid, $tid)
    {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT pid
            FROM %t
            WHERE uid = %d
            AND tid = %d
            AND attitude = %d
            ',
            array('forum_hotreply_member', $uid, $tid, 1)
        );
    }

    public static function getSupportTopicByUidAndTid($uid, $tid)
    {
        return DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT t.recommend_add
            FROM %t m INNER JOIN %t t
            ON m.tid= t.tid
            WHERE m.recommenduid = %d
            AND m.tid = %d
            ',
            array('forum_memberrecommend', 'forum_thread', $uid, $tid)
        );
    }

    public static function getSupportTopicCount($tid)
    {
        return DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT recommend_add
            FROM %t
            WHERE tid = %d
            ',
            array('forum_thread', $tid)
        );
    }
}