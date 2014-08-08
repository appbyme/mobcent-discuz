<?php

/**
 * diucuz UCenter AR 类
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class DiscuzUCenterAR extends MobcentAR {

	public function getDbConnection() {
		return Yii::app()->dbDzUc;
	}
}