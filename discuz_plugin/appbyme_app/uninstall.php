<?php

/**
 * 插件卸载时执行此文件
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 * @license http://opensource.org/licenses/LGPL-3.0
 */

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

$sql = <<<EOF

DROP TABLE IF EXISTS `cdb_add_admin`;
DROP TABLE IF EXISTS `cdb_home_weibo`;
DROP TABLE IF EXISTS `cdb_amy_user_setting`;
DROP TABLE IF EXISTS `cdb_add_module`;
DROP TABLE IF EXISTS `cdb_add_portal_module`;
#DROP TABLE IF EXISTS `cdb_ucenter_amy_pm_heart`;
#DROP TABLE IF EXISTS `cdb_home_access`;

# DROP TABLE IF EXISTS `cdb_home_surrounding_user`;

# DROP TABLE IF EXISTS `cdb_appbyme_user_access`;
# DROP TABLE IF EXISTS `cdb_appbyme_user_setting`;
# DROP TABLE IF EXISTS `cdb_appbyme_config`;
# DROP TABLE IF EXISTS `cdb_appbyme_portal_module`;
# DROP TABLE IF EXISTS `cdb_appbyme_portal_module_source`;

EOF;

runquery($sql);

$finish = TRUE;