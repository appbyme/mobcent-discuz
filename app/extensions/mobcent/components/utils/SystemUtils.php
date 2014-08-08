<?php

/**
 * 系统相关工具类
 *
 * @author  谢建平 <jianping_xie@aliyun.com>  
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class SystemUtils {

    /**
     * 后台执行命令
     * 
     * @param string $cmd
     */
    public static function execInBackground($cmd) { 
        if (substr(php_uname(), 0, 7) == 'Windows') { 
            pclose(popen('start /B '. $cmd, 'r'));  
        } else { 
            exec($cmd . ' > /dev/null &');   
        } 
    } 
}