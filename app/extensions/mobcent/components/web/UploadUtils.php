<?php

/**
 * 上传工具类
 *
 * @author  谢建平 <jianping_xie@aliyun.com>  
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class UploadUtils {

    public static function getTempAvatarPath() {
        return self::_makeBasePath(MOBCENT_RUNTIME_PATH.'/avatar');
    }

    public static function getUploadImageBasePath($suffixPath='') {
        $tempPath = $suffixPath != '' ? '/'.$suffixPath : '';
        $basePath = sprintf('%s%s', MOBCENT_UPLOAD_PATH.'/image', $tempPath);
        return self::_makeBasePath($basePath);
    }

    public static function getUploadImageBaseUrlPath() {
        return MOBCENT_UPLOAD_URL_PATH . '/image';
    }

    public static function getUploadAudioBaseUrlPath() {
        return MOBCENT_UPLOAD_URL_PATH . '/audio';
    }

    public static function getUploadAudioBasePath($suffixPath='') {
        $tempPath = $suffixPath != '' ? '/'.$suffixPath : '';
        $basePath = sprintf('%s%s', MOBCENT_UPLOAD_PATH.'/audio', $tempPath);
        return self::_makeBasePath($basePath);
    }
    
    private static function _makeBasePath($path) {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        return is_writable($path) ? $path : '';
    }
}