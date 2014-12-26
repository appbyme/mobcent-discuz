<?php

/**
 * 插件更新时执行此文件
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 * @license http://opensource.org/licenses/LGPL-3.0
 */

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

$sql = <<<EOF

CREATE TABLE IF NOT EXISTS `cdb_home_surrounding_user` (
    `poi_id` bigint(12) NOT NULL AUTO_INCREMENT,
    `longitude` decimal(10,7) NOT NULL DEFAULT '0',
    `latitude` decimal(10,7) NOT NULL DEFAULT '0',
    `object_id` bigint(12) NOT NULL DEFAULT '0',
    `type` tinyint(2) NOT NULL DEFAULT '0',
    `location` varchar(50) NOT NULL DEFAULT '',
    PRIMARY KEY (`poi_id`),
    UNIQUE KEY `object_id` (`object_id`, `type`),
    KEY `type` (`type`)
) ENGINE=MyISAM;

# 用户登陆表
CREATE TABLE IF NOT EXISTS `cdb_appbyme_user_access` (
    `user_access_id` int(11) NOT NULL AUTO_INCREMENT,
    `user_access_token` varchar(36) NOT NULL DEFAULT '',
    `user_access_secret` varchar(36) NOT NULL DEFAULT '',
    `user_id` int(11) NOT NULL DEFAULT '0',
    `create_time` int(10) unsigned NOT NULL DEFAULT '0',
     PRIMARY KEY (`user_access_id`),
     UNIQUE KEY `user_access_token` (`user_access_token`, `user_access_secret`),
     UNIQUE KEY `user_id` (`user_id`)
) ENGINE=MyISAM;

# 用户设置表
CREATE TABLE IF NOT EXISTS `cdb_appbyme_user_setting` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT, 
    `uid` INT(10) UNSIGNED NOT NULL DEFAULT '0',
    `ukey` CHAR(20) NOT NULL DEFAULT '',
    `uvalue` VARCHAR(255) NOT NULL DEFAULT '',
    PRIMARY KEY (`id`),
    UNIQUE KEY `key` (`uid`, `ukey`)
) ENGINE=MyISAM;

# 设置表
CREATE TABLE IF NOT EXISTS `cdb_appbyme_config` (
  `ckey` varchar(255) NOT NULL DEFAULT '',
  `cvalue` mediumtext NOT NULL,
  PRIMARY KEY (`ckey`)
) ENGINE=MyISAM;

# 门户模块表
CREATE TABLE IF NOT EXISTS `cdb_appbyme_portal_module` (
    `mid` int(12) NOT NULL AUTO_INCREMENT,
    `name` varchar(230) NOT NULL DEFAULT '',
    `type` tinyint(2) NOT NULL DEFAULT '0',
    `displayorder` int(12) NOT NULL DEFAULT '0',
    `param` text NOT NULL,
    PRIMARY KEY (`mid`),
    KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM;

# 门户模块数据表
CREATE TABLE IF NOT EXISTS `cdb_appbyme_portal_module_source` (
    `sid` int(12) NOT NULL AUTO_INCREMENT,
    `mid` int(12) DEFAULT '0',
    `id` int(12) DEFAULT '0',
    `url` varchar(500) DEFAULT '',
    `idtype` varchar(10) DEFAULT '',
    `imgid` int(12) DEFAULT '0',
    `imgurl` varchar(500) DEFAULT '',
    `imgtype` varchar(10) DEFAULT '',
    `title` varchar(200) DEFAULT '',
    `type` tinyint(2) DEFAULT '1',
    `displayorder` int(12) NOT NULL DEFAULT '0',
    `param` text NOT NULL,
    PRIMARY KEY (`sid`),
    KEY `mid` (`mid`, `type`, `idtype`, `imgtype`),
    KEY `displayorder` (`mid`, `type`, `displayorder`)
) ENGINE=MyISAM;

ALTER TABLE `cdb_appbyme_config` CHANGE cvalue cvalue MEDIUMTEXT;
ALTER TABLE `cdb_appbyme_user_setting` CHANGE uvalue uvalue VARCHAR(255);

EOF;

runquery($sql);

$finish = TRUE;
