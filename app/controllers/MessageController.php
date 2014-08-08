<?php

/**
 * @author  谢建平 <jianping_xie@aliyun.com>  
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class MessageController extends MobcentController {

	public function actions() {
		return array(
			'heart' => 'application.controllers.message.HeartAction',
            'pmlist' => 'application.controllers.message.PMListAction',
            'pmadmin' => 'application.controllers.message.PMAdminAction',
            'pmsessionlist' => 'application.controllers.message.PMSessionListAction',
			'notifylist' => 'application.controllers.message.NotifyListAction',
		);
	}

    protected function mobcentAccessRules() {
        return array(
            'heart' => true,
            'pmlist' => true,
            'pmsessionlist' => true,
            'pmadmin' => true,
            'notifylist' => true,
        );
    }
}