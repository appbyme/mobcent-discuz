<?php

/**
 * 文件操作工具类
 *
 * @author  谢建平 <jianping_xie@aliyun.com>  
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class FileUtils {

    public static function getFileExtension($fileName, $defExt='') {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        return !empty($extension) ? $extension : $defExt;
    }

    public static function saveFile($fileName, $data) {
        return file_put_contents($fileName, $data, LOCK_EX);
    }

    public static function getFile($fileName) {
        return file_get_contents($fileName);
    }

    public static function safeDeleteFile($fileName) {
        file_exists($fileName) && @unlink($fileName);
    }
    
    public static function getRandomUniqueFileName($path, $prefix='', $len=12, $suffix='') {
        $fileName = '';
        while (true) {
            $fileName = sprintf('%s/%s', $path, self::getRandomFileName($prefix, $len, $suffix));
            if (file_exists($fileName))
                break;
        }
        return $fileName;
    }

    public static function getRandomFileName($prefix='', $len=12, $suffix='') {
        $randomArr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        $tempName = '';
        for ($i = 0; $i < $len; $i++) {
            $tempName .= $randomArr[array_rand($randomArr)];
        }
        return $prefix.$tempName.$suffix;
    }
}