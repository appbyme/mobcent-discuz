<?php

/**
 * @author  谢建平 <jianping_xie@aliyun.com>  
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

// Mobcent::setErrors();
class MiscController extends MobcentController {

    public function actions() {
        return array(
        );
    }

    protected function mobcentAccessRules() {
        return array(
            'download' => false,
        );
    }

    public function init() {
        $this->rootUrl = Yii::app()->getBaseUrl(true);
        $this->dzRootUrl = substr($this->rootUrl, 0, -16);

        // 初始化数据库连接
        DbUtils::init($this->initDzDb);

        global $_G;
        $_G['siteurl'] = substr($_G['siteurl'], 0, -16);
        $_G['siteroot'] = substr($_G['siteroot'], 0, -16);
    }

    public function actionDownload() {
        !MobileDetectUtils::isMobile() && $this->redirect(sprintf('%s/plugin.php?id=%s:%s', $this->dzRootUrl, MOBCENT_DZ_PLUGIN_ID, 'download'));

        $this->renderPartial('download_mobile',
            array('options' => AppbymeConfig::getDownloadOptions())
        );
    }
}