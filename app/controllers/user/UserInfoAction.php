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
        $res = $this->_getPersonalDataInfo($res, $uid);
        // var_dump('expression');exit;
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

    private function _getPersonalDataInfo($res, $uid) {
        $PersonalData = $this->_getPersonalData($uid);

        $space = $PersonalData;
        space_merge($space, 'count');
        space_merge($space, 'field_home');
        space_merge($space, 'field_forum');
        space_merge($space, 'profile');
        space_merge($space, 'status');
        getonlinemember(array($space['uid']));
        $space['admingroup'] = $_G['cache']['usergroups'][$space['adminid']];
        $space['admingroup']['icon'] = g_icon($space['adminid'], 1);

        $space['group'] = $_G['cache']['usergroups'][$space['groupid']];
        $space['group']['icon'] = g_icon($space['groupid'], 1);
        $encodeusername = rawurlencode($space['username']);

        if($space['extgroupids']) {
            $newgroup = array();
            $e_ids = explode(',', $space['extgroupids']);
            foreach ($e_ids as $e_id) {
                $newgroup[] = $_G['cache']['usergroups'][$e_id]['grouptitle'];
            }
            $space['extgroupids'] = implode(',', $newgroup);
        }

        $space['regdate'] = dgmdate($space['regdate']);
        if($space['lastvisit']) $space['lastvisit'] = dgmdate($space['lastvisit']);
        if($space['lastactivity']) {
            $space['lastactivitydb'] = $space['lastactivity'];
            $space['lastactivity'] = dgmdate($space['lastactivity']);
        }
        if($space['lastpost']) $space['lastpost'] = dgmdate($space['lastpost']);
        if($space['lastsendmail']) $space['lastsendmail'] = dgmdate($space['lastsendmail']);


        if($_G['uid'] == $space['uid'] || $_G['group']['allowviewip']) {
            require_once libfile('function/misc');
            $space['regip_loc'] = convertip($space['regip']);
            $space['lastip_loc'] = convertip($space['lastip']);
        }

        $space['buyerrank'] = 0;
        if($space['buyercredit']){
            foreach($_G['setting']['ec_credit']['rank'] AS $level => $credit) {
                if($space['buyercredit'] <= $credit) {
                    $space['buyerrank'] = $level;
                    break;
                }
            }
        }

        $space['sellerrank'] = 0;
        if($space['sellercredit']){
            foreach($_G['setting']['ec_credit']['rank'] AS $level => $credit) {
                if($space['sellercredit'] <= $credit) {
                    $space['sellerrank'] = $level;
                    break;
                }
            }
        }

        $space['attachsize'] = formatsize($space['attachsize']);

        $space['timeoffset'] = empty($space['timeoffset']) ? '9999' : $space['timeoffset'];
        if(strtotime($space['regdate']) + $space['oltime'] * 3600 > TIMESTAMP) {
            $space['oltime'] = 0;
        }
        require_once libfile('function/friend');
        $isfriend = friend_check($space['uid'], 1);

        loadcache('profilesetting');
        include_once libfile('function/profile');
        $profiles = array();
        $privacy = $space['privacy']['profile'] ? $space['privacy']['profile'] : array();

        if($_G['setting']['verify']['enabled']) {
            space_merge($space, 'verify');
        }
        foreach($_G['cache']['profilesetting'] as $fieldid => $field) {
            if(!$field['available'] || in_array($fieldid, array('birthprovince', 'birthdist', 'birthcommunity', 'resideprovince', 'residedist', 'residecommunity'))) {
                    continue;
            }
            if(
                $field['available'] && (strlen($space[$fieldid]) > 0 || ($fieldid == 'birthcity' && strlen($space['birthprovince']) || $fieldid == 'residecity' && strlen($space['resideprovince']))) &&
                ($space['self'] || empty($privacy[$fieldid]) || ($isfriend && $privacy[$fieldid] == 1)) &&
                (!$_G['inajax'] && !$field['invisible'] || $_G['inajax'] && $field['showincard'])
            ) {
                $val = profile_show($fieldid, $space);
                if($val !== false) {
                    if($fieldid == 'realname' && $_G['uid'] != $space['uid'] && !ckrealname(1)) {
                        continue;
                    }
                    if($field['formtype'] == 'file' && $val) {
                        $imgurl = getglobal('setting/attachurl').'./profile/'.$val;
                        $val = '<span><a href="'.$imgurl.'" target="_blank"><img src="'.$imgurl.'"  style="max-width: 500px;" /></a></span>';
                    }
                    if($val == '')  $val = '-';
                    $profiles[$fieldid] = array('title'=>$field['title'], 'value'=>$val);
                }
            }
        }

        $count = C::t('forum_moderator')->count_by_uid($space['uid']);
        if($count) {
            foreach(C::t('forum_moderator')->fetch_all_by_uid($space['uid']) as $result) {
                $moderatefids[] = $result['fid'];
            }
            $query = C::t('forum_forum')->fetch_all_info_by_fids($moderatefids);
            foreach($query as $result) {
                $manage_forum[$result['fid']] = $result['name'];
            }
        }

        if(!$_G['inajax'] && $_G['setting']['groupstatus']) {
            $gorupcount = C::t('forum_groupuser')->fetch_all_group_for_user($space['uid'], 1);
            if($groupcount > 0) {
                $fids = C::t('forum_groupuser')->fetch_all_fid_by_uids($space['uid']);
                $usergrouplist = C::t('forum_forum')->fetch_all_info_by_fids($fids);
            }
        }

        if($space['medals']) {
                loadcache('medals');
                foreach($space['medals'] = explode("\t", $space['medals']) as $key => $medalid) {
                        list($medalid, $medalexpiration) = explode("|", $medalid);
                        if(isset($_G['cache']['medals'][$medalid]) && (!$medalexpiration || $medalexpiration > TIMESTAMP)) {
                                $space['medals'][$key] = $_G['cache']['medals'][$medalid];
                                $space['medals'][$key]['medalid'] = $medalid;
                        } else {
                                unset($space['medals'][$key]);
                        }
                }
        }
        $upgradecredit = $space['uid'] && $space['group']['type'] == 'member' && $space['group']['creditslower'] != 9999999 ? $space['group']['creditslower'] - $space['credits'] : false;
        $allowupdatedoing = $space['uid'] == $_G['uid'] && checkperm('allowdoing');

        dsetcookie('home_diymode', 1);

        $navtitle = lang('space', 'sb_profile', array('who' => $space['username']));
        $metakeywords = lang('space', 'sb_profile', array('who' => $space['username']));
        $metadescription = lang('space', 'sb_profile', array('who' => $space['username']));

        $showvideophoto = true;
        if($space['videophotostatus'] > 0 && $_G['uid'] != $space['uid'] && !ckvideophoto($space, 1)) {
            $showvideophoto = false;
        }

        $clist = array();
        if(in_array($_G['adminid'], array(1, 2, 3))) {
            include_once libfile('function/member');
            $clist = crime('getactionlist', $space['uid']);
        }
// var_dump('expression');exit;
        return $res['body']['PersonalData'] = $space;
    }   


    // 获取登录用户发表的帖子
    private function _getHomeTopicNum($uid) {
        return DzUserInfo::getAllHomeTopicNum($uid);
    }

    private function _getPersonalData($uid) {
        return UserUtils::getUserProfile($uid);
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