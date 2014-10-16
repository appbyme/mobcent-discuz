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
        $res['body']['profileList'] = $this->_getPersonalDataInfo($puid, $space);
        $res['body']['creditList'] = $this->_getStatisticalInformation($uid, $space);
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

    private function _getPersonalDataInfo($puid, $space) {
        global $_G;
        $res['body']['PersonalData'] = array();

        require_once libfile('function/spacecp');
        space_merge($space, 'count');
        space_merge($space, 'field_home');
        space_merge($space, 'field_forum');
        space_merge($space, 'profile');
        space_merge($space, 'status');

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

        require_once libfile('function/friend');
        $isfriend = friend_check($space['uid'], 1);
        loadcache('profilesetting');
        include_once libfile('function/profile');
        $profiles = array();
        $privacy = $space['privacy']['profile'] ? $space['privacy']['profile'] : array();
        if($_G['setting']['verify']['enabled']) {
            space_merge($space, 'verify');
        }

        if($_G['uid'] == $space['uid'] || $_G['group']['allowviewip']) {
            foreach($_G['cache']['profilesetting'] as $fieldid => $field) {
                if(!$field['available'] || $field['invisible'] || in_array($fieldid, array('birthmonth', 'birthyear'))) {
                    continue;
                }
                $val = profile_show($fieldid, $space);
                $profiles[] = array('type' => $fieldid, 'title'=>$field['title'], 'data'=>WebUtils::emptyHtml($val));
            }
        } else {
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
                        $profiles[] = array('type' => $fieldid, 'title'=>$field['title'], 'data'=>WebUtils::emptyHtml($val));
                    }
                }
            }
        }
        return $profiles;
    }   

    private function _getStatisticalInformation($uid, $space) {
        global $_G;

        $statisticalInfos = array();
        $statisticalInfos[] = array('type' => 'credits', 'title' => WebUtils::t('积分'),'data' => (int)$space['credits']);
        if(is_array($_G['setting']['extcredits'])) {
            foreach($_G['setting']['extcredits'] as $key => $value) { 
                if($value['title']) {
                    $statisticalInfos[] = array('type' => 'extcredits' . $key, 'title' => $value['title'],'data' => (int)$space["extcredits$key"]);
                }
            }
        }
        return $statisticalInfos;
    }

    // 获取登录用户发表的帖子
    private function _getHomeTopicNum($uid) {
        return DzUserInfo::getAllHomeTopicNum($uid);
    }

    private function _getPersonalData($puid) {
        return UserUtils::getUserProfile($puid);
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