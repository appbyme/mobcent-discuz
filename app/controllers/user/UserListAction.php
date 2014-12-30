<?php

/**
 * 用户关注好友、粉丝和推荐接口
 *
 * @author 徐少伟 <xushaowei@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}
// Mobcent::setErrors();
class UserListAction extends CAction {

    public function run($type='follow', $page=1, $pageSize=10, $orderBy='dateline', $longitude='', $latitude='', $radius=100000) {
        $res = WebUtils::initWebApiArray_oldVersion();

        switch ($orderBy) {
            case 'register': $sortType = 'regdate';break;
            case 'login': $sortType = 'lastvisit';break;
            case 'followed': $sortType = 'follower';break;
            case 'distance': $sortType = 'range';break;
            case 'dateline': $sortType = 'default';break;
            default:break;
        }

        global $_G;
        $uid = $_G['uid'];
        $viewUid = isset($_GET['uid']) ? $_GET['uid'] : $uid;
        $res = $this->_getUserInfoList($res, $type, $uid, $viewUid, $page, $pageSize, $sortType, $longitude, $latitude, $radius);
        
        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _getUserInfoList($res, $type, $uid, $viewUid, $page, $pageSize, $sortType, $longitude, $latitude, $radius) {

        $res['list'] = $this->_getUserList($type, $uid, $viewUid, $page, $pageSize, $sortType, $longitude, $latitude, $radius);
        $count = $this->_getUserListCount($type, $uid, $viewUid, $page, $pageSize); 
        $res = WebUtils::getWebApiArrayWithPage_oldVersion($page, $pageSize, $count, $res);
        return $res;
    }

    // 用户关注、粉丝和推荐详细信息列表
    private function _getUserList($type, $uid, $viewUid, $page, $pageSize, $sortType, $longitude, $latitude, $radius) {
        switch ($type) {
            case 'follow':  
                $users = $this->_getFollowUsers($viewUid, $page, $pageSize, $sortType, $longitude, $latitude, $radius);
                break;
            case 'followed':
                $users = $this->_getFollowedUsers($viewUid, $page, $pageSize, $sortType, $longitude, $latitude, $radius);
                break;
            case 'recommend':
                $users = $this->_getRecommendUserList($uid, $page, $pageSize, $sortType, $longitude, $latitude, $radius);
                $users = $users['users'];
                break;
            case 'friend':
                $users = $this->_getPostFuidList($viewUid, $page, $pageSize, $sortType, $longitude, $latitude, $radius);
                break;
            case 'all':
                $users = $this->_getAllUidList($uid, $page, $pageSize, $sortType, $longitude, $latitude, $radius);
                break;
            default:
                break;
        }
        $list = $this->_transUserList($users, $viewUid, $longitude, $latitude, $radius, $page, $pageSize, $sortType);
        return $list;
    }

    // 用户关注、粉丝和推荐详细信息
    private function _transUserList($users, $viewUid, $longitude, $latitude, $radius, $page, $pageSize, $sortType) {
        loadcache('usergroups');

        $list = array();
        foreach ($users as $user) {
            if ($sortType == 'range') {
                $tmpUserInfo['distance'] = (string)$user['distance'];
                $tmpUserInfo['location'] = (string)WebUtils::t($user['location']);
                $uid = $user['uid'];
            } else {
                $tmpUserInfo['distance'] = '';
                $tmpUserInfo['location'] = '';
                $uid = $user;
            }
            $tmpUserInfo['is_friend'] = UserUtils::isFollow($viewUid, $uid) ? 1 : 0;
            $tmpUserInfo['isFriend'] = UserUtils::isFriend($viewUid, $uid) ? 1 : 0;
            $tmpUserInfo['isFollow'] = UserUtils::isFollow($viewUid, $uid) ? 1 : 0;
            $tmpUserInfo['uid'] = (int)$uid;
            $tmpUserInfo['name'] = UserUtils::getUserName($uid);
            $tmpUserInfo['name'] = WebUtils::emptyHtml($tmpUserInfo['name']);
            $tmpUserInfo['status'] = (int)UserUtils::getUserLoginStatus($uid);
            $tmpUserInfo['is_black'] = UserUtils::isBlacklist($viewUid , $uid) ? 1 : 0;
            $tmpUserInfo['gender'] = (int)UserUtils::getUserGender($uid);
            $tmpUserInfo['icon'] = UserUtils::getUserAvatar($uid);
            $tmpUserInfo['level'] = (int)DzCommonUserList::getUserLevel($uid);
            $lastLogin = WebUtils::t(DzCommonUserList::getUserLastVisit($uid));
            $tmpUserInfo['lastLogin'] = $lastLogin . '000';
            if ($sortType == 'regdate') {
                $lastRegdate = DzCommonUserList::getUserLastRegdate($uid);
                $tmpUserInfo['dateline'] = $lastRegdate . '000';
            } else {
                $tmpUserInfo['dateline'] = $lastLogin . '000';
            }
            $signature = WebUtils::emptyHtml(DzCommonUserList::getUserSightml($uid));
            $tmpUserInfo['signature'] = WebUtils::t($signature);
            $userInfo = UserUtils::getUserInfo($uid);
            $tmpUserInfo['credits'] = (int)$userInfo['credits'];
            
            $list[] = $tmpUserInfo;
        } 
        return $list;
    }

    // 用户关注、粉丝和推荐的总数
    private function _getUserListCount($type, $uid, $viewUid, $page, $pageSize) {
        switch ($type) {
            case 'follow':  
                $count = DzCommonUserList::_getFollowUsersCount($viewUid);
                break;
            case 'followed':
                $count = DzCommonUserList::_getFollowedUsersCount($viewUid);
                break;
            case 'recommend':
                $count = $this->_getRecommendUserList($uid, $page, $pageSize);
                $count = $count['count'];
                break;
            case 'friend':
                $count = DzCommonUserList::getPostFuidListCount($viewUid);
                break;
            case 'all':
                $count = DzCommonUserList::_getRecommendUsersCount($uid);
                break;   
            default:
                break;
        }
        return $count;
    }

    // 设置用户关注
    private function _getRecommendUserList($uid, $page, $pageSize, $sortType) {
        $users = DzCommonUserList::_getRecommendUsersSet($page, $pageSize);
        if (!empty($users)) {
            $userListInfo = array();
            $userListInfo['users'] = $this->_getRecommendUsers($page, $pageSize, $sortType);
            $userListInfo['count'] = DzCommonUserList::_getRecommendUsersSetCount();
            return $userListInfo;
       } else {
            $userListInfo = array();
            $userListInfo['users'] = $this->_getAllUidList($uid, $page, $pageSize, $sortType);
            $userListInfo['count'] = DzCommonUserList::_getRecommendUsersCount($uid);
            return $userListInfo;
        }
    }

    // 用户关注排序
    private function _getFollowUsers($viewUid, $page, $pageSize, $sortType, $longitude, $latitude, $radius) {
        switch ($sortType) {
            case 'default':
                return DzCommonUserList::_getFollowUsersDefault($viewUid, $page, $pageSize);
                break;
            case 'regdate':
                return DzCommonUserList::_getFollowUsersByRegist($viewUid, $page, $pageSize);
                break;
            case 'lastvisit':
                return DzCommonUserList::_getFollowUsersByLastVisit($viewUid, $page, $pageSize);
                break;
            case 'follower':
                return DzCommonUserList::_getFollowUsersByFollower($viewUid, $page, $pageSize);
                break;
            case 'range':
                return DzCommonUserList::_getFollowUsersByRange($viewUid, $page, $pageSize, $longitude, $latitude, $radius);
                break;
            default:
                break;
        }
    }

    // 用户粉丝排序选择
    private function _getFollowedUsers($uid, $page, $pageSize, $sortType, $longitude, $latitude, $radius) {
        switch ($sortType) {
            case 'default':
                return DzCommonUserList::_getFollowedUsersDefault($uid, $page, $pageSize);
                break;
            case 'regdate':
                return DzCommonUserList::_getFollowedUsersByRegist($uid, $page, $pageSize);
                break;
            case 'lastvisit':
                return DzCommonUserList::_getFollowedUsersByLastVisit($uid, $page, $pageSize);
                break;
            case 'follower':
                return DzCommonUserList::_getFollowedUsersByFollower($uid, $page, $pageSize);
                break;
            case 'range':
                return DzCommonUserList::_getFollowedUsersByRange($uid, $page, $pageSize, $longitude, $latitude, $radius);
                break;
            default:
                break;
        }
    }

    // 用户推荐关注排序选择
    private function _getRecommendUsers($page, $pageSize, $sortType, $longitude, $latitude, $radius) {
        switch ($sortType) {
            case 'default':
                return DzCommonUserList::_getRecommendUsersSetByDefault($page, $pageSize);
                break;
            case 'regdate':
                return DzCommonUserList::_getRecommendUsersSetByRegist($page, $pageSize);
                break;
            case 'lastvisit':
                return DzCommonUserList::_getRecommendUsersSetByLastVisit($page, $pageSize);
                break;
            case 'follower':
                return DzCommonUserList::_getRecommendUsersSetByFollower($page, $pageSize);
                break;
            case 'range':
                return DzCommonUserList::_getRecommendUsersSetByRange($uid, $page, $pageSize, $longitude, $latitude, $radius);
                break;
            default:
                break;
        }
    }

    // 用户好友排序选择
    private function _getPostFuidList($uid, $page, $pageSize, $sortType, $longitude, $latitude, $radius) {
        switch ($sortType) {
            case 'default':
                return DzCommonUserList::_getPostFuidListByDefault($uid, $page, $pageSize);
                break;
            case 'regdate':
                return DzCommonUserList::_getPostFuidListByRegist($uid, $page, $pageSize);
                break;
            case 'lastvisit':
                return DzCommonUserList::_getPostFuidListByLastVisit($uid, $page, $pageSize);
                break;
            case 'follower':
                return DzCommonUserList::_getPostFuidListByFollower($uid, $page, $pageSize);
                break;
            case 'range':
                return DzCommonUserList::_getPostFuidListByRange($uid, $page, $pageSize, $longitude, $latitude, $radius);
                break;
            default:
                break;
        }
    }

    // 全部用户排序选择
    private function _getAllUidList($uid, $page, $pageSize, $sortType, $longitude, $latitude, $radius) {
        switch ($sortType) {
            case 'default':
                return DzCommonUserList::_getRecommendUsersByDefault($uid, $page, $pageSize);
                break;
            case 'regdate':
                return DzCommonUserList::_getRecommendUsersByRegist($uid, $page, $pageSize);
                break;
            case 'lastvisit':
                return DzCommonUserList::_getRecommendUsersByLastVisit($uid, $page, $pageSize);
                break;
            case 'follower':
                return DzCommonUserList::_getRecommendUsersByFollower($uid, $page, $pageSize);
                break;
            case 'range':
                return DzCommonUserList::_getRecommendUsersByRange($uid, $page, $pageSize, $longitude, $latitude, $radius);
                break;
            default:
                break;
        }
    }
}