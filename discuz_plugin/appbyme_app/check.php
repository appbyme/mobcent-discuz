<?php

/**
 * 插件安装、卸载、升级操作前执行此文件
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 * @license http://opensource.org/licenses/LGPL-3.0
 */

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

$operation = preg_replace('/[^\[A-Za-z0-9_\]]/', '', getgpc('operation'));

if ($operation == 'import' || $operation == 'upgrade') {
	// !is_writable(DISCUZ_ROOT.'/mobcent') && cpmsg('请先去官网下载通过验证后', '', 'error');
}