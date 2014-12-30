<?php

/**
 * 客户端相关工具类
 *
 * @author 谢建平 <xiejianping@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class AppUtils {

    const LEVEL_FREE = 0;

    public static function getAppId() {
        return AppbymeConfig::getForumkey();
    }

    public static function getAppLevel() {
        $appId = self::getAppId();
        // $appId = 'o8n5mW6eo6AP8A5Hmb';
        // $appId = 'pmjAXPiqj7RKAiPrbL';

        $url = sprintf('http://sdk.mobcent.com/baikesdk/pay/payState.do?gzip=0&forumKey=%s', $appId);
        $data = WebUtils::jsonDecode(WebUtils::httpRequest($url, 30));

        return (int)(isset($data['data']['paystate']['user_defined']) ? $data['data']['paystate']['user_defined'] : self::LEVEL_FREE);
    }
}