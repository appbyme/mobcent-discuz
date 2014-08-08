<?php
/**
 * @author  谢建平 <jianping_xie@aliyun.com>  
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class SquareController extends MobcentController {

    public function actions() {
        return array(
            'surrounding' => 'application.controllers.square.SurroundingAction',
        	'share' => 'application.controllers.square.ShareAction',
        );
    }

    protected function mobcentAccessRules() {
        return array(
            'surrounding' => $_GET['poi'] != 'topic',
            'share' => false,
        );
    }
}