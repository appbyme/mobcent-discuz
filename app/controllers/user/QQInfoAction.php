<?php

/**
 * 获取QQ登录信息
 *
 * @author HanPengyu 
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}
// Mobcent::setErrors();
class QQInfoAction extends MobcentAction {

    public function run($openId, $oauthToken, $platformId=20) {
        $res = $this->initWebApiArray();
        $res = $this->_qqInfo($res, $openId, $oauthToken, $platformId);
        echo WebUtils::outputWebApi($res, '', false);   
    }

    private function _qqInfo($res, $openId, $oauthToken, $platformId) {
        
        global $_G;
        $password = 'mobcent123';
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