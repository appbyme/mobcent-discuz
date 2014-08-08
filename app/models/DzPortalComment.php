<?php

/**
 * 门户评论 model类
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class DzPortalComment extends DiscuzAR {

    const TYPE_COMMENT_AID = 'aid';         // 文章
    const TYPE_COMMENT_TOPICID = 'topicid'; // 专题

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{portal_comment}}';
    }

    public function rules() {
        return array(
        );
    }

    public static function getCount($id, $idType=self::TYPE_COMMENT_AID) {
        return (int)DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT COUNT(*)
            FROM %t
            WHERE id=%d AND idtype=%s AND status=0
            ',
            array('portal_comment', $id, $idType)
        );
    }

    public static function getComments($id, $idType=self::TYPE_COMMENT_AID, $page=1, $pageSize=10, $params=array()) {
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT *
            FROM %t
            WHERE id=%d AND idtype=%s AND status=0
            ORDER BY dateline DESC
            LIMIT %d, %d
            ',
            array('portal_comment', $id, $idType, $pageSize*($page-1), $pageSize)
        );
    }

    public static function getCommentById($id) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE cid=%d
            LIMIT 1
            ',
            array('portal_comment', $id)
        );
    }
}