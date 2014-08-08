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

class UserListAction extends CAction {

    public function run($type='follow', $page=1, $pageSize=10) {
        $res = WebUtils::initWebApiArray_oldVersion();

        $uid = $this->getController()->uid;
        $viewUid = isset($_GET['uid']) ? $_GET['uid'] : $uid;
        
        $res = $this->_getUserInfoList($res, $type, $uid, $viewUid, $page, $pageSize);
        
        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _getUserInfoList($res, $type, $uid, $viewUid, $page, $pageSize) {
        $res['list'] = $this->_getUserList($type, $uid, $viewUid, $page, $pageSize);
        $count = $this->_getUserListCount($type, $uid, $viewUid, $page, $pageSize); 
        $res = WebUtils::getWebApiArrayWithPage_oldVersion($page, $pageSize, $count, $res);
        return $res;
    }

    // 用户关注、粉丝和推荐详细信息列表
    private function _getUserList($type, $uid, $viewUid, $page, $pageSize) {
        switch ($type) {
            case 'follow':  
                $users = UserFollowInfo::_getFollowUsers($viewUid, $page, $pageSize);
                break;
            case 'followed':
                $users = UserFollowInfo::_getFollowedUsers($viewUid, $page, $pageSize);
                break;
            case 'recommend':
                $users = $this->_getRecommendUserList($uid, $page, $pageSize);
                $users = $users['users'];
                break;
            case 'friend':
                $users = UserFollowInfo::getPostFuidList($uid);
                break;
            default:
                break;
        }
        $list = $this->_transUserList($users, $viewUid);
        return $list;
    }

    // 用户关注、粉丝和推荐详细信息
    private function _transUserList($users, $viewUid) {
        loadcache('usergroups');

        $list = array();
        foreach ($users as $user) {
            $tmpfollowe['is_friend'] = UserUtils::isFollow($viewUid, $user) ? 1 : 0;
            $tmpfollowe['uid'] = (int)$user;
            $tmpfollowe['name'] = UserUtils::getUserName($user);
            $tmpfollowe['name'] = WebUtils::emptyHtml($tmpfollowe['name']);
            $tmpfollowe['status'] = (int)UserUtils::getUserLoginStatus($user);
            $tmpfollowe['is_black'] = UserUtils::isBlacklist($viewUid , $user) ? 1 : 0;
            $tmpfollowe['gender'] = (int)UserUtils::getUserGender($user);
            $tmpfollowe['icon'] = UserUtils::getUserAvatar($user);
            $tmpfollowe['level'] = (int)UserFollowInfo::getUserLevel($user);
            $userInfo = UserUtils::getUserInfo($user);
            $tmpfollowe['credits'] = (int)$userInfo['credits'];
            $list[] = $tmpfollowe;
        } 
        return $list;
    }

    // 用户关注、粉丝和推荐的总数
    private function _getUserListCount($type, $uid, $viewUid, $page, $pageSize) {
        switch ($type) {
            case 'follow':  
                $count = UserFollowInfo::_getFollowUsersCount($viewUid);
                break;
            case 'followed':
                $count = UserFollowInfo::_getFollowedUsersCount($viewUid);
                break;
            case 'recommend':
                $count = $this->_getRecommendUserList($uid, $page, $pageSize);
                $count = $count['count'];
                break;
            case 'friend':
                $count = UserFollowInfo::getPostFuidListCount($uid);
                break;
            default:
                break;
        }
        return $count;
    }

    // 设置用户关注
    private function _getRecommendUserList($uid, $page, $pageSize) {
        $users = UserFollowInfo::_getrecommendUsersSet($page, $pageSize);
        if (!empty($users)) {
            $userListInfo = array();
            $userListInfo['users'] = $users;
            $userListInfo['count'] = UserFollowInfo::_getrecommendUsersSetCount();
            return $userListInfo;
       } else {
            $userListInfo = array();
            $userListInfo['users'] = UserFollowInfo::_getrecommendUsers($uid, $page, $pageSize);
            $userListInfo['count'] = UserFollowInfo::_getrecommendUsersCount($uid);
            return $userListInfo;
        }
    }
}

class UserFollowInfo extends DiscuzAR {

