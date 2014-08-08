<?php

/**
 * 用户中心发表、回复和关注帖子接口
 *
 * @author 徐少伟 <xushaowei@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class TopicListAction extends CAction {

    public function run() {
        $res = WebUtils::initWebApiArray_oldVersion();

        $uid = $_GET['uid'];
        $page = $_GET['page'] ? $_GET['page'] : 1;
        $pageSize = $_GET['pageSize'] ? $_GET['pageSize'] : 10;
        $type = $_GET['type'];
        $idType = $_GET['idType'] ? $_GET['idType'] : 'tid';

        $res = $this->_getTopicList($res, $type, $idType, $uid, $page, $pageSize, $idtype);

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _getTopicList($res, $type, $idType, $uid, $page, $pageSize, $idtype) {
        $res['list'] = $this->_getUserTopicList($type, $idType, $uid, $page, $pageSize);
        $count = $this->_getUserTopicListCount($type, $idType, $uid, $idtype);
        $res = WebUtils::getWebApiArrayWithPage_oldVersion($page, $pageSize, $count, $res);
        return $res;
    }

    // 获取发表、回复和关注帖子列表
    private function _getUserTopicList($type, $idType, $uid, $page, $pageSize) {
        $topicList = array();
        switch ($type) {
            case 'topic':
                $topicList = UserTopicInfo::getTopicsByUid($uid, $page, $pageSize);
                break;
            case 'reply':
                $topicList = UserTopicInfo::getTopicsByUidWithPost($uid, $page, $pageSize);
                break;
            case 'favorite':
                $topicList = UserTopicInfo::getUserfavoriteByTid($uid, $idType, $page, $pageSize);
                break;
            default:
                break;
        }
        $topicList = $this->_transTopicList($topicList, $type);
        $topicList = $this->getClassificationInfo($topicList);
        return $topicList;
    }

    // 获取发表、回复和关注帖子详细信息
    private function _transTopicList($topicList) {
        $list = array();

        global $_G;
        $forum = $_G['forum'];
        foreach ($topicList as $topic) {
            $tmpTopicInfo = ForumUtils::getTopicInfo((int)$topic);
            $topicSummary = ForumUtils::getTopicSummary((int)$topic);
            $topicInfo['board_id'] = (int)$tmpTopicInfo['fid'];
            $topicInfo['board_name'] = $fid != 0 ? $forum['name'] : ForumUtils::getForumName($tmpTopicInfo['fid']);
            $topicInfo['board_name'] = WebUtils::emptyHtml($topicInfo['board_name']);
            $topicInfo['topic_id'] = (int)$topic;
            $topicInfo['type_id'] = (int)$tmpTopicInfo['typeid'];
            $topicInfo['sort_id'] = (int)$tmpTopicInfo['sortid'];
            $topicInfo['title'] = WebUtils::emptyHtml($tmpTopicInfo['subject']);
            $topicSummary['msg'] = WebUtils::emptyReturnLine($topicSummary['msg'], ' ');
            $topicInfo['subject'] = $topicSummary['msg'];
            $topicInfo['user_id'] = (int)$tmpTopicInfo['authorid'];
            $topicInfo['last_reply_date'] = $tmpTopicInfo['lastpost'] . '000';
            $topicInfo['user_nick_name'] = $tmpTopicInfo['author'];  
            $topicInfo['hits'] = (int)$tmpTopicInfo['views'];  
            $topicInfo['replies'] = (int)$tmpTopicInfo['replies'];
            $topicInfo['top'] = (int)ForumUtils::isTopTopic($topic) ? 1 : 0;
            $topicInfo['status'] = (int)$tmpTopicInfo['status']; 
            $topicInfo['essence'] = (int)$tmpTopicInfo['digest'] ? 1 : 0;  
            $topicInfo['hot'] = (int)$tmpTopicInfo['highlight'] ? 1 : 0;
            $topicInfo['pic_path'] = ImageUtils::getThumbImage($topicSummary['image']);
            $list[] = $topicInfo;
        }
        return $list;
    }

    // 获取发表、回复和关注帖子的总数
    private function _getUserTopicListCount($type, $idType, $uid, $idtype) {  
        switch ($type) {
            case 'topic':
                $count = UserTopicInfo::getTopicsByUidCount($uid);
                break;
            case 'reply':
                $count = UserTopicInfo::getTopicsByUidWithPostCount($uid);
                break;
            case 'favorite':
                $count = UserTopicInfo::getUserfavoriteByTidCount($uid, $idType);
                break;
             default:
                break;
        }
        return $count;
    }

    // 分类信息
    private function getClassificationInfo($list) {
        for($i = 0; $i < count($list); $i++) {
            $ClassificationName = UserTopicInfo::getUserThreadTypeBySort($list[$i]['sort_id']);
            foreach ($ClassificationName as $tmpName) {
                $ClassName = $tmpName['name'];
                $list[$i]['title'] = WebUtils::emptyHtml("[".$ClassName."]".$list[$i]['title']);
            }
        }

        for($i = 0; $i < count($list); $i++) {
            $ClassificationType = UserTopicInfo::getUserThreadClassByType($list[$i]['type_id']);
            foreach($ClassificationType as $tmpType) {
                $ClassName = $tmpType['name'];
                $list[$i]['title'] = WebUtils::emptyHtml("[".$ClassName."]".$list[$i]['title']);
            }
        }
        return $list;
    }
}   

class UserTopicInfo extends DiscuzAR {
    
    // public static function getTopicsByUid($uid, $page, $pageSize) {
    //     return DbUtils::getDzDbUtils(true)->queryColumn('
    //         SELECT * 
    //         FROM %t WHERE authorid=%d 
    //         ORDER BY dateline DESC
    //         LIMIT %d, %d
    //         ',
    //         array('forum_thread', $uid, $pageSize*($page-1), $pageSize)
    //     );
    // }

    // 查询发表帖子tid
    public static function getTopicsByUid($uid, $page, $pageSize) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT tid 
            FROM %t 
            WHERE authorid = %d 
            AND displayorder >=0 
            ORDER BY lastpost DESC 
            LIMIT %d, %d
            ',
            array('forum_thread', $uid, $pageSize*($page-1), $pageSize)
        );
    }

    // public static function getTopicsByUidCount($uid) {
    //     $count = DbUtils::getDzDbUtils(true)->queryRow('
    //         SELECT COUNT(*) as num
    //         FROM %t
    //         WHERE authorid = %d
    //         AND displayorder >= 0
    //         ',
    //         array('forum_thread', $uid)
    //     );
    //     var_dump($count);exit;
    //     return $count['num'];
    // }

    // 查询发表帖子总数
    public static function getTopicsByUidCount($uid) {
        return DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT threads
            FROM %t
            WHERE uid = %d
            ',
            array('common_member_count', $uid)
        );
    }

    // 获取回复贴子的总数
    public static function getTopicsByUidWithPostCount($uid) {
        $count = DbUtils::getDzDbUtils(true)->queryRow('
            SELECT COUNT(DISTINCT post.tid) as num 
            FROM %t post INNER JOIN %t thread 
            ON post.tid = thread.tid 
            WHERE post.authorid=%d 
            AND post.first !=1
            ',
            array('forum_post', 'forum_thread', $uid)
        );
        return $count['num'];
    }

    // public static function getTopicsByUidWithPostCount($uid) {
    //     $posts = DbUtils::getDzDbUtils(true)->queryRow('
    //         SELECT threads, posts
    //         FROM %t
    //         WHERE uid = %d
    //         ',
    //         array('common_member_count', $uid)
    //     );
    //     return ($posts['posts'] - $posts['threads']);
    // }

    // public static function getUserfavoriteByTidCount($uid) {
    //     $count = DbUtils::getDzDbUtils(true)->queryRow('
    //         SELECT COUNT(DISTINCT thread.tid) as num
    //         FROM %t favorite INNER JOIN %t thread 
    //         ON favorite.id = thread.tid 
    //         WHERE favorite.uid = %d
    //         ',
    //         array('home_favorite', 'forum_thread', $uid)
    //     );
    //     return $count['num'];
    // }

    // 查询用户关注的帖子总数
    public static function getUserfavoriteByTidCount($uid, $idType) {
        return DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT COUNT(*)
            FROM %t 
            WHERE uid = %d
            AND idtype = %s
            ',
            array('home_favorite', $uid, $idType)
        );
    }

    // 获取回复帖子的tid
    public static function getTopicsByUidWithPost($uid, $page, $pageSize) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT thread.tid
            FROM %t post INNER JOIN %t thread 
            ON post.tid = thread.tid 
            WHERE post.authorid = %d 
            AND post.first = 0 
            GROUP BY thread.tid
            ORDER BY post.dateline DESC
            LIMIT %d, %d 
            ',
            array('forum_post', 'forum_thread', $uid, $pageSize*($page-1), $pageSize)
        );
    }

        
    // public static function getUserReplyByTid($tid) {
    //     return DbUtils::getDzDbUtils(true)->queryAll('
    //         SELECT * 
    //         FROM %t 
    //         WHERE tid=%d
    //         ',
    //         array('forum_thread', $tid)
    //     );
    // }

    // 获取用户关注帖子的tid
    public static function getUserfavoriteByTid($uid, $idType, $page, $pageSize) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT thread.tid 
            FROM %t favorite INNER JOIN %t thread 
            ON favorite.id = thread.tid 
            WHERE favorite.uid = %d 
            AND favorite.idtype = %s
            ORDER BY favorite.dateline DESC
            LIMIT %d, %d
            ',
            array('home_favorite', 'forum_thread', $uid, $idType, $pageSize*($page-1), $pageSize)
        );
    }

    // 获取分类信息的名称
    public static function getUserThreadTypeBySort($typeid) {
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT name 
            FROM %t 
            WHERE typeid = %d
            ',
            array('forum_threadtype', $typeid)
        );
    }

    // 获取主题分类信息
    public static function getUserThreadClassByType($typeid) {
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT * 
            FROM %t 
            WHERE typeid = %d
            ',
            array('forum_threadclass', $typeid)
        );
    }
}