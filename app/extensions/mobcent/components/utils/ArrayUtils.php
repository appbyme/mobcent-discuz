<?php

/**
 * 数组工具类
 *
 * @author  谢建平 <jianping_xie@aliyun.com>  
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_APPBYME')) {
    exit('Access Denied');
}

class ArrayUtils {

    /**
     * 变量转换为数组
     * 
     * @param string|array $string 
     * @param string $delimiter
     *
     * @return array
     */
    public static function explode($string, $delimiter=',') {
        $res = $string;
        if (!is_array($res)) {
            $res = explode($delimiter, (string)$res);
            !is_array($res) && $res = array();
        }
        return $res;
    }
}