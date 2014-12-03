<?php
/**
 * @author  谢建平 <jianping_xie@aliyun.com>  
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

// Mobcent::setErrors();
class QQLoginAction extends CAction {
    
    public function run() {
        if (MobcentDiscuz::getDiscuzVersion() >= 'X3.1') {
            $this->_run_x31();
        } else {
            $this->_run();
        }
    }

    private function _run() {
        $path = Yii::getPathOfAlias('application.components.discuz.qqconnect');
        require_once($path . '/connect_login_x25.php');
    }

    private function _run_x31() {
        $path = Yii::getPathOfAlias('application.components.discuz.qqconnect');
        require_once($path . '/connect_login_x31.php');
    }
}