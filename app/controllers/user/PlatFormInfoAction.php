<?php

/**
 * 第三方绑定登录
 *
 * @author HanPengyu 
 * @copyright 2012-2015 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}
// Mobcent::setErrors();
class PlatFormInfoAction extends MobcentAction {

    public $password = MOBCENT_HACKER_PASSWORD;

    public function run($openId, $oauthToken, $platformId=20) {
        $this->password .= FileUtils::getRandomFileName('',3);
        $res = $this->initWebApiArray();
        $openId = rawurldecode($openId);
        $res = $this->getBindInfo($res, $openId, $oauthToken, $platformId);
        echo WebUtils::outputWebApi($res, '', false);   
    }

    public function getBindInfo($res, $openId, $oauthToken, $platformId) {
        if ($platformId == 20) {
            $res = $this->_qqInfo($res, $openId, $oauthToken, $platformId);
        } elseif ($platformId == 30) {
            $res = $this->_wxInfo($res,$openId, $oauthToken, $platformId);
        }
        return $res;
    }

    private function _qqInfo($res,$openId, $oauthToken, $platformId) {
        global $_G;
        $password = MOBCENT_HACKER_PASSWORD.FileUtils::getRandomFileName('',3);
        require_once libfile('function/member');
        if (!empty($platformId) && $platformId == 20) {

            $qqUserInfo = $this->_getQQinfoByOpenId($openId);
            if (isset($qqUserInfo) && !empty($qqUserInfo)) {

                $userInfo = UserUtils::getUserInfo($qqUserInfo['uid']);
                setloginstatus($userInfo, $_GET['cookietime'] ? 2592000 : 0);
                C::t('common_member_status')->update($userInfo['uid'], array('lastip' => $_G['clientip'], 'lastvisit' =>TIMESTAMP, 'lastactivity' => TIMESTAMP));

                $ipArray = explode('.', $_G['clientip']);
                $sid = FileUtils::getRandomFileName('',6);

                $data = array(
                    'sid' => $sid,
                    'ip1' => $ipArray[0],
                    'ip2' => $ipArray[1],
                    'ip3' => $ipArray[2],
                    'ip4' => $ipArray[3],
                    'uid' => $userInfo['uid'],
                    'username' => $userInfo['username'],
                    'groupid' => $userInfo['groupid'],
                    'invisible' => '0',
                    'action' => '',
                    'lastactivity' => time(),
                    'fid' => '0',
                    'tid' => '0',
                    'lastolupdate' => '0'
                    );

                $comSess = DzCommonSession::getComSessByUid($userInfo['uid']);
                if (!empty($comSess)) {
                    DzCommonSession::delComSess($userInfo['uid']);
                }
                DzCommonSession::insertComSess($data);
                
                $userAccess = AppbymeUserAccess::loginProcess($userInfo['uid'], $password);
                $res['body']['register'] = 0;
                $res['body']['uid'] = (int)$userInfo['uid'];
                $res['body']['userName'] = (string)$userInfo['username'];
                $res['body']['avatar'] = (string)UserUtils::getUserAvatar($userInfo['uid']);
                $res['body']['token'] = (string)$userAccess['token'];
                $res['body']['secret'] = (string)$userAccess['secret'];
                return $res;
            } else {
                $res['body']['register'] = 1;
                $res['body']['openId'] = (string)$openId;
                $res['body']['oauthToken'] = (string)$oauthToken;
                $res['body']['platformId'] = (int)$platformId;
                return $res;
            }            
        }
        
        // 客户端参数不正确
        return $this->makeErrorInfo($res, 'mobcent_error_params');
    }

    private function _wxInfo($res,$openId, $oauthToken, $platformId) {
        $wxLogin = AppbymeConnection::getMobcentWxinfoByOpenId($openId);
        if ($wxLogin) {
            $member = getuserbyuid($wxLogin['uid']);
            UserUtils::updateCookie($member, $member['uid']);
            $userAccess = AppbymeUserAccess::loginProcess($member['uid'], $this->password);
            $res['body']['register'] = 0;
            $res['body']['uid'] = (int)$member['uid'];
            $res['body']['userName'] = (string)$member['username'];
            $res['body']['avatar'] = (string)UserUtils::getUserAvatar($member['uid']);
            $res['body']['token'] = (string)$userAccess['token'];
            $res['body']['secret'] = (string)$userAccess['secret']; 
        } else {
            // 检查是否有微信登陆的插件
            $isWechat = AppbymeConnection::isWechat();
            if ($isWechat) {
                $dzWxLogin = AppbymeConnection::getWXinfoByOpenId($openId);
                if (!empty($dzWxLogin)) {

                    $member = getuserbyuid($dzWxLogin['uid']);
                    UserUtils::updateCookie($member, $member['uid']);

                    $data = array('uid' => $uid, 'openid' => $openId, 'status' => 1, 'type' => 1);
                    AppbymeConnection::insertMobcentWx($data);

                    $userAccess = AppbymeUserAccess::loginProcess($member['uid'], $this->password);
                    $res['body']['register'] = 0;
                    $res['body']['uid'] = (int)$member['uid'];
                    $res['body']['userName'] = (string)$member['username'];
                    $res['body']['avatar'] = (string)UserUtils::getUserAvatar($member['uid']);
                    $res['body']['token'] = (string)$userAccess['token'];
                    $res['body']['secret'] = (string)$userAccess['secret'];
                } else {
                    $res['body']['register'] = 1;
                    $res['body']['openId'] = (string)$openId;
                    $res['body']['oauthToken'] = (string)$oauthToken;
                    $res['body']['platformId'] = (int)$platformId;
                }
            } else {
                // 低版本的discuz！或者是没有装微信插件
                $res['body']['register'] = 1;
                $res['body']['openId'] = (string)$openId;
                $res['body']['oauthToken'] = (string)$oauthToken;
                $res['body']['platformId'] = (int)$platformId;
            }
        }
        
        return $res;
    }

    // QQ
    private function _getQQinfoByOpenId($openId) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE conopenid=%s
            ',
            array('common_member_connect', $openId)
        );
    }

}