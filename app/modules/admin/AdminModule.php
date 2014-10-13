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
    public function init()
    {
        $this->setImport(array(
            'admin.models.*',
            'admin.components.*',
        ));

        header("Content-Type: text/html; charset=utf-8");
    }

    public function beforeControllerAction($controller, $action)
    {
        if (!($controller->id == 'index' && $action->id == 'login') && !UserUtils::isInAppbymeAdminGroup()) {
            $controller->redirect(Yii::app()->createAbsoluteUrl('admin/index/login'));
        }
        return true;
    }
}
