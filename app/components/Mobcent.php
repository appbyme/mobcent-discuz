<?php

/**
 * 安米插件全局通用类
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

$mobcentDataPath = dirname(__FILE__).'/../../../data/appbyme';
$mobcentRuntimePath = dirname(__FILE__).'/../../../data/appbyme/runtime';

function mobcentMakeWritableDir($dir) {
    if (!is_writable($dir)) {
       !is_dir($dir) && mkdir($dir, 0777, true);
        chmod($dir, 0777);
    }
}
mobcentMakeWritableDir($mobcentDataPath);
mobcentMakeWritableDir($mobcentRuntimePath);
!is_writable($mobcentDataPath) && exit('Mobcent Data Path must be writable');
!is_writable($mobcentRuntimePath) && exit('Mobcent Runtime Path must be writable');

define('IN_APPBYME', true);
define('MOBCENT_HACKER_UID', true);

defined('MOBCENT_ROOT') or define('MOBCENT_ROOT', realpath(dirname(__FILE__).'/../../'));
defined('MOBCENT_APP_ROOT') or define('MOBCENT_APP_ROOT', realpath(dirname(__FILE__).'/../'));
defined('MOBCENT_DATA_PATH') or define('MOBCENT_DATA_PATH', realpath($mobcentDataPath));
defined('MOBCENT_DATA_URL_PATH') or define('MOBCENT_DATA_URL_PATH', 'data/appbyme');
defined('MOBCENT_RUNTIME_PATH') or define('MOBCENT_RUNTIME_PATH', realpath($mobcentRuntimePath));
defined('MOBCENT_CACHE_PATH') or define('MOBCENT_CACHE_PATH', MOBCENT_DATA_PATH.'/cache');
defined('MOBCENT_THUMB_PATH') or define('MOBCENT_THUMB_PATH', MOBCENT_DATA_PATH.'/thumb');
defined('MOBCENT_THUMB_URL_PATH') or define('MOBCENT_THUMB_URL_PATH', MOBCENT_DATA_URL_PATH.'/thumb');
defined('MOBCENT_UPLOAD_PATH') or define('MOBCENT_UPLOAD_PATH', MOBCENT_DATA_PATH.'/upload');
defined('MOBCENT_UPLOAD_URL_PATH') or define('MOBCENT_UPLOAD_URL_PATH', MOBCENT_DATA_URL_PATH.'/upload');

defined('MOBCENT_DZ_PLUGIN_ID') or define('MOBCENT_DZ_PLUGIN_ID', 'appbyme_app');

defined('MOBCENT_ERROR_NONE') or define('MOBCENT_ERROR_NONE', '00000000');
defined('MOBCENT_ERROR_DEFAULT') or define('MOBCENT_ERROR_DEFAULT', '11100001');

// define('DISCUZ_DEBUG', true);

class Mobcent {

    public static function setErrors($open=1, $level=E_ALL) {
        ini_set('display_errors', $open);
        error_reporting($level);
    }

    public static function dumpSql() {
        // 开启 DISCUZ_DEBUG 才有效
        DbUtils::getDzDbUtils(true)->dumpDebug();
    }

    public static function import($phpFileName, $prefix='my_', $return=false) {
        $pathInfo = pathinfo($phpFileName);
        if (!empty($pathInfo)) {
            $tmpPhp = $pathInfo['dirname'].'/'.$prefix.$pathInfo['basename'];
            !file_exists($tmpPhp) && $tmpPhp = $phpFileName;
            $res = require_once($tmpPhp);
            if ($return) { return $res; }
        }
        return false;
    }
}