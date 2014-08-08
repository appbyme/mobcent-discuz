<?php

/**
 * 应用 >> 安米手机客户端 >> 下载页面
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 * @license http://opensource.org/licenses/LGPL-3.0
 */

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

require_once dirname(__FILE__) . '/appbyme.class.php';
Appbyme::init();

global $_G;
$_G['disabledwidthauto'] = 1;
$appInfo = Appbyme::getAppbymeConfig('app_download_options');
!empty($appInfo['cvalue']) && $appInfo = unserialize($appInfo['cvalue']);
$appName = $appInfo['appName'];
$appImage = $appInfo['appImage'];
$appDescribe = $appInfo['appDescribe'];
$androidDownloadUrl = $appInfo['appDownloadUrl']['android'];
$appleDownloadUrl = $appInfo['appDownloadUrl']['apple'];
$androidQRCode = $appInfo['appQRCode']['android'];
$appleQRCode = $appInfo['appQRCode']['apple'];
$assetsBaseUrlPath = $_G['siteurl'].'/source/plugin/'.Appbyme::PLUGIN_ID.'/template';

list($navtitle, $metadescription, $metakeywords) = Appbyme::getSeoSetting('download');
if(!$navtitle) {
    $navtitle = Appbyme::lang('appbyme_seo_title_download');
    $nobbname = false;
} else {
    $nobbname = true;
}
!$metadescription && $metadescription = $navtitle;
!$metakeywords && $metakeywords = $navtitle;

include template('appbyme_app:download');