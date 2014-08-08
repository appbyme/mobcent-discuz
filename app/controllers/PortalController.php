<?php

/**
 * @author  谢建平 <jianping_xie@aliyun.com>  
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class PortalController extends MobcentController {

    public function actions() {
        return array(
        	'commentlist' => 'application.controllers.portal.CommentListAction',
            'commentadmin' => 'application.controllers.portal.CommentAdminAction',
            'modulelist' => 'application.controllers.portal.ModuleListAction',
            'newslist' => 'application.controllers.portal.NewsListAction',
            'newsview' => 'application.controllers.portal.NewsViewAction',
        );
    }

    protected function mobcentAccessRules() {
        return array(
            'commentlist' => false,
            'commentadmin' => true,
            'modulelist' => false,
            'newslist' => false,
            'newsview' => false,
        );
    }
}