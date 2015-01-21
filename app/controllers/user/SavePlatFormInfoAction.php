<?php

/**
 * 保存第三方绑定登录信息
 *
 * @author HanPengyu 
 * @copyright 2012-2015 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}
// Mobcent::setErrors();
class SavePlatFormInfoAction extends MobcentAction {

    public function run($username, $oauthToken, $password, $openId, $email='', $gender=0, $act='register', $platformId=20) {
        $username = WebUtils::t(rawurldecode($username));
        $email = WebUtils::t(rawurldecode($email));
        $res = $this->initWebApiArray();
        $res = $this->getPlatFormInfo($res, $username, $oauthToken, $password, $openId, $email, $gender, $act, $platformId);
        echo WebUtils::outputWebApi($res, '', false);   
    }

    public function getPlatFormInfo($res, $username, $oauthToken, $password, $openId, $email, $gender, $act, $platformId) {
        if ($platformId == 20) {
            $res = $this->_saveInfo($res, $username, $email, $oauthToken, $openId, $gender, $platformId);
        } elseif ($platformId == 30) {
            $res = $this->_saveWxInfo($res, $username, $oauthToken, $password, $openId, $email, $gender, $act, $platformId);
        }
        return $res;
    }

    private function _saveInfo($res, $username, $email, $oauthToken, $openId, $gender, $platformId) {
        $username = WebUtils::t(rawurldecode($username));
        $email = WebUtils::t(rawurldecode($email));
        $password = MOBCENT_HACKER_PASSWORD;
        $regInfo = UserUtils::register($username, $password, $email, 'qq');
        if ($regInfo['errcode']) {
            return $this->makeErrorInfo($res, $regInfo['message']);
        }

        $uid = $regInfo['info']['uid'];
        $userInfo = UserUtils::getUserInfo($uid);
        $userAccess = AppbymeUserAccess::registerProcess($uid, $password);

        if (!empty($platformId) && $platformId == 20) {
            $qqdata = array(
                'uid' => $uid,
                'conuin' => $oauthToken,
                'conuinsecret' =>'',
                'conopenid' => $openId,
                'conisfeed' => 1,
                'conispublishfeed' => 1,
                'conispublisht' => 1,
                'conisregister' => 1,
                'conisqzoneavatar' => 1,
                'conisqqshow' => 1,
                );
            $qqbind = array('mblid'=>'','uid'=>$uid,'uin' =>$openId,'type'=>1,'dateline'=>time());
            $this->_inserBindlog($qqbind);
            $this->_inserConnect($qqdata);
            $updateInfo = array('avatarstatus' => 1, 'conisbind' => 1); // 用户是否绑定QQ
            DzCommonMember::updateMember($updateInfo, array('uid' => $uid));
            $setarr ['gender'] = intval($gender);
            C::t ('common_member_profile')->update($uid, $setarr);

            $ipArray = explode('.', $_G['clientip']);
            $sid = FileUtils::getRandomFileName('',6);
            $data = array(
                'sid' => $sid,
                'ip1' => $ipArray[0],
                'ip2' => $ipArray[1],
                'ip3' => $ipArray[2],
                'ip4' => $ipArray[3],
                'uid' => $userInfo['uid'],
                'username' =>$userInfo['username'],
                'groupid' => $userInfo['groupid'],
                'invisible' =>'0',
                'action' => '' ,
                'lastactivity' => time(),
                'fid' => '0',
                'tid' => '0',
                'lastolupdate' => '0'
                );
                DzCommonSession::insertComSess($data);
                require_once libfile('cache/userstats', 'function');
                build_cache_userstats();

                $res['token'] = (string)$userAccess['token'];
                $res['secret'] = (string)$userAccess['secret'];
                $res['uid'] = (int)$regInfo['info']['uid'];
                return $res;
        }
        // 客户端参数不正确
        return $this->makeErrorInfo($res, 'mobcent_error_params');

    }

    private function _inserBindlog($data) {
        return DbUtils::getDzDbUtils(true)->insert('connect_memberbindlog', $data);
    }

    private function _inserConnect($data) {
        return DbUtils::getDzDbUtils(true)->insert('common_member_connect', $data);
    }

    private function _saveWxInfo($res, $username, $oauthToken, $password, $openId, $email, $gender, $act, $platformId) {
        if ($act == 'register') {

            $regInfo = UserUtils::register($username, $password, $email);
            if ($regInfo['errcode']) {
                return $this->makeErrorInfo($res, $regInfo['message']);
            }

            $uid = $regInfo['info']['uid'];
            $member = UserUtils::getUserInfo($uid);
            $userInfo = AppbymeUserAccess::registerProcess($regInfo['info']['uid'], $password);

            $data = array('uid' => $uid, 'openid' => $openId, 'status' => 1, 'type' => 1);
            AppbymeConnection::insertMobcentWx($data);

            $res['body']['uid'] = (int)$uid;
            $res['body']['token'] = (string)$userInfo['token'];
            $res['body']['secret'] = (string)$userInfo['secret'];
        } elseif($act == 'bind') {
            
            global $_G;
            $logInfo = UserUtils::login($username, $password);
            if ($logInfo['errcode']) {
                UserUtils::delUserAccessByUsername($username);
                return $this->makeErrorInfo($res, $logInfo['message']);
            }

            $data = array('uid' => $_G['uid'], 'openid' => $openId, 'status' => 1, 'type' => 1);
            AppbymeConnection::insertMobcentWx($data);

            $userInfo = AppbymeUserAccess::loginProcess($_G['uid'], $password);
            $userAvatar = UserUtils::getUserAvatar($_G['uid']);
            $res['token'] = (string)$userInfo['token'];
            $res['secret'] = (string)$userInfo['secret'];
            $res['uid'] = (int)$_G['uid'];
            $res['avatar'] = (string)$userAvatar;
            $res['userName'] = (string)$_G['username'];
        }
        return $res;
    }
            

}