    public static function getPostFuidList($uid) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT fuid
            FROM %t 
            WHERE uid = %d
            ',
            array('home_friend', $uid)
        );
    }

    public static function getPostFuidListCount($uid) {
        return DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT count(*)
            FROM %t 
            WHERE uid = %d
            ',
            array('home_friend', $uid)
        );
    }
    public static function getFollowList($uid) {
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT * 
            FROM %t WHERE uid = %d 
            AND status != -1
            ORDER BY dateline DESC
            ',
            array('home_follow', $uid)
        );
    }

    // 查询用户关注好友的tid
    public static function _getFollowUsers($uid, $page, $pageSize) {
        if ($page==0) {
            $sql = sprintf('
            SELECT followuid 
            FROM %%t
            WHERE uid=%%d AND status=0
            ORDER BY dateline DESC 
            ');
        }else{
            $sql = sprintf('
            SELECT followuid 
            FROM %%t
            WHERE uid=%%d AND status=0
            ORDER BY dateline DESC
            LIMIT %%d, %%d
            ');
        }
        return DbUtils::getDzDbUtils(true)->queryColumn(
            $sql,
            array('home_follow', $uid, $pageSize*($page-1), $pageSize)
        );
    }

    // 查询用户关注好友的总数
    public static function _getFollowUsersCount($uid) {
        $count = DbUtils::getDzDbUtils(true)->queryRow('
            SELECT count(*) as num
            FROM %t
            WHERE uid=%d AND status=0
            ORDER BY dateline DESC
            ',
            array('home_follow', $uid)
        );
        return $count['num'];
    }

    // 查询用户粉丝的总数
    public static function _getFollowedUsersCount($uid) {
        $count = DbUtils::getDzDbUtils(true)->queryRow('
            SELECT count(*) as num
            FROM %t
            WHERE followuid=%d AND status=0
            ORDER BY dateline DESC
            ',
            array('home_follow', $uid)
        );
        return $count['num'];
    }

    // 获取用户等级
    public static function getUserLevel($uid) {
        $icon = UserUtils::getUserLevelIcon($uid);
        return $icon['sun'] * 4 + $icon['moon'] * 2 + $icon['star'] * 1;
    }

    // 获取用户粉丝的详细信息
    public static function _getFollowedUsers($uid, $page, $pageSize) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT uid
            FROM %t
            WHERE followuid=%d AND status=0
            ORDER BY dateline DESC 
            LIMIT %d, %d
            ',
            array('home_follow', $uid, $pageSize*($page-1), $pageSize)
        );
    }

    // 获取用户未设置关注用户时的关注用户
    public static function _getrecommendUsers($uid, $page, $pageSize) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT uid 
            FROM %t 
            WHERE uid != %d
            ORDER BY credits DESC
            LIMIT %d, %d
            ',
            array('common_member', $uid, $pageSize*($page-1), $pageSize)
        );
    }

    // 获取用户设置关注用户数
    public static function _getrecommendUsersCount($uid) {
        $count = DbUtils::getDzDbUtils(true)->queryRow('
            SELECT count(*) as num 
            FROM %t 
            WHERE uid != %d
            ',
            array('common_member', $uid)
        );
        return $count['num'];
    }

    // 查询用户是否设置了关注
    public static function _getrecommendUsersSet($page, $pageSize) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT uid
            FROM %t
            WHERE status = 0
            ORDER BY displayorder ASC 
            LIMIT %d, %d
            ',
            array('home_specialuser', $pageSize*($page-1), $pageSize)
        );
    }

    // 用户设置了关注的用户数
    public static function _getrecommendUsersSetCount() {
        $count = DbUtils::getDzDbUtils(true)->queryRow('
            SELECT COUNT(*) as num
            FROM %t
            WHERE status = 0
            ',
            array('home_specialuser')
        );
        return $count['num'];
    }
}