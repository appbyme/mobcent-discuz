<?php

/**
 * Utils about user
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @author HanPengyu
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class UserUtils {

    /**
     * 用户登陆状态
     */
    const STATUS_OFFLINE = 0;
    const STATUS_ONLINE_INVISIBLE = 1;
    const STATUS_ONLINE = 2;

    /**
     * 用户性别
     */
    const GENDER_SECRET = 0;
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    /**
     * get user's avatar
     * copy and modify by DISCUZ avatar function 
     *
     * @param int $uid
     * @param string $size
     * @return string
     */
    public static function getUserAvatar($uid, $size = 'middle') {
        global $_G;
        $ucenterurl = $_G['setting']['ucenterurl'];
        $size = in_array($size, array('big', 'middle', 'small')) ? $size : 'middle';
        $uid = abs(intval($uid));

        return $ucenterurl . '/avatar.php?uid=' . $uid . '&size='. $size; 
    }

    public static function getUserName($uid) {
        $user = self::getUserInfo($uid);
        return !empty($user) ? $user['username'] : '';
    }

    public static function getUserGender($uid) {
        $user = self::getUserProfile($uid);
        return !empty($user) ? (int)$user['gender'] : self::GENDER_SECRET;
    }
    
    // 获取用户等级名称
    public static function getUserTitle($uid) {
        $userTitle = '';
        $userInfo = UserUtils::getUserInfo($uid);
        if (!empty($userInfo)) {
            $groupId = $userInfo['groupid'];
            $userGroup = UserUtils::getUserGroupsByGids($groupId);
            $userTitle = (string)WebUtils::emptyHtml($userGroup[$groupId]['grouptitle']);
        }
        return $userTitle;
    }

    public static function getUserLevelIcon($uid) {
        // from funtion_forumlist showstars
        $icon = array('sun' => 0, 'moon' => 0, 'star' => 0);
        
        global $_G;
        $user = self::getUserInfo($uid);
        if (!empty($user)) {
            $num = $stars = $_G['cache']['usergroups'][$user['groupid']]['stars'];
            if(empty($_G['setting']['starthreshold'])) {
                for($i = 0; $i < $num; $i++) {
                    $icon['star']++;
               }
            } else {
                $maps = array('1' => 'star', 'moon', 'sun');
                for($i = 3; $i > 0; $i--) {
                    $numlevel = intval($num / pow($_G['setting']['starthreshold'], ($i - 1)));
                    $num = ($num % pow($_G['setting']['starthreshold'], ($i - 1)));
                    for($j = 0; $j < $numlevel; $j++) {
                        $icon[$maps[$i]]++;
                    }
                }
            }            
        }
        
        return $icon;
    }

    /**
     * get user's info
     * copy from DISCUZ getuserbyuid function 
     */
    public static function getUserInfo($uid) {
        return getuserbyuid($uid);
    }

    public static function getUserProfile($uid) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE uid=%d
            ',
            array('common_member_profile', $uid)
        );
    }

    /**
     * 判断用户登陆状态
     * 
     * @param int $uid 用户id
     *
     * @return int 0为不在线, 1为隐身登陆, 2为在线登陆
     */
    public static function getUserLoginStatus($uid) {
        $invisible = DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT invisible
            FROM %t
            WHERE uid=%d
            ', 
            array('common_session', $uid)
        );
        return $invisible !== false ? 
            ($invisible == 1 ? self::STATUS_ONLINE_INVISIBLE : self::STATUS_ONLINE) :
            self::STATUS_OFFLINE;
    }
    
    /**
     * 判断用户是否为好友
     *
     * @param int $uid 主用户id
     * @param int $fuid 要检测的用户id
     *
     * @return bool false为非好友, true为好友
     */
    public static function isFriend($uid , $fuid) {
    	$res = (int)DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT COUNT(*)
            FROM %t
            WHERE uid=%d AND fuid=%d           
            ',
            array('home_friend', $uid, $fuid)
        );
    	return $res !== 0;
    }
    
    /**
     * 判断用户是否在黑名单
     *
     * @param int $uid 主用户id
     * @param int $buid 要检测的用户id
     *
     * @return bool true为加入黑名单, false为没有加入黑名单
     */
    public static function isBlacklist($uid , $buid) {
        $res = (int)DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT COUNT(*)
            FROM %t
            WHERE uid=%d AND buid=%d
            ',
            array('home_blacklist', $uid, $buid)
        );
    	return $res !== 0;
    }

    /**
     * 判断用户是否关注了某个用户
     *
     * @param int $uid 用户id
     * @param int $fuid 关注的用户id
     *
     * @return bool
     */
    public static function isFollow($uid, $fuid) {
        $res = (int)DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT COUNT(*)
            FROM %t
            WHERE uid=%d AND followuid=%d
            ',
            array('home_follow', $uid, $fuid)
        );
        return $res !== 0;
    }

    /**
     * 判断该用户是否开启GPS定位功能
     */
    public static function isGPSLocationOn($uid) {
        return AppbymeUserSetting::isGPSLocationOn($uid);
    }

    /**
     * 获取用户组信息
     * 
     * @param string|array $gids 用户组id
     *
     * @return array
     */
    public static function getUserGroupsByGids($gids) {
        return DzCommonUserGroup::getUserGroupsByGids($gids);
    }

    /**
     * 获取当前用户及其对应版块的权限
     * 
     * @param string $fids 版块id集合
     *
     * @return array
     */
    public static function getPermission($fids) {
        $permission = array();

        global $_G;
        $tempGroupAllowPostImage = $_G['group']['allowpostimage'];
        $tempGroupAllowPostAttach = $_G['group']['allowpostattach'];

        $forumInfos = ForumUtils::getForumInfos($fids);
        foreach ($forumInfos as $forum) {
            $fid = (int)$forum['fid'];
            
            ForumUtils::initForum($fid);

            // 获取上传图片权限
            $_G['forum']['allowpostimage'] = isset($_G['forum']['allowpostimage']) ? $_G['forum']['allowpostimage'] : '';
            $_G['group']['allowpostimage'] = $tempGroupAllowPostImage;
            $_G['group']['allowpostimage'] = $_G['forum']['allowpostimage'] != -1 && ($_G['forum']['allowpostimage'] == 1 || (!$_G['forum']['postimageperm'] && $_G['group']['allowpostimage']) || ($_G['forum']['postimageperm'] && forumperm($_G['forum']['postimageperm'])));
            require_once libfile('function/upload');
            $swfconfig = getuploadconfig($_G['uid'], $_G['fid']);
            $imgexts = str_replace(array(';', '*.'), array(', ', ''), $swfconfig['imageexts']['ext']);
            $allowpostimg = $_G['group']['allowpostimage'] && $imgexts;
            $allowPostImage = $allowpostimg ? 1 : 0;

            $allowAnonymous = $_G['forum']['allowanonymous'] || $_G['group']['allowanonymous'] ? 1 : 0;
            $_G['forum']['allowpostattach'] = isset($_G['forum']['allowpostattach']) ? $_G['forum']['allowpostattach'] : '';
            $_G['group']['allowpostattach'] = $tempGroupAllowPostAttach;
            $_G['group']['allowpostattach'] = $_G['forum']['allowpostattach'] != -1 && ($_G['forum']['allowpostattach'] == 1 || (!$_G['forum']['postattachperm'] && $_G['group']['allowpostattach']) || ($_G['forum']['postattachperm'] && forumperm($_G['forum']['postattachperm'])));
            $allowPostAttachment = $_G['group']['allowpostattach'] ? 1 : 0;

            $topicClassfications = ForumUtils::getTopicClassificationInfos($fid);

            $permission[] =array(
                'fid' => $fid,
                'topic' => array(
                    'isHidden' => 0,
                    'isAnonymous' => $allowAnonymous,
                    'isOnlyAuthor'=> 1,
                    'allowPostAttachment' => $allowPostAttachment,
                    'allowPostImage' => $allowPostImage,
                    'newTopicPanel' => ForumUtils::getNewTopicPanel(),
                    'classificationType_list' => $topicClassfications['types'],
                    'isOnlyTopicType' => $topicClassfications['requireTypes'] ? 1 : 0,
                ),
                'post' => array(
                    'isHidden' => 0,
                    'isAnonymous' => $allowAnonymous,
                    'isOnlyAuthor' => 0,
                    'allowPostAttachment' => $allowPostAttachment,
                    'allowPostImage' => $allowPostImage,
                ),
            );
        }

        return $permission;
    }

    public static function getUserIdByAccess() {
        $accessToken = isset($_GET['accessToken']) ? $_GET['accessToken'] : '';
        $accessSecret = isset($_GET['accessSecret']) ? $_GET['accessSecret'] : '';
        return AppbymeUserAccess::getUserIdByAccess($accessToken, $accessSecret);
    }

    public static function checkAccess() {
        global $_G;
        return $_G['uid'] > 0;
    }

    /**
     * 获取马甲列表
     * 
     * @author HanPengyu
     * @param int $uid 
     * @author HanPengyu
     * @return array $userlist 
     */
    public static function getRepeatList($uid) {
        global $_G;
        $userList = array();
        if ($_G['setting']['plugins']['spacecp']['myrepeats:memcp']){
            foreach(C::t('#myrepeats#myrepeats')->fetch_all_by_uid($uid) as $user) {
                $userlist[] = $user['username'];
            }                
        }
        return $userlist;
    }


    /**
     * 用户登录操作
     *
     * @author HanPengyu
     * @param string $username 用户名.
     * @param string $password 用户密码.
     * @return 
     */
    public static function login($username, $password) {
        global $_G;
        $_GET['username'] = $username;
        $_GET['password'] = $password;
        $_GET['questionid'] = $_GET['answer'] = '';
        $_GET['loginfield'] = 'username';

        require_once libfile('function/member');
        require_once libfile('class/member');
        require_once libfile('function/misc');
        require_once libfile('function/mail');

        loaducenter();

        $invite = getinvite();

        $_G['uid'] = $_G['member']['uid'] = 0;
        $_G['username'] = $_G['member']['username'] = $_G['member']['password'] = '';        

        if (trim($_GET['username']) == '') {
            return self::errorInfo('user_name_null');
        }

        if(!($_G['member_loginperm'] = logincheck($_GET['username']))) {
            // 密码错误次数过多，请 15 分钟后重新登录,后面还会进行判断
            return self::errorInfo( lang('message', 'login_strike') );
        }

        if(!$_GET['password'] || $_GET['password'] != addslashes($_GET['password'])) {
            // 抱歉，密码空或包含非法字符
            return self::errorInfo(lang('message', 'profile_passwd_illegal'));
        }
        $result = userlogin($_GET['username'], $_GET['password'], $_GET['questionid'], $_GET['answer'], 'username', $_G['clientip']);

        if ($result['ucresult']['uid'] == '-3') {
            $userInfo = DzCommonMember::getUidByUsername($result['ucresult']['username']);
            $result['ucresult']['uid'] = $userInfo['uid'];
            $result['member'] = $userInfo;
            $result['status'] = 1;
        }

        $uid = $_G['uid'] = $result['ucresult']['uid'];
        $userName = $result['ucresult']['username'];
        $userAvatar = UserUtils::getUserAvatar($uid);

        $ctlObj = new logging_ctl();
        $ctlObj->setting = $_G['setting'];

        if($result['status'] == -1) {
            if(!$ctlObj->setting['fastactivation']) {
                // 帐号没有激活
                return self::errorInfo(Yii::t('mobcent', 'location_activation'));
            } else {
                // 自动激活
                $init_arr = explode(',', $ctlObj->setting['initcredits']);
                $groupid = $ctlObj->setting['regverify'] ? 8 : $ctlObj->setting['newusergroupid'];
                C::t('common_member')->insert($uid, $result['ucresult']['username'], md5(random(10)), $result['ucresult']['email'], $_G['clientip'], $groupid, $init_arr);
                $result['member'] = getuserbyuid($uid);
                $result['status'] = 1;
            }
        }

        if($result['status'] > 0) {

            if($ctlObj->extrafile && file_exists($ctlObj->extrafile)) {
                require_once $ctlObj->extrafile;
            }
            setloginstatus($result['member'], $_GET['cookietime'] ? 2592000 : 0);
            checkfollowfeed();

            C::t('common_member_status')->update($_G['uid'], array('lastip' => $_G['clientip'], 'lastvisit' =>TIMESTAMP, 'lastactivity' => TIMESTAMP));
            $ucsynlogin = $ctlObj->setting['allowsynlogin'] ? uc_user_synlogin($_G['uid']) : '';

            if($invite['id']) {
                $result = C::t('common_invite')->count_by_uid_fuid($invite['uid'], $uid);
                if(!$result) {
                    C::t('common_invite')->update($invite['id'], array('fuid'=>$uid, 'fusername'=>$_G['username']));
                    updatestat('invite');
                } else {
                    $invite = array();
                }
            }

            if($invite['uid']) {
                require_once libfile('function/friend');
                friend_make($invite['uid'], $invite['username'], false);
                dsetcookie('invite_auth', '');
                if($invite['appid']) {
                    updatestat('appinvite');
                }
            }

            return self::errorInfo('',0);

        } else {
            $password = preg_replace("/^(.{".round(strlen($_GET['password']) / 4)."})(.+?)(.{".round(strlen($_GET['password']) / 6)."})$/s", "\\1***\\3", $_GET['password']);
            $errorlog = dhtmlspecialchars(
                TIMESTAMP."\t".
                ($result['ucresult']['username'] ? $result['ucresult']['username'] : $_GET['username'])."\t".
                $password."\t".
                "Ques #".intval($_GET['questionid'])."\t".
                $_G['clientip']);
            writelog('illegallog', $errorlog);
            loginfailed($_GET['username']);

            if($_G['member_loginperm'] > 1) {
                // 登录失败,还可以尝试几次
                return self::errorInfo(lang('message', 'login_invalid',array('loginperm' => $_G['member_loginperm'] - 1)));
            } elseif($_G['member_loginperm'] == -1) {
                // 抱歉，您输入的密码有误
                return self::errorInfo(lang('message', 'login_password_invalid'));
            } else {
                // 密码错误次数过多，请 15 分钟后重新登录
                return self::errorInfo(lang('message', 'login_strike'));
            }
        }
        
    }


    /**
     * 退出登录
     *
     * @author HanPengyu
     * @return 退出登录信息
     */
    public static function logout() {
        global $_G;
        require_once libfile('function/member');
        require_once libfile('class/member');
        $ctlObj = new logging_ctl();
        $ctlObj->setting = $_G['setting'];
        clearcookies();
        $_G['groupid'] = $_G['member']['groupid'] = 7;
        $_G['uid'] = $_G['member']['uid'] = 0;
        $_G['username'] = $_G['member']['username'] = $_G['member']['password'] = '';
        $_G['setting']['styleid'] = $ctlObj->setting['styleid'];
        if(empty($_G['uid']) && empty($_G['username'])) {
            $accessToken = (string)$_GET['accessToken'];
            $accessSecret = (string)$_GET['accessSecret'];
            $userId = AppbymeUserAccess::getUserIdByAccess($accessToken, $accessSecret);
            if ($userId) {
                DB::query('DELETE FROM '.DB::table('common_session').' WHERE uid='.$userId);
            }
        }
        return  self::errorInfo(lang('message', 'modcp_logout_succeed'));
    }
    

    /**
     * 用户注册
     * 
     * @author HanPengyu
     * @param string  $username 用户名.
     * @param string  $password 用户密码.
     * @param string  $email    用户邮件.
     * @param string  $type     注册类型,默认general.
     * @return array .
     */
    public static function register($username, $password, $email, $type='general') {
        global $_G;
        require_once libfile('function/member');
        require libfile('class/member');
        require_once libfile('function/misc');
        loaducenter();

        $ctlObj = new register_ctl();
        $ctlObj->setting = $_G['setting'];

        // 客户端是否开启注册功能
        $mobAllowReg = WebUtils::getDzPluginAppbymeAppConfig('mobile_allow_register');
        if ($mobAllowReg === '0') {
            return self::errorInfo(Webutils::t('客户端不允许注册'));
        }

        // 客户端是否开启跳转web页注册

        // 系统是否允许注册
        if(!$ctlObj->setting['regclosed'] && (!$ctlObj->setting['regstatus'] || !$ctlObj->setting['ucactivation'])) {
            if(!$ctlObj->setting['regstatus']) {
                $message = !$ctlObj->setting['regclosemessage'] ? 'register_disable' : str_replace(array("\r", "\n"), '', $ctlObj->setting['regclosemessage']);
                return self::errorInfo(lang('message', $message));
            }
        }
        // $username = isset($username) ? trim(WebUtils::t($username)) : '';
        $password = isset($password) ? $password : '';
        // $password2 = isset($password2) ? $password2 : '';
        $email = strtolower(trim($email));

        if($ctlObj->setting['regverify']) {
            // 对注册 IP 的限制
            if($ctlObj->setting['areaverifywhite']) {
                $location = $whitearea = '';
                $location = trim(convertip($_G['clientip'], "./"));
                if($location) {
                    $whitearea = preg_quote(trim($ctlObj->setting['areaverifywhite']), '/');
                    $whitearea = str_replace(array("\\*"), array('.*'), $whitearea);
                    $whitearea = '.*'.$whitearea.'.*';
                    $whitearea = '/^('.str_replace(array("\r\n", ' '), array('.*|.*', ''), $whitearea).')$/i';
                    if(@preg_match($whitearea, $location)) {
                        $ctlObj->setting['regverify'] = 0;
                    }
                }
            }

            if($_G['cache']['ipctrl']['ipverifywhite']) {
                foreach(explode("\n", $_G['cache']['ipctrl']['ipverifywhite']) as $ctrlip) {
                    if(preg_match("/^(".preg_quote(($ctrlip = trim($ctrlip)), '/').")/", $_G['clientip'])) {
                        $ctlObj->setting['regverify'] = 0;
                        break;
                    }
                }
            }
        }

        if($ctlObj->setting['regverify'] && $type == 'general') {
            $groupinfo['groupid'] = 8;
        } else {
            $groupinfo['groupid'] = $ctlObj->setting['newusergroupid'];
        }
        $usernamelen = dstrlen($username);
        if($usernamelen < 3) {
            return self::errorInfo(lang('message', 'profile_username_tooshort'));
        } elseif($usernamelen > 15) {
            return self::errorInfo(lang('message', 'profile_username_toolong'));
        }

        if($ctlObj->setting['pwlength']) {
            if(strlen($password) < $ctlObj->setting['pwlength']) {
                // 密码最小的长度
                return self::errorInfo(lang('message', 'profile_password_tooshort', array('pwlength' => $ctlObj->setting['pwlength'])));
            }
        }

        // 密码复杂度的限制
        if($ctlObj->setting['strongpw']) {
            $strongpw_str = array();
            if(in_array(1, $ctlObj->setting['strongpw']) && !preg_match("/\d+/", $password)) {
                $strongpw_str[] = lang('member/template', 'strongpw_1');
            }
            if(in_array(2, $ctlObj->setting['strongpw']) && !preg_match("/[a-z]+/", $password)) {
                $strongpw_str[] = lang('member/template', 'strongpw_2');
            }
            if(in_array(3, $ctlObj->setting['strongpw']) && !preg_match("/[A-Z]+/", $password)) {
                $strongpw_str[] = lang('member/template', 'strongpw_3');
            }
            if(in_array(4, $ctlObj->setting['strongpw']) && !preg_match("/[^a-zA-z0-9]+/", $password)) {
                $strongpw_str[] = lang('member/template', 'strongpw_4');
            }
            if($strongpw_str) {
                // 密码太弱，密码中必须包含什么
                return self::errorInfo(lang('member/template', 'password_weak').implode(',', $strongpw_str));
            }
        }

        // if($password !== $password2) {
        //     // 两次输入的密码不同
        //     return WebUtils::makeErrorInfo_oldVersion($res, lang('message', 'profile_passwd_notmatch'));
        // }

        if(!$password || $password != addslashes($password)) {
            // 密码有特殊的字符
            return self::errorInfo(lang('message', 'profile_passwd_illegal'));
        }

        $censorexp = '/^('.str_replace(array('\\*', "\r\n", ' '), array('.*', '|', ''), preg_quote(($ctlObj->setting['censoruser'] = trim($ctlObj->setting['censoruser'])), '/')).')$/i';

        if($ctlObj->setting['censoruser'] && @preg_match($censorexp, $username)) {
            // 用户名包含被系统屏蔽的字符
            return self::errorInfo(lang('message', 'profile_username_protect'));
        }

        // 这里是对ip注册的限制
        if($_G['cache']['ipctrl']['ipregctrl']) {
            foreach(explode("\n", $_G['cache']['ipctrl']['ipregctrl']) as $ctrlip) {
                if(preg_match("/^(".preg_quote(($ctrlip = trim($ctrlip)), '/').")/", $_G['clientip'])) {
                    $ctrlip = $ctrlip.'%';
                    $ctlObj->setting['regctrl'] = $ctlObj->setting['ipregctrltime'];
                    break;
                } else {
                    $ctrlip = $_G['clientip'];
                }
            }
        } else {
            $ctrlip = $_G['clientip'];
        }

        // ip在一定时间内不能注册
        if($ctlObj->setting['regctrl']) {
            if(C::t('common_regip')->count_by_ip_dateline($ctrlip, $_G['timestamp']-$ctlObj->setting['regctrl']*3600)) {
                return self::errorInfo(lang('message', 'register_ctrl', array('regctrl' => $ctlObj->setting['regctrl'])));
            }
        }

        // IP 地址在 24 小时内只能注册几次
        $setregip = null;
        if($ctlObj->setting['regfloodctrl']) {
            $regip = C::t('common_regip')->fetch_by_ip_dateline($_G['clientip'], $_G['timestamp']-86400);
            if($regip) {
                if($regip['count'] >= $ctlObj->setting['regfloodctrl']) {
                    return self::errorInfo(lang('message', 'register_flood_ctrl', array('regfloodctrl' => $ctlObj->setting['regfloodctrl'])));
                } else {
                    $setregip = 1;
                }
            } else {
                $setregip = 2;
            }
        }

        $uid = uc_user_register(addslashes($username), $password, $email, '', '', $_G['clientip']);
        if($uid <= 0) {
            if($uid == -1) {
                // 用户名包含敏感字符
                return self::errorInfo(lang('message', 'profile_username_illegal'));
            } elseif($uid == -2) {
                // 用户名包含被系统屏蔽的字符
                return self::errorInfo(lang('message', 'profile_username_protect'));
            } elseif($uid == -3) {
                // 该用户名已被注册
                return self::errorInfo(lang('message', 'profile_username_duplicate'));
            } elseif($uid == -4) {
                // Email 地址无效
                return self::errorInfo(lang('message', 'profile_email_illegal'));
            } elseif($uid == -5) {
                // 抱歉，Email 包含不可使用的邮箱域名
                return self::errorInfo(lang('message', 'profile_email_domain_illegal'));
            } elseif($uid == -6) {
                // 该 Email 地址已被注册
                return self::errorInfo(lang('message', 'profile_email_duplicate'));
            }
        }

        $_G['username'] = $username;

        if($setregip !== null) {
            if($setregip == 1) {
                C::t('common_regip')->update_count_by_ip($_G['clientip']);
            } else {
                C::t('common_regip')->insert(array('ip' => $_G['clientip'], 'count' => 1, 'dateline' => $_G['timestamp']));
            }
        }

        $profile = $verifyarr = array ();
        $emailstatus = 0;
        $init_arr = array('credits' => explode(',', $ctlObj->setting['initcredits']), 'profile'=>$profile, 'emailstatus' => $emailstatus);

        C::t('common_member')->insert($uid, $username, $password, $email, $_G['clientip'], $groupinfo['groupid'], $init_arr);

        if($ctlObj->setting['regctrl'] || $ctlObj->setting['regfloodctrl']) {
            C::t('common_regip')->delete_by_dateline($_G['timestamp']-($ctlObj->setting['regctrl'] > 72 ? $ctlObj->setting['regctrl'] : 72)*3600);
            if($ctlObj->setting['regctrl']) {
                C::t('common_regip')->insert(array('ip' => $_G['clientip'], 'count' => -1, 'dateline' => $_G['timestamp']));
            }
        }

        if($ctlObj->setting['regverify'] == 1) {
            $idstring = random(6);
            $authstr = $ctlObj->setting['regverify'] == 1 ? "$_G[timestamp]\t2\t$idstring" : '';
            C::t('common_member_field_forum')->update($uid, array('authstr' => $authstr));
            $verifyurl = "{$_G[siteurl]}member.php?mod=activate&amp;uid=$uid&amp;id=$idstring";
            $email_verify_message = lang('email', 'email_verify_message', array(
                'username' => $username,
                'bbname' => $ctlObj->setting['bbname'],
                'siteurl' => $_G['siteurl'],
                'url' => $verifyurl
            ));
            if(!sendmail("$username <$email>", lang('email', 'email_verify_subject'), $email_verify_message)) {
                runlog('sendmail', "$email sendmail failed.");
            }
        }

        $_GET['regmessage'] = Webutils::t('来自手机客户端注册');
        $regmessage = dhtmlspecialchars($_GET['regmessage']);
        if($ctlObj->setting['regverify'] == 2) {
            C::t('common_member_validate')->insert(array(
                'uid' => $uid,
                'submitdate' => $_G['timestamp'],
                'moddate' => 0,
                'admin' => '',
                'submittimes' => 1,
                'status' => 0,
                'message' => $regmessage,
                'remark' => '',
            ), false, true);
            manage_addnotify('verifyuser');
        }



        setloginstatus(array(
            'uid' => $uid,
            'username' => $_G['username'],
            'password' => $password,
            'groupid' => $groupinfo['groupid'],
        ), 0);

        // 统计用户表
        include_once libfile('function/stat');
        updatestat('register');

        return self::errorInfo('', 0, array('uid'=>$uid));
         
    }

    /**
     * 返回错误信息数组
     *
     * @author HanPengyu
     * @param string $message 错误信息.
     * @param int    $errcode 错误码.
     * @param array  $info
     * @return mixed Value.
     */
    public static function errorInfo($message='', $errcode=1, $info=array()) {
        return array(
            'message' => $message,
            'errcode' => $errcode,
            'info' => $info
        );
    }

}