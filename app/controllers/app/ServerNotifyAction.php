<?php

/**
 * 服务器事件通知接口
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class ServerNotifyAction extends MobcentAction
{
    public function run($event, $appKey)
    {
        $res = $this->initWebApiArray();
        
        $res = $this->_doEvent($res, $event, $appKey);

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _doEvent($res, $event, $appKey)
    {
        if($event == 'newApp') {
            $res = $this->_doNewApp($res, $appKey);
        } elseif ($event == 'updateApp') {
            $res = $this->_doUpdateApp($res, $appKey);
        }
        return $res;
    }

    private function _doNewApp($res, $appKey)
    {
        // $url = 'http://192.168.1.211:9797/mobcentACA/app/wAMkQjefj3HPHsrfCk/profile';
        $url = 'http://www.appbyme.com/app/'.$appKey.'/profile';
        $temRes = WebUtils::httpRequest($url, 15);
        $temRes = WebUtils::jsonDecode($temRes);
        $appInfo = array(
            'appName' => WebUtils::t($temRes['appName']),
            'appAuthor' => WebUtils::t($temRes['appAuthor']),
            'appDescribe' => WebUtils::t($temRes['appDescribtion']),
            'appVersion' => WebUtils::t($temRes['appVersion']),
            'appIcon' => $temRes['appIcon'],
            'appImage' => $temRes['appCover'],
            'appContentId' => $temRes['contentId'],
            'appDownloadUrl' => array(
                'android' => $temRes['apkUrl'],
                'apple' => $temRes['ipaUrl'],
                'appleMobile' => $temRes['plistUrl'],
            ),
            'appQRCode' => array(
                'android' => $temRes['qrcode'],
                'apple' => $temRes['qrcode'],
            )
        );

        $appDownloadOptions = array('ckey' => 'app_download_options', 'cvalue' => serialize($appInfo));
        $tempData = DB::fetch_first("SELECT * FROM ".DB::table('appbyme_config')." WHERE ckey='app_download_options'");
        if (empty($tempData)) {
            DB::insert('appbyme_config', $appDownloadOptions);
        } else {
            DB::update('appbyme_config', $appDownloadOptions, array('ckey' => 'app_download_options'));
        }

        $appForumKey = array('ckey' => 'app_forumkey', 'cvalue' => $temRes['forumKey']);
        $tempData = DB::fetch_first("SELECT * FROM ".DB::table('appbyme_config')." WHERE ckey='app_forumkey'");
        if (empty($tempData)) {
            DB::insert('appbyme_config', $appForumKey);
        } else {
            DB::update('appbyme_config', $appForumKey, array('ckey' => 'app_forumkey'));
        }
        return $res; 
    }

    private function _doUpdateApp($res, $appKey)
    {
        return $this->_doNewApp($res, $appKey); 
    }
}