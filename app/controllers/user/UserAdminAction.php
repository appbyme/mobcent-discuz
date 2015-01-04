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
// Mobcent::setErrors();
class UserAdminAction extends MobcentAction {

    public function run($uid, $type='follow', $gid=0, $message='') {
        $res = $this->initWebApiArray();
        $res = $this->_userSettingType($res, $type, $uid, $gid, $message);

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _userSettingType($res, $type, $uid, $gid, $note) {
        switch ($type) {
            case 'follow':$res = $this->_userFollowSetting($res, $uid); break;
            case 'unfollow':$res = $this->_userUnFollowSetting($res, $uid); break;
            case 'black': $res = $this->_userBlackSetting($res, $uid); break;
            case 'delblack':$res = $this->_userDelBlackSetting($res, $uid); break;
            // case 'friend': $res = $this->_userFriendSetting($res, $uid, $gid, $note); break;
            // case 'delfriend': $res = $this->_userDelFriendSetting($res, $uid); break;
            default:
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
                C::t('home_follow')->update_by_uid_followuid($followUid, $_G['uid'], array('mutual'=>1));
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
                C::t('common_member_count')->increase($followUid, array('follower' => 1, 'newfollower' => 1));
                notification_add($followUid, 'follower', 'member_follow_add', array('count' => $count, 'from_id'=>$uid, 'from_idtype' => 'following'), 1);
            } elseif($special) {
                $status = $special == 1 ? 1 : 0;
                C::t('home_follow')->update_by_uid_followuid($uid, $followUid, array('status'=>$status));
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

    private function _userFriendSetting($res, $uid, $gid, $note) {
        global $_G;
        require_once libfile('function/friend');
        require_once libfile('function/spacecp');
        require_once libfile('function/home');
        if(!checkperm('allowfriend')) {
            return $this->makeErrorInfo($res, 'no_privilege_addfriend');
        }

        if($uid == $_G['uid']) {
            return $this->makeErrorInfo($res, 'friend_self_error');
        }

        if(friend_check($uid)) {
            return $this->makeErrorInfo($res, 'you_have_friends');
        }

        $tospace = getuserbyuid($uid);
        if(empty($tospace)) {
            return $this->makeErrorInfo($res, 'space_does_not_exist');
        }

        if(isblacklist($tospace['uid'])) {
            return $this->makeErrorInfo($res, 'is_blacklist');
        }
        // $res['body']['gidInfo'] = $this->_getFriendGroupList();
        space_merge($space, 'count');
        space_merge($space, 'field_home');
        $maxfriendnum = checkperm('maxfriendnum'); 

        if($maxfriendnum && $space['friends'] >= $maxfriendnum + $space['addfriend']) {
            if($_G['magic']['friendnum']) {
                return $this->makeErrorInfo($res, 'enough_of_the_number_of_friends_with_magic');
            } else {
                return $this->makeErrorInfo($res, 'enough_of_the_number_of_friends');
            }
        }

        if(friend_request_check($uid)) {

            // if(submitcheck('add2submit')) {

                $_POST['gid'] = intval($gid);
                friend_add($uid, $uid);

                if(ckprivacy('friend', 'feed')) {
                    require_once libfile('function/feed');
                    feed_add('friend', 'feed_friend_title', array('touser'=>"<a href=\"home.php?mod=space&uid=$tospace[uid]\">$tospace[username]</a>"));
                }

                notification_add($uid, 'friend', 'friend_add');
                // showmessage('friends_add', dreferer(), array('username' => $tospace['username'], 'uid'=>$uid, 'from' => $_GET['from']), array('showdialog'=>1, 'showmsg' => true, 'closetime' => true));
                return $this->makeErrorInfo($res, 'friends_add', array('{username}' => $tospace['username']));
            // }

            // $op = 'add2';
            // $groupselect = empty($space['privacy']['groupname']) ? array(1 => ' checked') : array();
            // $navtitle = lang('core', 'title_friend_add');
            // include template('home/spacecp_friend');
            // exit();

        } else {
            if(C::t('home_friend_request')->count_by_uid_fuid($uid, $_G['uid'])) {
                return $this->makeErrorInfo($res, 'waiting_for_the_other_test');
            }

            $_POST['gid'] = $gid;
            $_POST['note'] = censor(htmlspecialchars(cutstr($note, strtolower(CHARSET) == 'utf-8' ? 30 : 20, '')));
            friend_add($uid, $_POST['gid'], $_POST['note']);
            $note = array(
                'uid' => $_G['uid'],
                'url' => 'home.php?mod=spacecp&ac=friend&op=add&uid='.$_G['uid'].'&from=notice',
                'from_id' => $_G['uid'],
                'from_idtype' => 'friendrequest',
                'note' => !empty($_POST['note']) ? lang('spacecp', 'friend_request_note', array('note' => $_POST['note'])) : ''
            );

            notification_add($uid, 'friend', 'friend_request', $note);

            require_once libfile('function/mail');
            $values = array(
                'username' => $tospace['username'],
                'url' => getsiteurl().'home.php?mod=spacecp&ac=friend&amp;op=request'
            );
            sendmail_touser($uid, lang('spacecp', 'friend_subject', $values), '', 'friend_add');
            return $this->makeErrorInfo($res, 'request_has_been_sent');
        }
        return $res;
    }

    private function _userDelFriendSetting($res, $delFriendUid) {
        require_once libfile('function/friend');
        friend_delete($delFriendUid);
        $params['noError'] = 1;
        return $this->makeErrorInfo($res, 'do_success', $params);
    }

    private function _getFriendGroupList() {
        $group = array();
        $groups = friend_group_list();
        foreach ($groups as $k => $v) {
            $group[] = array('fusername' => $v, 'gid' => $k);
        }
        return $group;
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