<?php

/**
 * 检测设备工具类
 *
 * @author  谢建平 <jianping_xie@aliyun.com>  
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class MobileDetectUtils {

    private static $_mobileDetect = null;

    private static function _createDetect() {
        !self::$_mobileDetect && self::$_mobileDetect = new Mobile_Detect;
        return self::$_mobileDetect;
    }

    public static function isMobile() {
        $detect = self::_createDetect();
        return $detect->isMobile();
    }

    public static function isMicroMessenger() {
        $detect = self::_createDetect();
        return $detect->isMobile() && $detect->version('MicroMessenger');
    }
}