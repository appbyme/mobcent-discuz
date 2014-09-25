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

    public function actionDownload()
    {
        $this->redirect($this->dzRootUrl.'/plugin.php?id='.MOBCENT_DZ_PLUGIN_ID.':download');
    }
}
