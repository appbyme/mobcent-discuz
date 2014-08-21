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
    public function run($event)
    {
        $res = $this->initWebApiArray();
        
        $res = $this->_doEvent($res, $event);

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _doEvent($res, $event)
    {
        return $res;
    }
}