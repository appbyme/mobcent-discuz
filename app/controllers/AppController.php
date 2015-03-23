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
            'moduleconfig' => 'application.controllers.app.ModuleConfigAction',
            'servernotify' => 'application.controllers.app.ServerNotifyAction',
            'serverupload' => 'application.controllers.app.ServerUploadAction',
            'getcode' => 'application.controllers.app.GetCodeAction',
            'checkmobilecode' => 'application.controllers.app.CheckMobileCodeAction',
        );
    }

    protected function mobcentAccessRules() 
    {
        return array(
            'initui' => false,
            'moduleconfig' => false,
            'servernotify' => false,
            'serverupload' => false,
            'getcode' => false,
            'checkmobilecode' => false
        );
    }
}