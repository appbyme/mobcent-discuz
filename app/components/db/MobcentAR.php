<?php

/**
 * AR 基类
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class MobcentAR extends CActiveRecord {

	protected function testDbConnection($db) {
		if ($db instanceof CDbConnection) {
			return true;
		} else {
			throw new CDbException(Yii::t('yii','Active Record requires a "db" CDbConnection application component.'));
		}
	}
}