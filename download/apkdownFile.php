<?php

/**
 * 下载页数据获取接口
 *
 * @author 徐少伟 <xushaowei@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

require_once dirname(__FILE__).'/../../source/class/class_core.php';

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

$tempMethod = $_SERVER['REQUEST_METHOD'];
$_SERVER['REQUEST_METHOD'] = 'POST';
define('DISABLEXSSCHECK', 1);
define('DISABLEDEFENSE', 1);
C::creatapp();
C::app()->init();
$_SERVER['REQUEST_METHOD'] = $tempMethod;

// $_GET['AppAuthor']='%5Cu0068%5Cu0061%5Cu0070%5Cu0070%5Cu0079';
// $_GET['AppDescribe']='%5Cu65b0%5Cu534e%5Cu7f51%5Cu5317%5Cu4eac%5Cu7535%5Cu8bb0%5Cu8005%5Cu738b%5Cu5e06%5Cu5728%5Cu51fa%5Cu5e2d%5Cu91d1%5Cu7816%5Cu56fd%5Cu5bb6%5Cu9886%5Cu5bfc%5Cu4eba%5Cu7b2c%5Cu516d%5Cu6b21%5Cu4f1a%5Cu6664%5Cu53e4%5Cu5df4%5Cu8fdb%5Cu884c%5Cu56fd%5Cu4e8b%5Cu8bbf%5Cu95ee%5Cu5e76%5Cu51fa%5Cu5e2d%5Cu4e2d%5Cu56fd%5Cu62c9%5Cu7f8e%5Cu548c%5Cu52a0%5Cu52d2%5Cu6bd4%5Cu56fd%5Cu5bb6%5Cu9886%5Cu5bfc%5Cu4eba%5Cu4f1a%5Cu6664%5Cu524d%5Cu5915%5Cu56fd%5Cu5bb6%5Cu4e3b%5Cu5e2d%5Cu4e60%5Cu8fd1%5Cu5e73%5Cu63a5%5Cu53d7%5Cu4e86%5Cu5df4';
// $_GET['AppVersion'] = '%5Cu0031%5Cu0030%5Cu0030%5Cu0031%5Cu0035';
// $_GET['AppImg'] = '%5Cu0068%5Cu0074%5Cu0074%5Cu0070%5Cu003a%5Cu002f%5Cu002f%5Cu0031%5Cu0039%5Cu0032%5Cu002e%5Cu0031%5Cu0036%5Cu0038%5Cu002e%5Cu0031%5Cu002e%5Cu0032%5Cu0033%5Cu0032%5Cu002f%5Cu0064%5Cu002f%5Cu0069%5Cu006d%5Cu0067%5Cu002f%5Cu0061%5Cu0063%5Cu0061%5Cu002f%5Cu006c%5Cu006f%5Cu0061%5Cu0064%5Cu0069%5Cu006e%5Cu0067%5Cu0050%5Cu0061%5Cu0067%5Cu0065%5Cu002f%5Cu0032%5Cu0030%5Cu0031%5Cu0034%5Cu002f%5Cu0030%5Cu0036%5Cu002f%5Cu0031%5Cu0031%5Cu002f%5Cu006f%5Cu0072%5Cu0069%5Cu0067%5Cu0069%5Cu006e%5Cu0061%5Cu006c%5Cu002f%5Cu0064%5Cu0038%5Cu0033%5Cu0037%5Cu0062%5Cu0033%5Cu0037%5Cu0062%5Cu002d%5Cu0061%5Cu0034%5Cu0064%5Cu0033%5Cu002d%5Cu0034%5Cu0030%5Cu0037%5Cu0064%5Cu002d%5Cu0061%5Cu0032%5Cu0036%5Cu0061%5Cu002d%5Cu0032%5Cu0062%5Cu0037%5Cu0037%5Cu0038%5Cu0063%5Cu0063%5Cu0030%5Cu0038%5Cu0036%5Cu0065%5Cu0065%5Cu002e%5Cu0070%5Cu006e%5Cu0067';
// $_GET['contentId'] = 10191;
// $_GET['AppIcon'] = '%5Cu0068%5Cu0074%5Cu0074%5Cu0070%5Cu003a%5Cu002f%5Cu002f%5Cu0031%5Cu0039%5Cu0032%5Cu002e%5Cu0031%5Cu0036%5Cu0038%5Cu002e%5Cu0031%5Cu002e%5Cu0032%5Cu0033%5Cu0032%5Cu002f%5Cu0064%5Cu002f%5Cu0069%5Cu006d%5Cu0067%5Cu002f%5Cu0061%5Cu0063%5Cu0061%5Cu002f%5Cu0069%5Cu0063%5Cu006f%5Cu006e%5Cu002f%5Cu0032%5Cu0030%5Cu0031%5Cu0034%5Cu002f%5Cu0030%5Cu0036%5Cu002f%5Cu0031%5Cu0031%5Cu002f%5Cu006f%5Cu0072%5Cu0069%5Cu0067%5Cu0069%5Cu006e%5Cu0061%5Cu006c%5Cu002f%5Cu0066%5Cu0066%5Cu0033%5Cu0037%5Cu0039%5Cu0035%5Cu0036%5Cu0036%5Cu002d%5Cu0062%5Cu0039%5Cu0039%5Cu0034%5Cu002d%5Cu0034%5Cu0062%5Cu0032%5Cu0030%5Cu002d%5Cu0039%5Cu0032%5Cu0033%5Cu0030%5Cu002d%5Cu0038%5Cu0065%5Cu0036%5Cu0038%5Cu0032%5Cu0031%5Cu0035%5Cu0064%5Cu0030%5Cu0031%5Cu0036%5Cu0033%5Cu002e%5Cu0070%5Cu006e%5Cu0067';
// $_GET['AppName']= '%5Cu0078%5Cu0073%5Cu0077';

function transformCoding($string, $charset='') {
    global $_G;
    $charset == '' && $charset = $_G['charset'];
    if (($enc = strtoupper($charset)) !== 'UTF-8') {
        $string = iconv('UTF-8', $enc, $string);
    }
    return $string;
}

foreach ($_GET as $key => $value) {
    $value = rawurldecode($value);
    $_GET[$key] = json_decode('"'.$value.'"');
}

$appIcon = str_replace('original', '57x57', $_GET['AppIcon']);
$appImage = str_replace('original', '320x480', $_GET['AppImg']);
$appInfo = array(
    'appName' => transformCoding($_GET['AppName']),
    'appAuthor' => transformCoding($_GET['AppAuthor']),
    'appDescribe' => transformCoding($_GET['AppDescribe']),
    'appVersion' => $_GET['AppVersion'],
    'appIcon' => $appIcon,
    'appImage' => $appImage,
    'appContentId' => $_GET['contentId'],
    'appDownloadUrl' => array(
        'android' => 'http://dl.mobcent.com/mobcentFile/servlet/DownLoadFileServlet?action=apk&contentId='.$_GET['contentId'].'&appPlat=0',
        'apple' => 'http://dl.mobcent.com/mobcentFile/servlet/DownLoadFileServlet?action=ipa&contentId='.$_GET['contentId'].'&appPlat=0',
        'appleMobile' => 'itms-services://?action=download-manifest&url=https://www.appbyme.com/mobcentACA/app_'.$_GET['contentId'].'.plist',
    ),
    'appQRCode' => array(
        'android' => 'http://img.appbyme.com/d/aca/QRcCodeImg/android/app'.$_GET['contentId'].'/app'.$_GET['contentId'].'.png',
        'apple' => 'http://img.appbyme.com/d/aca/QRcCodeImg/ios/app'.$_GET['contentId'].'/app'.$_GET['contentId'].'.png',
    )
);

$appDownloadOptions = array('ckey' => 'app_download_options', 'cvalue' => serialize($appInfo));
$tempData = DB::fetch_first("SELECT * FROM ".DB::table('appbyme_config')." WHERE ckey='app_download_options'");
if (empty($tempData)) {
    DB::insert('appbyme_config', $appDownloadOptions);
} else {
    DB::update('appbyme_config', $appDownloadOptions, array('ckey' => 'app_download_options'));
}

echo json_encode(array('rs' => 1));
?>