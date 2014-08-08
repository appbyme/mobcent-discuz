<?php

/**
 * 插件安装时执行此文件
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
DROP TABLE IF EXISTS `cdb_appbyme_user_access`;
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
# DROP TABLE IF EXISTS `cdb_appbyme_user_setting`;
CREATE TABLE IF NOT EXISTS `cdb_appbyme_user_setting` (
    `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id', 
    `uid` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户id',
    `ukey` CHAR(20) NOT NULL DEFAULT '' COMMENT '用户设置键名',
    `uvalue` VARCHAR(50) NOT NULL DEFAULT '' COMMENT '用户设置值',
    PRIMARY KEY (`id`),
    UNIQUE KEY `key` (`uid`, `ukey`)
) ENGINE=MyISAM;

# 设置表
# DROP TABLE IF EXISTS `cdb_appbyme_config`;
CREATE TABLE IF NOT EXISTS `cdb_appbyme_config` (
  `ckey` varchar(255) NOT NULL DEFAULT '' COMMENT '设置键名',
  `cvalue` text NOT NULL COMMENT '设置值',
  PRIMARY KEY (`ckey`)
) ENGINE=MyISAM;

# 门户模块表
# DROP TABLE IF EXISTS `cdb_appbyme_portal_module`;
CREATE TABLE IF NOT EXISTS `cdb_appbyme_portal_module` (
    `mid` int(12) NOT NULL AUTO_INCREMENT COMMENT '模块id',
    `name` varchar(230) NOT NULL DEFAULT '' COMMENT '模块名称',
    `type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '模块类型',
    `displayorder` int(12) NOT NULL DEFAULT '0' COMMENT '排序',
    `param` text NOT NULL COMMENT '模块参数配置序列化存储',
    PRIMARY KEY (`mid`),
    KEY `displayorder` (`displayorder`)
) ENGINE=MyISAM;

# 门户模块数据表
# DROP TABLE IF EXISTS `cdb_appbyme_portal_module_source`;
CREATE TABLE IF NOT EXISTS `cdb_appbyme_portal_module_source` (
    `sid` int(12) NOT NULL AUTO_INCREMENT,
    `mid` int(12) DEFAULT '0' COMMENT '模块id',
    `id` int(12) DEFAULT '0' COMMENT '来源id',
    `url` varchar(500) DEFAULT '' COMMENT '来源url',
    `idtype` varchar(10) DEFAULT '' COMMENT '来源id类型 (fid 版块id, catid 文章栏目id, tid 主题id ,aid 文章id, url 为外链地址)',
    `imgid` int(12) DEFAULT '0' COMMENT '来源图片id',
    `imgurl` varchar(500) DEFAULT '' COMMENT '图片来源url',
    `imgtype` varchar(10) DEFAULT '' COMMENT '图片来源类型 (tid 主题id ,aid 文章id, url 为外链地址)',
    `title` varchar(200) DEFAULT '' COMMENT '来源的标题',
    `type` tinyint(2) DEFAULT '1' COMMENT '来源类型 (1为普通来源, 2为幻灯片来源)',
    `displayorder` int(12) NOT NULL DEFAULT '0' COMMENT '排序',
    `param` text NOT NULL COMMENT '参数配置序列化存储',
    PRIMARY KEY (`sid`),
    KEY `mid` (`mid`, `type`, `idtype`, `imgtype`),
    KEY `displayorder` (`mid`, `type`, `displayorder`)
) ENGINE=MyISAM;

EOF;

runquery($sql);

$finish = TRUE;