<?php

/**
 * 后台管理模块类
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class AdminModule extends CWebModule
{
    public $controllerMap = array(
        'uidiy' => 'application.modules.admin.controllers.UIDiyController',
        'wshdiy' => 'application.modules.admin.controllers.WshDiyController',
    );

    public function init()
    {
        $this->setImport(array(
            // 'admin.models.*',
            'admin.components.*',
        ));

        header("Content-Type: text/html; charset=utf-8");
        header("Cache-Control: no-cache, must-revalidate");
        header('Pragma: no-cache');
    }

    public function beforeControllerAction($controller, $action)
    {
        if (!($controller->id == 'index' && $action->id == 'login') && !UserUtils::isInAppbymeAdminGroup()) {
            $controller->redirect(Yii::app()->createAbsoluteUrl('admin/index/login'));
        }
        return true;
    }
}
