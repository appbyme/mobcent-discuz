<?php

/**
 * 用户中心接口
 *
 * @author 徐少伟 <xushaowei@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class UserInfoAction extends CAction {

    public function run() {

        $res = WebUtils::initWebApiArray_oldVersion();
        
        $uid = $this->getController()->uid;
        $puid = isset($_GET['userId']) ? $_GET['userId'] : $uid;

        $res = $this->_getUserInfo($res, $uid, $puid);
        $res['info'] = array();
        
        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _getUserInfo($res, $uid, $puid) {
        loadcache('usergroups');

        $space = UserUtils::getUserInfo($puid);
        if (empty($space)) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'space_does_not_exist');
        }

        $spacePro = UserUtils::getUserProfile($puid);
        $spaceCount = DzUserInfo::getCommonMemberCount($puid);
        $space = array_merge($space, $spacePro, $spaceCount);
        $listCount = (int)DzUserInfo::getUserPhotosHomeAlbumCount($puid);
        
        $res['flag'] = $uid == $puid ? 1 : 0;
        $res['is_black'] = (int)UserUtils::isBlacklist($uid, $puid) ? 1 : 0;
        $res['is_follow'] = (int)UserUtils::isFollow($uid, $puid); 
        $res['icon'] = UserUtils::getUserAvatar($puid); 
        $res['level_url'] = '';
        $res['name'] = $space['username'];
        $res['email'] = $space['email'];
        $res['status'] = (int)UserUtils::getUserLoginStatus($puid);
        $res['gender'] = (int)UserUtils::getUserGender($puid);
        $res['email'] = $space['email'];
        $res['score'] = (int)$space['credits'];
        $res['credits'] = (int)$space['credits'];  
        $res['gold_num'] = (int)$space['extcredits2']; 
        $res['topic_num'] = (int)$this->_getHomeTopicNum($puid); 
        $res['photo_num'] = (int)$listCount['nums'];
        $res['reply_posts_num'] = (int)DzUserInfo::getTopicsByUidWithPostCount($puid);
        $res['essence_num'] = (int)$space['digestposts'];
        $res['friend_num'] = (int)DzUserInfo::getFollowFriendsCount($puid);
        $res['follow_num'] = (int)DzUserInfo::getFollowedFriendsCount($puid);
        $res['level'] = (int)DzUserInfo::getUserLevel($space['groupid']);

        $res['userTitle'] = UserUtils::getUserTitle($puid);

        $repeatList = array();
        foreach(UserUtils::getRepeatList($uid) as $user) {
            $repeatList[] = array(
                'userName' => $user,
            );
        }
        $res['body']['repeatList'] = $repeatList;
        return $res;
    }

    private function _getInfoList($uid, $puid) {
        $list = array();
        $topicList = array();
        if ($uid == $puid) {
            $topicList = DzUserInfo::getFavouriteTopics($uid);
        } else {
            $topicList = DzUserInfo::getTopicsByUid($puid);
        }

        foreach ($topicList as $topic) {                  
            $topicSummary = ForumUtils::getTopicSummary((int)$topic['tid']);
            $tmpTopic['board_id'] = (int)$topic['fid'];
            $tmpTopic['board_name'] = ForumUtils::getForumName((int)$topic['fid']);
            $tmpTopic['topic_id'] = (int)$topic['tid'];
            $tmpTopic['title'] = $topic['subject'];
            $tmpTopic['user_id'] = (int)$topic['authorid'];
            $tmpTopic['lastpost'] = ($topic['lastpost']) . "000";
            $tmpTopic['user_nick_name'] = $topic['author'];
            $tmpTopic['hits'] = (int)$topic['views'];
            $tmpTopic['content'] = WebUtils::emptyReturnLine($topicSummary['msg'], ' ');
            $tmpTopic['replies'] = (int)$topic['replies'];
            $tmpTopic['pic_path'] = ImageUtils::getThumbImage($topicSummary['image']);
            $list[] = $tmpTopic;
        }
        return $list;
    }

    // 获取登录用户发表的帖子
    private function _getHomeTopicNum($uid) {
        return DzUserInfo::getAllHomeTopicNum($uid);
    }
}

class DzUserInfo extends DiscuzAR {
    
    // 获取用户相册
    public static function getUserPhotosHomeAlbumCount($uid) {
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT count(*) as nums 
            FROM %t 
            WHERE uid = %d
            ',
            array('home_album', $uid)
        );
    }


    public static function getUserForumThread($uid) {
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT count(*) as nums 
            FROM %t 
            WHERE uid = %d
            ',
                array('home_album',$uid)
            );
    }

    // 获取用户统计信息
    public static function getCommonMemberCount($uid) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE uid=%d
            ',
                array('common_member_count', $uid)
        );
    }

    //获取用户被关注好友数
    public static function getFollowedFriendsCount($uid) {
        return DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT COUNT(*)
            FROM %t 
            WHERE followuid = %d 
            ', 
            array('home_follow', $uid)
        );
    }

    //获取用户关注好友数
    public static function getFollowFriendsCount($uid) {
        return DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT COUNT(*)
            FROM %t 
            WHERE uid = %d 
            ', 
            array('home_follow', $uid)
        );
    }

    //获取当前用户发表的帖子数
    public static function getAllHomeTopicNum($uid) {
        $TopicNum =  DbUtils::getDzDbUtils(true)->queryAll('
            SELECT count(*) as num
            FROM %t 
            WHERE authorid = %d 
            AND displayorder >=0
            ',
            array('forum_thread', $uid)
        );
        return $TopicNum = $TopicNum[0]['num'];
    }

    // 获取用户当前等级
    public static function getUserLevel($uid) {
        $icon = UserUtils::getUserLevelIcon($uid);
        return $icon['sun'] * 4 + $icon['moon'] * 2 + $icon['star'] * 1;
    }

    public static function getFavouriteTopics($uid) {
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT a.*, b.*
            FROM %t a INNER JOIN %t b 
            ON a.id=b.tid
            WHERE a.uid = %d
            AND a.idtype = %s 
            ORDER BY a.id DESC 
            LIMIT 0, 5
            ',
            array('home_favorite', 'forum_thread', $uid, 'tid')
        );
    }

    public static function getTopicsByUid($uid) {
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT * 
            FROM %t 
            WHERE authorid = %d 
            AND displayorder >= 0 
            ORDER BY lastpost DESC 
            LIMIT 0, 5
            ',
            array('forum_thread', $uid)
        );
    }

    public static function getTopicsByUidWithPostCount($uid) {
        $count = DbUtils::getDzDbUtils(true)->queryRow('
            SELECT COUNT(DISTINCT post.tid) as num 
            FROM %t post INNER JOIN %t thread 
            ON post.tid = thread.tid 
            WHERE post.authorid = %d 
            AND post.first != 1
            ',
            array('forum_post', 'forum_thread', $uid)
        );
        return $count['num'];
    }
}