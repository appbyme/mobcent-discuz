<?php

/** 
 * @author  谢建平 <jianping_xie@aliyun.com>  
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class SettingAction extends MobcentAction {

    public function run($setting) {
        $res = $this->initWebApiArray();

        $uid = $this->getController()->uid;

        // test 
        // $setting ='{"head": {"errCode": 0, "errInfo": ""}, "body": {"settingInfo": {"hidden": 0}, "externInfo": {}}}';
        $settings = rawurldecode($setting);
        $settings = WebUtils::jsonDecode($settings);
        $settings = $settings != null ? $settings['body']['settingInfo'] : array();
        
        // insert or update new settings
        AppbymeUserSetting::saveNewSettings($uid, $settings);

        echo WebUtils::outputWebApi($res, '', false);
    }
}