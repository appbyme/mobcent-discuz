<?php
/**
 * @author  谢建平 <jianping_xie@aliyun.com>  
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class UserController extends MobcentController {
    
    public function actions() {
        return array(
            'setting' => 'application.controllers.user.SettingAction',
            'qqlogin' => 'application.controllers.user.QQLoginAction',
            'getsetting' => 'application.controllers.user.GetSettingAction',
            'uploadavatar' => 'application.controllers.user.UploadAvatarAction',
            'saveavatar' => 'application.controllers.user.SaveAvatarAction',
            'userinfo' => 'application.controllers.user.UserInfoAction',
            'topiclist' => 'application.controllers.user.TopicListAction',
            'sign' => 'application.controllers.user.SignAction',
            'userlist' => 'application.controllers.user.UserListAction',
            'albumlist' => 'application.controllers.user.AlbumListAction',
            'photolist' => 'application.controllers.user.PhotoListAction',
            'useradmin' => 'application.controllers.user.UserAdminAction',
            'userfavorite' => 'application.controllers.user.UserFavoriteAction',
            'login' => 'application.controllers.user.LoginAction',
            'register' => 'application.controllers.user.RegisterAction',
            'updateuserinfo' => 'application.controllers.user.UpdateUserInfoAction',
            'report' => 'application.controllers.user.ReportAction',
            'switch' => 'application.controllers.user.SwitchAction',
            'location' => 'application.controllers.user.LocationAction',
            'qqinfo' => 'application.controllers.user.QQInfoAction',
            'saveqqinfo' => 'application.controllers.user.SaveQQInfoAction',            
        );
    }

    protected function mobcentAccessRules() {
        return array(
            'setting' => true,
            'qqlogin' => false,
            'getsetting' => false,
            'uploadavatar' => true,
            'saveavatar' => true,
            'userinfo' => true,
            'topiclist' => true,
            'sign' => true,
            'userlist' => true,
            'albumlist' => true,
            'photolist' => true,
            'useradmin' => true,
            'userfavorite' => true,
            'login' => false,
            'register' => false,
            'updateuserinfo' => true,
            'report' => true,
            'switch' => true,
            'location' => true,
            'qqinfo' => false,
            'saveqqinfo' => false            
        );
    }
}