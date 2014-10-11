<?php

/**
 * 后台管理控制器基类
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class AdminController extends Controller
{
    public $rootUrl = '';
    public $dzRootUrl = '';

    public function init()
    {
        parent::init();

        global $_G;
        $_G['siteurl'] = substr($_G['siteurl'], 0, -16);
        $_G['siteroot'] = substr($_G['siteroot'], 0, -16);

        $this->rootUrl = Yii::app()->getBaseUrl(true);
        $this->dzRootUrl = substr($this->rootUrl, 0, -16);

        loadcache('plugin');
        loadcache(MOBCENT_DZ_PLUGIN_ID);
    }
}
