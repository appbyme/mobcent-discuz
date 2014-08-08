<?php

/**
 * 周边用户，周边帖子 接口
 *
 * @author  谢建平 <jianping_xie@aliyun.com>  
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class SurroundingAction extends CAction {

    public function run($longitude, $latitude, $poi='user', 
                        $page=1, $pageSize=10, $radius=100000) {
        $res = WebUtils::initWebApiArray();
        $res = array_merge(array('rs' => 1, 'errcode' => 0), $res);

        $uid = $this->getController()->uid;
        $SurroundingType = $poi;
        
        // get infos with type
        $infos = array();
        switch ($SurroundingType) {
            case 'user': $infos = $this->_getUserInfos($uid, $longitude, $latitude,
                $radius, $page, $pageSize); break;
            case 'topic': $infos = $this->_getTopicInfos($uid, $longitude, $latitude,
                $radius, $page, $pageSize); break;
            default: break;
        }
        $list = $infos['list'];
        $count = $infos['count'];

        $res['pois'] = $list;
        $res = array_merge($res, WebUtils::getWebApiArrayWithPage_oldVersion(
            $page, $pageSize, $count));

        echo WebUtils::outputWebApi($res, '', false);
    }

    // 取得周边用户信息
    private function _getUserInfos($uid, $longitude, $latitude, $radius, 
                                   $page, $pageSize) {
        $userInfos = array('count' => 0, 'list' => array());
        $count = SurroundingInfo::getUserCountByUid($uid, $longitude, $latitude, $radius);
        $surroundUsers = SurroundingInfo::getAllUsersByUid($uid, $longitude, $latitude, $radius, $page, $pageSize);

        $list = array();
        foreach ($surroundUsers as $user) {
            $userInfo = array();
            $tmpUid = (int)$user['object_id'];
            
            $userInfo['uid'] = $tmpUid;
            $userInfo['distance'] = $user['distance'];
            $userInfo['location'] = $user['location'];
            $userInfo['nickname'] = UserUtils::getUserName($tmpUid);
            $userInfo['gender'] = UserUtils::getUserGender($tmpUid);
            $userInfo['status'] = UserUtils::getUserLoginStatus($tmpUid) == UserUtils::STATUS_ONLINE ? 1 : 0;
            $userInfo['icon'] = UserUtils::getUserAvatar($tmpUid);
            // $userInfo['is_friend'] = UserUtils::isFriend($uid, $tmpUid) ? 1 : 0;
            $userInfo['is_friend'] = UserUtils::isFollow($uid, $tmpUid) ? 1 : 0;
            $userInfo['is_black'] = UserUtils::isBlacklist($uid, $tmpUid) ? 1 : 0;

            $list[] = $userInfo;
        }
        $userInfos['count'] = $count;
        $userInfos['list'] = $list;

        return $userInfos;
    }

    // 取得周边帖子信息
    private function _getTopicInfos($uid, $longitude, $latitude, $radius,
                                    $page, $pageSize) {
        $topicInfos = array('count' => 0, 'list' => array());
        $count = SurroundingInfo::getTopicCountByTid($uid, $longitude, $latitude, $radius);
        $surroundTopics = SurroundingInfo::getAllTopicsByTid($uid, $longitude, $latitude, $radius, $page, $pageSize);

        $list = array();
        foreach ($surroundTopics as $topic) {
        	$topicInfo = array();
            $topicId = (int)$topic['object_id'];
            // $tmpTopicInfo = ForumUtils::getTopicInfo($topicId);
            $tmpTopicInfo = $topic;
            if (!empty($tmpTopicInfo)) {
                $tmpUid = (int)$tmpTopicInfo['authorid'];
            	$tmpFid = (int)$tmpTopicInfo['fid'];
            	
			    $topicInfo['distance'] = $topic['distance'];
                $topicInfo['location'] = $topic['location'];
        		$topicInfo['board_id'] = $tmpFid;
        		$topicInfo['board_name'] = ForumUtils::getForumName($tmpFid);
        		$topicInfo['topic_id'] = $topicId;
        		$topicInfo['title'] = $tmpTopicInfo['subject'];
        		$topicInfo['uid'] = $tmpUid;
        		$topicInfo['user_nick_name'] = $tmpTopicInfo['author'];
        		$topicInfo['vote'] = ForumUtils::isVoteTopic($tmpTopicInfo) ? 1 : 0;
        		$topicInfo['hot'] = ForumUtils::isHotTopic($tmpTopicInfo) ? 1 : 0;
        		$topicInfo['hits'] = (int)$tmpTopicInfo['views'];
        		$topicInfo['replies'] = (int)$tmpTopicInfo['replies'];
                $topicInfo['essence'] = ForumUtils::isMarrowTopic($tmpTopicInfo) ? 1 : 0;
                $topicInfo['top'] = ForumUtils::isTopTopic($tmpTopicInfo) ? 1 : 0;
                $topicInfo['last_reply_date'] = $tmpTopicInfo['lastpost'] . '000';
                $topicInfo['replies'] = (int)$tmpTopicInfo['replies'];

                $topicSummary = ForumUtils::getTopicSummary($topicId);
                $topicInfo['subject'] = $topicSummary['msg'];
                $topicInfo['pic_path'] = $topicSummary['image'];
                // $topicInfo['type_id'] = (int)$tmpTopicInfo['typeid'];
                // $topicInfo['sort_id'] = (int)$tmpTopicInfo['sortid'];
                // $topicInfo['poll'] = $tmpTopicInfo['lastpost'];
                
        		$list[] = $topicInfo;
            }
        } 
        $topicInfos['count'] = $count;
        $topicInfos['list'] = $list;

        return $topicInfos;
    }
}