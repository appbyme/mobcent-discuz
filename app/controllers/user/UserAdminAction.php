<?php

/**
 * 关注、取消关注和拉黑、取消拉黑接口
 *
 * @author 徐少伟 <xushaowei@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class UserAdminAction extends MobcentAction {
    
    public function run($uid, $type='follow') {
        $res = $this->initWebApiArray();
        $res = $this->_userSettingType($res, $type, $uid);

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _userSettingType($res, $type, $uid) {
        switch ($type) {
            case 'follow':$res = $this->_userFollowSetting($res, $uid); break;
            case 'unfollow':$res = $this->_userUnFollowSetting($res, $uid); break;
            case 'black': $res = $this->_userBlackSetting($res, $uid); break;
            case 'delblack':$res = $this->_userDelBlackSetting($res, $uid); break;
            default:# code...
            break;
        }
        return $res;
    }

    private function _userFollowSetting($res, $followUid) {
        global $_G;
        require_once libfile('function/core');
        $special = intval($_GET['special']) ? intval($_GET['special']) : 0;

        $uid = $_G['uid'];
        if ($uid == $followUid) {
            $res = $this->makeErrorInfo($res, 'follow_not_follow_self');
        } else {
            $followUser = getuserbyuid($followUid);
            $mutual = 0;

            $followed = DzUserSettingInfo::getUserSettingInfo($followUid, $uid);
            if(!empty($followed)) {
                if($followed['status'] == '-1') {
                    $res = $this->makeErrorInfo($res, 'follow_other_unfollow');
                }
                $mutual = 1;
                C::t('home_follow')->update_by_uid_followuid($followuid, $_G['uid'], array('mutual'=>1));
            }

            $followed = DzUserSettingInfo::getUserSettingInfo($uid, $followUid);
            if(empty($followed)) {
                $userInfo = UserUtils::getUserInfo($uid);

                $followInfo = array(
                        'uid' => $uid,
                        'username' => $_G['username'],
                        'followuid' => $followUid,
                        'fusername' => $followUser['username'],
                        'status' => 0,
                        'mutual' => $mutual,
                        'dateline' => TIMESTAMP
                );
                C::t('home_follow')->insert($followInfo, false, true);
                C::t('common_member_count')->increase($uid, array('following' => 1));
                C::t('common_member_count')->increase($followuid, array('follower' => 1, 'newfollower' => 1));
                notification_add($followUid, 'follower', 'member_follow_add', array('count' => $count, 'from_id'=>$uid, 'from_idtype' => 'following'), 1);
            } elseif($special) {
                $status = $special == 1 ? 1 : 0;
                C::t('home_follow')->update_by_uid_followuid($uid, $followuid, array('status'=>$status));
                $special = $special == 1 ? 2 : 1;
            } else {
                $res = $this->makeErrorInfo($res, 'follow_followed_ta');
            }
        }
        if ($res['rs'] == 1) {
            $params['noError'] = 1;
            $res = $this->makeErrorInfo($res, 'follow_add_succeed', $params);
        }
        return $res;
    }

    private function _userUnFollowSetting($res, $delfollowuid) {
        global $_G;
        $uid = $_G['uid'];

        $affectedrows = C::t('home_follow')->delete_by_uid_followuid($uid, $delfollowuid);
        if($affectedrows) {
            C::t('home_follow')->update_by_uid_followuid($delfollowuid, $uid, array('mutual'=>0));
            C::t('common_member_count')->increase($uid, array('following' => -1));
            C::t('common_member_count')->increase($delfollowuid, array('follower' => -1, 'newfollower' => -1));
        }

        $params['noError'] = 1;
        $res = $this->makeErrorInfo($res, 'follow_cancel_succeed', $params);
        return $res;
    }

    private function _userBlackSetting($res, $blackUid) {
        global $_G;
        $uid = $_G['uid'];

        $blackName = UserUtils::getUserName($blackUid);
        require_once libfile('function/friend');
        
        if(!($blackName = C::t('common_member')->fetch_by_username($blackName))) {
            $res = $this->makeErrorInfo($res, 'space_does_not_exist');
        } elseif ($blackUid == $uid) {
            $res = $this->makeErrorInfo($res, 'unable_to_manage_self');
        } else {
            friend_delete($blackUid);
            C::t('home_blacklist')->insert(array('uid'=>$uid, 'buid'=>$blackUid, 'dateline'=>$_G['timestamp']), false, false, true);
            $params['noError'] = 1;
            $res = $this->makeErrorInfo($res, 'do_success', $params);
        }
        return $res;
    }

    private function _userDelBlackSetting($res, $delBlackUid) {
        global $_G;
        $uid = $_G['uid'];

        C::t('home_blacklist')->delete_by_uid_buid($uid, $delBlackUid);
        $params['noError'] = 1;
        $res = $this->makeErrorInfo($res, 'do_success', $params);
        return $res;
    }
}

class DzUserSettingInfo extends DiscuzAR {
    
    public static function getUserSettingInfo($uid, $followUid) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT * 
            FROM %t 
            WHERE uid = %d AND followuid = %d
            ',
            array('home_follow', $uid, $followUid)
        );
    }
}
?>