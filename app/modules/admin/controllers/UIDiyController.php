<?php

/**
 * UI Diy 控制器
 *
 * @author 谢建平 <jianping_xie@aliyun.com>  
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

// Mobcent::setErrors();
class UIDiyController extends AdminController
{
    public function actionIndex()
    {
        $modules = array();
        $tempModules = AppbymeUIDiyModel::getModules();
        $isFindDiscover = $isFindFastpost = false;
        $discoverModule = AppbymeUIDiyModel::initDiscoverModule();
        $fastpostModule = AppbymeUIDiyModel::initFastpostModule();

        foreach ($tempModules as $module) {
            switch ($module['type']) {
                case AppbymeUIDiyModel::MODULE_TYPE_DISCOVER:
                    if (!$isFindDiscover) {
                        $isFindDiscover = true;
                        $discoverModule = $module;
                    }
                    break;
                case AppbymeUIDiyModel::MODULE_TYPE_FASTPOST:
                    if (!$isFindFastpost) {
                        $isFindFastpost = true;
                        $fastpostModule = $module;
                    }
                    break;
                default:
                    $modules[] = $module;
                    break;
            }
        }
        array_unshift($modules, $discoverModule, $fastpostModule);

        $this->renderPartial('index', array(
            'modules' => $modules,
        ));
    }

    public function actionSaveModules($modules)
    {

    }
}