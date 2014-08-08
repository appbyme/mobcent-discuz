<?php

/**
 * 应用 >> 安米手机客户端 >> 转换接口包管理
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 * @license http://opensource.org/licenses/LGPL-3.0
 */

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

require_once dirname(__FILE__) . '/appbyme.class.php';
Appbyme::init();

$mobcentInfo = Appbyme::getVersion();

showtagheader('div', 'forum_stype', true);
showtableheader(Appbyme::lang('mobcent_infomation_appbyme'));
showtablerow('', array(), array(
    sprintf('%s: %s', Appbyme::lang('mobcent_version_user'), $mobcentInfo['user_version']),
));
showtablerow('', array(), array(
    sprintf('%s: %s', Appbyme::lang('mobcent_version_appbyme'), $mobcentInfo['mobcent_version']),
));
showtablefooter();
showtagfooter('div');