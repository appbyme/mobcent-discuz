<?php

/**
 * 版块model类
 *
 * @author 谢建平 <xiejianping@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class DzForumForum extends DiscuzAR {

    const FORUM_TYPE_GROUP = 'group';
    const FORUM_TYPE_FORUM = 'forum';
    const FORUM_TYPE_SUB_FORUM = 'sub';

    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{forum_forum}}';
    }

    public function rules() {
        return array(
        );
    }

    // public function attributeLabels() {
    //  return array(
    //  );
    // }

    public static function getNameByFid($fid) {
        return DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT name
            FROM %t
            WHERE fid=%d
            ', 
            array('forum_forum', $fid)
        );
    }

    /**
     * 获取版块信息
     */
    public static function getForumInfos($fids) {
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT *
            FROM %t
            WHERE fid IN (%n)
            ',
            array('forum_forum', $fids)
        );
    }

    // 获取版块分区
    public static function getForumGroups() {
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT *
            FROM %t
            WHERE type=%s AND status=%d
            ORDER BY displayorder ASC
            ',
            array('forum_forum', 'group', 1)
        );
    }

    // 获取分区下的版块列表(不包括子版块)
    public static function getForumsByGid($gid) {
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT *
            FROM %t
            WHERE type=%s AND status=%d AND fup=%d
            ORDER BY displayorder ASC
            ',
            array('forum_forum', 'forum', 1, $gid)
        );
    }

    // 获取子版块列表
    public static function getSubForumsByFid($fid) {
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT *
            FROM %t
            WHERE type=%s AND status=%d AND fup=%d
            ',
            array('forum_forum', 'sub', 1, $fid)
        );
    }

    private static function _getForumByFid($fid) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE fid=%d
            ',
            array('forum_forum', $fid)
        );
    }

    // 找到某个版块所属的groupid
    public static function getGidByFid($fid) {
        $gid = 0;
        if (($forum = self::_getForumByFid($fid)) !== false) {
            if ($forum['type'] == 'forum') {
                $gid = $forum['fup'];
            } else if ($forum['type'] == 'sub') {
                $gid = self::getGidByFid($forum['fup']);
            }
        }
        return (int)$gid;
    }

    // 获取某个分区下的所有fids
    public static function getFidsByGid($gid) {
        $fids = self::getSubFidsByFid($gid);
        foreach ($fids as $fid) {
            $fids = array_merge($fids, self::getSubFidsByFid($fid));
        }
        return $fids;
    }

    // 获取所有可用fids
    public static function getFids() {
        $groupIds = self::getGids();
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT fid
            FROM %t
            WHERE type IN (%n) AND status=%d AND fup IN (%n)
            ', 
            array(
                'forum_forum', 
                array(self::FORUM_TYPE_FORUM, self::FORUM_TYPE_SUB_FORUM),
                1, $groupIds
            )
        );
    }

    // 获取所有可用gids
    public static function getGids() {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT fid
            FROM %t
            WHERE type=%s AND status=%d
            ', 
            array('forum_forum', self::FORUM_TYPE_GROUP, 1)
        );
    }

    // 获取某个版块的子版块
    public static function getSubFidsByFid($fid) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT fid
            FROM %t
            WHERE fup=%s
            ',
            array('forum_forum', $fid)
        );
    }

    // 获取版块扩展信息
    public static function getForumFieldByFid($fid) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE fid=%d
            ', 
            array('forum_forumfield', $fid)
        );
    }
}