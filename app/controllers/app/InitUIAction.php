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

class InitUIAction extends MobcentAction {
    
    public function run($custom=0) {
        $res = $this->initWebApiArray();
        $res['body'] = $this->_getUIconfig($custom);
        $res['head']['errInfo'] = '';
        echo WebUtils::outputWebApi($res, 'utf-8', false);
    }

    private function _getUIconfig($custom) {
        $moduleList = array();
        foreach (AppbymeUIDiyModel::getModules() as $module) {
            if (!$custom && $module['type'] == AppbymeUIDiyModel::MODULE_TYPE_CUSTOM) {
                $module['componentList'] = array();
            }
            $moduleList[] = AppUtils::filterModule($module);
        }

        return array(
            'navigation' => AppbymeUIDiyModel::getNavigationInfo(),
            'moduleList' => $moduleList,
        );
    }
}