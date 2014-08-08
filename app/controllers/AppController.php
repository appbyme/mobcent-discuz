<?php

/**
 * @author  谢建平 <jianping_xie@aliyun.com>  
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class AppController extends MobcentController
{    
    public function actions()
    {
        return array(
            'initui' => 'application.controllers.app.InitUIAction',
        );
    }

    protected function mobcentAccessRules() 
    {
        return array(
            'initui' => false,
        );
    }
}