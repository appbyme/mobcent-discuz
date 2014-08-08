<?php

/**
 * 保存QQ登录信息
 *
 * @author HanPengyu 
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}
// Mobcent::setErrors();
class SaveQQInfoAction extends MobcentAction {

    public function run($username, $email, $oauthToken, $openId, $gender=0, $platformId=20) {
        $res = $this->initWebApiArray();
        $res = $this->_saveInfo($res, $username, $email, $oauthToken, $openId, $gender, $platformId);
        echo WebUtils::outputWebApi($res, '', false);       
    }

    private function _saveInfo($res, $username, $email, $oauthToken, $openId, $gender, $platformId) {
        $username = WebUtils::t(rawurldecode($username));
        $email = WebUtils::t(rawurldecode($email));
        $password = 'mobcent';
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


}