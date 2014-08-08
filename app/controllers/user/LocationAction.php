<?php

/**
 * 定位接口
 *
 * @author 徐少伟 <xushaowei@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class LocationAction extends MobcentAction {

    public function run($longitude, $latitude, $location) {
        $res = $res = $this->initWebApiArray();

        // $longitude='116.3093650';$latitude='40.0611250';$location='北京市海淀区上地东路xxx';
        $location = WebUtils::t(rawurldecode($location));
        $this->_getSaveUserLocation($longitude, $latitude, $location);

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _getSaveUserLocation($longitude, $latitude, $location) {
        global $_G;
        $uid = $_G['uid'];

        SurroundingInfo::saveUserLocation($uid, $longitude, $latitude, $location);
    }
}