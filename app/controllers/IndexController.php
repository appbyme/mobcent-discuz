<?php

/**
 * @author  谢建平 <jianping_xie@aliyun.com>  
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class IndexController extends Controller {

	public function actionIndex() {        
		echo 'welcome mobcent';
	}

	public function actionError() {
		if ($error = Yii::app()->errorHandler->error) {
			echo $error['message'];
		}
	}

    public function actionReturnMobileView() {
        echo 'redirect to mobile view';
    }
}