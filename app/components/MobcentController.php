<?php

/**
 * 客户端接口控制器基类
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class MobcentController extends Controller {

    public $uid = 0;
    public $rootUrl = '';
    public $dzRootUrl = '';

    public $mobcentDiscuzApp = null;
    public $initDzDb = false;

    public function init() {
        parent::init();

        $this->rootUrl = Yii::app()->getBaseUrl(true);
        $this->dzRootUrl = substr($this->rootUrl, 0, -16);
        
        // $_GET['accessToken'] = '8d5478c77477933169ab8cfde10b5'; $_GET['accessSecret'] = 'a57002aab240f3ff831d868b623ff';
        // $_GET['accessToken'] = 'a4f26a1de6a3fd60e133075eecc73'; $_GET['accessSecret'] = '35394ccf5119fc6a01d2dc3c2786a';

        // 初始化数据库连接
        DbUtils::init($this->initDzDb);

        $this->mobcentDiscuzApp = new MobcentDiscuzApp;
        $this->mobcentDiscuzApp->init();
    }

    protected function beforeAction($action) {
        parent::beforeAction($action);

        $accessRules = $this->mobcentAccessRules();
        $checkLogin = isset($accessRules[$action->id]) ? $accessRules[$action->id] : true;
        $this->checkUserAccess($checkLogin);

        return true;
    }

    protected function mobcentAccessRules() {
        return array();
    }

    protected function checkUserAccess($checkLogin=true) {
        if (!UserUtils::checkAccess() && $checkLogin) {
            WebUtils::endAppWithErrorInfo(array('rs' => 0, 'errcode' => 50000000), 'to_login');
        }

        global $_G;
        $this->uid = $_G['uid'];
    }
}
