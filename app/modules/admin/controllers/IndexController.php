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

            $username = isset($_POST['username']) ? $_POST['username'] : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            $result = UserUtils::login($username, $password);

            $errorMsg = '';
            if ($result['errcode']) {
                $errorMsg = $result['message'];
            } else {
                if (UserUtils::isInAppbymeAdminGroup()) {
                    $this->redirect(Yii::app()->createAbsoluteUrl('admin/index'));
                } else {
                    $errorMsg = '用户不是管理员，也不在允许登录的范围内！';
                }
            }
        }

        $this->renderPartial('login', array('errorMsg' => $errorMsg, 'username' => $username));
    }
}