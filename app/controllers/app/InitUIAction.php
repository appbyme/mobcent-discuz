<?php

/**
 * 初始化 App UI接口
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class InitUIAction extends MobcentAction
{
    public function run()
    {
        $res = $this->initWebApiArray();
        $res['body'] = $this->_getUIconfig();
        $res['head']['errInfo'] = '';
        echo WebUtils::outputWebApi($res, 'utf8', false);
    }

    private function _getUIconfig()
    {
        $navInfo = AppbymeUIDiyModel::getNavigationInfo();
        $moduleList = AppbymeUIDiyModel::getModules();
        return array(
            'navigation' => $navInfo,
            'moduleList' => $moduleList,
        );
    }
}
