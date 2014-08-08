<?php

/**
 * Updated user information interface
 *
 * @author HanPengyu 
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}
// Mobcent::setErrors();
class UpdateUserInfoAction extends MobcentAction {

    public function run($type, $gender='', $avatar='', $oldPassword='', $newPassword='') {
        $res = $this->initWebApiArray();
        if ($type == 'info') {
            $res = $this->_updateUser($res, $gender, $avatar);
        } elseif ($type == 'password') {
            $res = $this->_updatePass($res, $oldPassword, $newPassword);
        }
        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _updateUser($res, $gender, $avatar) {
        global $_G;
        include_once libfile('function/profile');
        
        $setarr ['gender'] = intval($gender);
        if ($setarr) {
            C::t ('common_member_profile')->update($_G['uid'], $setarr);
        }
        manyoulog('user', $uid, 'update');
        $operation = 'gender';
        include_once libfile('function/feed');
        feed_add('profile', 'feed_profile_update_' . $operation, array('hash_data' => 'profile'));
        countprofileprogress();
        
        // ob_start();
        // $this->getController()->forward('user/uploadavatar', false);
        // $result = ob_get_clean();
        // $picInfo = WebUtils::jsonDecode($result, true);
        // $avatar = $picInfo['pic_path'];
        // $avatar = !empty($_GET['avatar']) ? $_GET['avatar'] : '';
        if (!empty($avatar)) {
            $_GET = array_merge($_GET, array('avatar' => $avatar));
            ob_start();
            $this->getController()->forward('user/saveavatar', false);
            $result = ob_get_clean();
            $result = WebUtils::jsonDecode($result);
            if (WebUtils::checkError($result)) {
                return $this->makeErrorInfo($res, 'user_info_avatar_error');
            }                       
        }
        return $this->makeErrorInfo($res, lang('message', 'profile_succeed'), array('noError' => 1));
    }

    private function _updatePass($res, $oldpassword, $newpassword) {
        global $_G;
        $oldpassword = $oldpassword ? urldecode($oldpassword) : '';
        $newpassword = $newpassword ? urldecode($newpassword) : '';
        if(!empty($newpassword) && $newpassword != addslashes($newpassword)) {
            // 抱歉，密码空或包含非法字符:新密码
            return $this->makeErrorInfo($res, lang('message', 'profile_passwd_illegal'));
        }
        loaducenter();
        $ucresult = uc_user_edit(addslashes($_G['username']), $oldpassword, $newpassword );
        if($ucresult == -1) {
            // 原密码不正确，您不能修改密码或 Email 或安全提问
            return $this->makeErrorInfo($res, lang('message', 'profile_passwd_wrong'));
        } 
        $setarr['password'] = md5(random(10));
        C::t('common_member')->update($_G['uid'], $setarr);
            
        $secretStr = AppbymeUserAccess::getSecretStr($_G['uid'], $newpassword);
        $newAccessSecret = $secretStr['accessSecret'];
        $data = array('user_access_secret' => $newAccessSecret);
        $result = AppbymeUserAccess::updateUserAccess($data, $_G['uid']);
        // if (!$result) {
        //     return $this->makeErrorInfo($res, 'user_info_edit_error');
        // }
        $res['token'] = $secretStr['accessToken'];
        $res['secret'] = $newAccessSecret;
        return $res;
    }
}