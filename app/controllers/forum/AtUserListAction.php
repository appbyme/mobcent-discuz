<?php

/**
 * @好友接口
 *
 * @author 徐少伟 <xushaowei@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class AtUserListAction extends CAction {
    public function run($page=1, $pageSize=10) {

        $res = WebUtils::initWebApiArray_oldVersion();
        
        $uid = $this->getController()->uid;

        $params = array('type'=>'friend', 'page'=>0);
        $friend = $this->_getForWardInfo($params);

        $params = array('type'=>'follow', 'page'=>0, 'uid'=>$uid);
        $follow = $this->_getForWardInfo($params);

        $res = $this->_getUserPostFriendPagingListInfo($res, $friend, $follow, $page, $pageSize);

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _getUserPostFriendPagingListInfo($res, $friendList, $followList, $page, $pageSize) {
        $res['list'] =$this->_getUserPostFriendPagingList($friendList, $followList, $page, $pageSize);
        $count = $this->_getUserPostFriendListCount($friendList, $followList);
        $res = WebUtils::getWebApiArrayWithPage_oldVersion($page, $pageSize, $count, $res);
        return $res;
    }

    private function _getForWardInfo($params) {
        $_GET = array_merge($_GET, $params);
        ob_start();
        $this->getController()->forward('user/userlist', false);
        $res = ob_get_clean();
        $list = WebUtils::jsonDecode($res);
        return $list['list'];
    }

    private function _getUserPostFriendList($friendList, $followList) {
        $postFriend = $this->getPostFriendList($friendList);
        $userFollow = $this->getUserFollowList($friendList, $followList);
        return array_merge($postFriend, $userFollow);
    }

    private function getPostFriendList($friendList) {
        $list = array();

        foreach ($friendList as $post) {
            $postInfo['uid'] = (int)$post['uid']; 
            $postInfo['name'] = WebUtils::t($post['name']);
            $postInfo['role_num'] = (int)2;
            $list[] = $postInfo;
        }
        return $list;
    }

    private function getUserFollowList($friendList, $followList) {
        $list = array();

        foreach ($friendList as $friend) {
            $tempFriend[] = $friend['uid'];
        }

        foreach ($followList as $follow) {

            if(in_array($follow['uid'], $tempFriend)) {
                continue;
            }

            $followInfo['uid'] = (int)$follow['uid'];
            $followInfo['name'] = WebUtils::t($follow['name']);
            $followInfo['role_num'] = (int)6;
            $list[] = $followInfo;
        }
        return $list;
    }

    private function _getUserPostFriendListCount($friendList, $followList) {
        $count = $this->_getUserPostFriendList($friendList, $followList);
        $count = count($count);
        return $count;
    }

    private function _getUserPostFriendPagingList($friendList, $followList, $page, $pageSize) {
        $list = array();

        $userPost = $this->_getUserPostFriendList($friendList, $followList);

        $current = ($page - 1)*$pageSize;
        $count = count($userPost);
        $lastPage = ceil($count/$pageSize);

        if ($count < $pageSize) {
            $pageSize = $count;
        } elseif ($page == $lastPage && $count % $pageSize) {
            $pageSize = $count % $pageSize;
        }
        for ($i = $current; $i < ($current + $pageSize); $i++) { 
            $list[] = $userPost[$i];
        }
        if ($page < 0 || $page > $lastPage) {
            $list = array();
        }
        if ($page == 0) {
            $list = $userPost;
        }
        return $list;
    }
}
?>