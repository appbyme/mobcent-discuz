<?php

/**
 * 获取模块配置接口
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class ModuleConfigAction extends MobcentAction
{
    public function run($moduleId)
    {
        $res = $this->initWebApiArray();
        $res['body'] = $this->_getModuleconfig($moduleId);
        $res['head']['errInfo'] = '';
        echo WebUtils::outputWebApi($res, 'utf-8', false);
    }

    private function _getModuleconfig($moduleId)
    {
        $module = array('padding' => '');
        foreach (AppbymeUIDiyModel::getModules() as $tmpModule) {
            if ($tmpModule['id'] == $moduleId) {
                $module = $tmpModule;
                break;
            }
        }
        return array(
            'module' => $module,
        );
    }
}
