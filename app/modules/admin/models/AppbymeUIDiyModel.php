<?php

/**
 * UI Diy model类
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class AppbymeUIDiyModel extends DiscuzAR
{
    const MODULE_KEY = 'app_uidiy_modules';
    const MODULE_TYPE_FULL = 'full';
    const MODULE_TYPE_SUBNAV = 'subnav';
    const MODULE_TYPE_NEWS = 'news';
    const MODULE_TYPE_FASTPOST = 'fastpost';
    const MODULE_TYPE_DISCOVER = 'discover';
    const MODULE_TYPE_CUSTOM = 'custom';

    public static function initModule()
    {
        return array(
            'id' => 0,
            'type' => self::MODULE_TYPE_FULL,
            'title' => '',
            'icon' => Yii::app()->getController()->rootUrl.'/images/admin/module-default.png',
            'leftTopbars' => array(),
            'rightTopbars' => array(),
            'layoutList' => array(),
            'extParams' => array('padding' => '',),
        );
    }

    public static function initDiscoverModule()
    {
        return array_merge(self::initModule(), array(
            'id' => 1,
            'type' => self::MODULE_TYPE_DISCOVER,
            'title' => '发现',
        ));
    }
    
    public static function initFastpostModule()
    {
        return array_merge(self::initModule(), array(
            'id' => 2,
            'title' => '快速发表',
            'type' => self::MODULE_TYPE_FASTPOST,
            // 'icon' => Yii::app()->getController()->rootUrl.'/images/admin/module-fastpost.png',
        ));
    }

    public static function getModules()
    {
        $data = DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT cvalue
            FROM %t
            WHERE ckey = %s
            ',
            array('appbyme_config', self::MODULE_KEY)
        );
        return $data ? (array)unserialize($data) : array();
    }

    public static function saveModules($modules)
    {
        $appUIDiyModules = array('ckey' => self::MODULE_KEY, 'cvalue' => serialize($modules));
        $config = DbUtils::getDzDbUtils(true)->row('
            SELECT * 
            FROM %t 
            WHERE ckey=%s
            ',
            array('appbyme_config', self::MODULE_KEY)
        );
        if (empty($config)) {
            DbUtils::getDzDbUtils(true)->insert('appbyme_config', $appUIDiyModules);
        } else {
            DbUtils::getDzDbUtils(true)->update('appbyme_config', $appUIDiyModules, array('ckey' => self::MODULE_KEY));
        }
    }
}