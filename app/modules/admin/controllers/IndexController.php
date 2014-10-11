<?php

/**
 * 后台管理默认控制器
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class IndexController extends AdminController
{
    public function actionIndex()
    {
        $this->redirect(Yii::app()->createAbsoluteUrl('admin/uidiy'));
    }

    public function actionLogin()
    {
        if (UserUtils::isInAppbymeAdminGroup()) {
            $this->redirect(Yii::app()->createAbsoluteUrl('admin/index'));
        }

        if (!empty($_POST)) {
            if ($_POST['username'] == 'admin' && $_POST['password'] == 'admin') {
                $this->redirect(Yii::app()->createAbsoluteUrl('admin/index'));
            }
        }

        $this->renderPartial('login');
    }
}