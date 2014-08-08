<?php

/** 
 * 获取用户设置 接口
 *
 * @author 谢建平 <jianping_xie@aliyun.com>  
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class GetSettingAction extends MobcentAction {

    public function run($getSetting, $longitude=0, $latitude=0, $location='') {
        $res = $this->initWebApiArray();

        // $longitude='116.3093650';$latitude='40.0611250';$location='北京市海淀区上地东路';
        $location = WebUtils::t(rawurldecode($location));
        global $_G;
        ($uid = $_G['uid']) && $this->_saveUserLocation($uid, $longitude, $latitude, $location);

        // $getSetting ="{'body': {'postInfo': {'forumIds': '0'}}}";
        $settings = rawurldecode($getSetting);        
        $settings = WebUtils::jsonDecode($settings);
        $postInfo = isset($settings['body']['postInfo']) ? $settings['body']['postInfo'] : array();

        if (!empty($postInfo)) {
            $res['body']['postInfo'] = $this->_getPostInfo($postInfo);
        }
        $res['body']['serverTime'] = time() . '000';
        $res['body']['misc'] = $this->_getMiscSetting();
        $res['body']['plugin'] = $this->_getPluginSetting();
        $res['body']['forum'] = $this->_getForumSetting();
        $res['body']['portal'] = $this->_getPortalSetting();
        $res['body']['user'] = $this->_getUserSetting();
        $res['body']['message'] = $this->_getMessageSetting();

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _saveUserLocation($uid, $longitude, $latitude, $location) {
        // 插入用户定位开关设置
        $count = (int)DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT COUNT(*) 
            FROM %t
            WHERE uid=%d
            AND ukey=%s
        ',
            array('appbyme_user_setting', $uid, AppbymeUserSetting::KEY_GPS_LOCATION)
        );
        if (!$count) {
            AppbymeUserSetting::saveNewSettings($uid, array(
                AppbymeUserSetting::KEY_GPS_LOCATION => AppbymeUserSetting::VALUE_GPS_LOCATION_ON,
            ));
        }

        !empty($location) && SurroundingInfo::saveUserLocation($uid, $longitude, $latitude, $location);
    }

    private function _getPostInfo($postInfo) {
        return UserUtils::getPermission($postInfo['forumIds']);
    }

    private function _getMiscSetting() {
        $misc = array(
            'weather' => $this->_getWeatherConfig(),
        );
        return $misc;
    }

    private function _getWeatherConfig() {
        $weather = array('allowUsage' => 1, 'allowCityQuery' => 1);
        $forumKey = isset($_GET['forumKey']) ? $_GET['forumKey'] : '';
        $platType = isset($_GET['platType']) ? $_GET['platType'] : APP_TYPE_ANDROID;
        $url = 'http://sdk.mobcent.com/baikesdk/phpapi/settings';
        // $url = 'http://192.168.1.213/forum/phpapi/settings';
        $url .= sprintf('?forumKey=%s&platType=%s&gzip=false', $forumKey, $platType);
        $res = WebUtils::httpRequest($url, 10);
        $res = WebUtils::jsonDecode($res);
        isset($res['data']['weather']['show_weather']) && $weather['allowUsage'] = (int)$res['data']['weather']['show_weather'];
        isset($res['data']['weather']['city_query_setting']) && $weather['allowCityQuery'] = (int)$res['data']['weather']['city_query_setting'];
        return $weather;
    }

    // 获取插件设置
    private function _getPluginSetting() {
        $plugin = array(
            'qqconnect' => $this->_isQQConnect(),
            'dsu_paulsign' => $this->_isDsuPaulsign(),
        );
        return $plugin;
    }

    // 获取版块设置
    private function _getForumSetting() {
        $plugin = array(
            'isSummaryShow' => $this->_isForumSummaryShow(),
            'isTodayPostCount' => $this->_isForumShowTodayPost(),
            'postlistOrderby' => (int)WebUtils::getDzPluginAppbymeAppConfig('forum_postlist_orderby'),
            'postAudioLimit' => $this->_getAudioLimit('forum_audio_limit'),
            // 'defaultNewImageTopicFid' => (int)WebUtils::getDzPluginAppbymeAppConfig('forum_new_image_topic'),
        );
        return $plugin;
    }

    // 获取门户设置
    private function _getPortalSetting() {
        $portal = array(
            'isSummaryShow' => $this->_isPortalSummaryShow(),
        );
        return $portal; 
    }

    /**
     * 获取消息设置
     */
    private function _getMessageSetting() {
        return array(
            'pmAudioLimit' => $this->_getAudioLimit('message_pm_audio_limit'),
            'allowPostImage' => 1,
        );
    }

    // 是否开启qq登陆
    private function _isQQConnect() {
        return DzCommonPlugin::isQQConnectionAvailable() && $this->_isMobileAllowQQlogin() ? 1 : 0;
    }

    // 是否开启签到
    private function _isDsuPaulsign() {
        return DzCommonPlugin::isDsuPaulsignAvailable() && $this->_isMobileAllowPaulsign() ? 1 : 0;
    }

    private function _isMobileAllowPaulsign() {
        $config = WebUtils::getDzPluginAppbymeAppConfig('mobile_allow_sign');
        return !($config !== false && $config == 0);
    }

    // 是否显示论坛摘要
    private function _isForumSummaryShow() {
        $forumSummaryLenth = WebUtils::getDzPluginAppbymeAppConfig('forum_summary_length');
        return $forumSummaryLenth !== false && $forumSummaryLenth == 0 ? 0 : 1;
    }

    // 是否显示门户摘要
    private function _isPortalSummaryShow() {
        $portalSummaryLenth = WebUtils::getDzPluginAppbymeAppConfig('portal_summary_length');
        return $portalSummaryLenth !== false && $portalSummaryLenth == 0 ? 0 : 1;
    }

    // 是否开启获取当天发帖总数
    private function _isForumShowTodayPost() {
        $config = WebUtils::getDzPluginAppbymeAppConfig('forum_show_today_post');
        return $config !== false && $config == 0 ? 0 : 1;
    }

    // 是否允许qq登陆
    private function _isMobileAllowQQlogin() {
        $config = WebUtils::getDzPluginAppbymeAppConfig('mobile_allow_qqlogin');
        return !($config !== false && $config == 0);
    }

    private function _getUserSetting() {
        global $_G;
        $res = array(
            'allowAt' => (int)$_G['group']['allowat'],
            'allowRegister' => (int)WebUtils::getDzPluginAppbymeAppConfig('mobile_allow_register'),
            'wapRegisterUrl' => (string)WebUtils::getDzPluginAppbymeAppConfig('mobile_register_url'),
        );
        return $res;
    }

    private function _getAudioLimit($key) {
        $limit = (int)WebUtils::getDzPluginAppbymeAppConfig($key);
        $limit < 0 && $limit = -1;
        $limit > 600 && $limit = 600;
        return $limit;
    }
}