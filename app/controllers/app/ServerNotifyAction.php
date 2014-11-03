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
    public function run($event, $appKey, $test=0)
    {
        $res = $this->initWebApiArray();
        
        $res = $this->_doEvent($res, $event, $appKey, $test);

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _doEvent($res, $event, $appKey, $test)
    {
        if($event == 'newApp') {
            $res = $this->_doNewApp($res, $appKey, $test);
        } elseif ($event == 'updateApp') {
            $res = $this->_doUpdateApp($res, $appKey, $test);
        }
        return $res;
    }

    private function _doNewApp($res, $appKey, $test)
    {
        // $url = 'http://192.168.1.211:9797/mobcentACA/app/wAMkQjefj3HPHsrfCk/profile';
        $url = 'http://www.appbyme.com/mobcentACA/app/'.$appKey.'/profile';
        $temRes = WebUtils::httpRequest($url, 30);
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

        if ($test == 0) {
            AppbymeConfig::saveDownloadOptions($appInfo);
            AppbymeConfig::saveForumkey($temRes['forumKey']);
        }
        return $res; 
    }

    private function _doUpdateApp($res, $appKey, $test)
    {
        return $this->_doNewApp($res, $appKey, $test); 
    }
}