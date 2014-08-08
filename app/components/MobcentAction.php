<?php

/**
 * 客户端接口动作基类
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class MobcentAction extends CAction {

    /**
     * runWithCache
     * 
     * @param string $key
     * @param array $params
     */
    protected function runWithCache($key, $params=array()) {
        $cache = $this->getCacheInfo();

        $res = array();
        if (!$cache['enable'] || ($res = Yii::app()->cache->get($key)) === false) {
            $res = WebUtils::outputWebApi($this->getResult($params), '', false);
            if ($cache['enable']) {
                Yii::app()->cache->set($key, $res, $cache['expire']);
            }
        }

        echo $res;
    }

    protected function getCacheInfo() {
        return array('enable' => 0, 'expire' => 60,);
    }

    protected function getResult($params=array()) {
        return array();
    }

    protected function initWebApiArray() {
        return WebUtils::initWebApiArray_oldVersion();
    }

    protected function makeErrorInfo($res, $message, $params=array()) {
        return WebUtils::makeErrorInfo_oldVersion($res, $message, $params);
    }
}