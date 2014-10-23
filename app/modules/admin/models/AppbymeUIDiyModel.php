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
    // navigator
    const NAV_KEY = 'app_uidiy_nav_info';
    const NAV_TYPE_BOTTOM = 'bottom';
    const NAV_ITEM_ICON_1 = 'mc_forum_main_bar_button1';

    // module
    const MODULE_KEY = 'app_uidiy_modules';
    
    const MODULE_ID_DISCOVER = 1;
    const MODULE_ID_FASTPOST = 2;

    const MODULE_TYPE_FULL = 'full';
    const MODULE_TYPE_SUBNAV = 'subnav';
    const MODULE_TYPE_NEWS = 'news';
    const MODULE_TYPE_FASTPOST = 'fastpost';
    const MODULE_TYPE_CUSTOM = 'custom';

    // component
    const COMPONENT_TYPE_DISCOVER = 'discover';
    const COMPONENT_TYPE_FASTTEXT = 'fasttext';
    const COMPONENT_TYPE_FASTIMAGE = 'fastimage';
    const COMPONENT_TYPE_FASTCAMERA = 'fastcamera';
    const COMPONENT_TYPE_FASTAUDIO = 'fastaudio';
    const COMPONENT_TYPE_WEATHER = 'weather';
    const COMPONENT_TYPE_SEARCH = 'search';
    const COMPONENT_TYPE_FORUMLIST = 'forumlist';
    const COMPONENT_TYPE_NEWSLIST = 'newslist';
    const COMPONENT_TYPE_TOPICLIST = 'topiclist';
    const COMPONENT_TYPE_SIGN = 'sign';
    const COMPONENT_TYPE_MESSAGELIST = 'messagelist';
    const COMPONENT_TYPE_SETTING = 'setting';
    const COMPONENT_TYPE_ABOAT = 'aboat';
    const COMPONENT_TYPE_USERINFO = 'userinfo';
    const COMPONENT_TYPE_MODULEREF = 'moduleRef';
    const COMPONENT_TYPE_WEBAPP = 'webapp';
    const COMPONENT_TYPE_LAYOUT = 'layout';
    const COMPONENT_TYPE_SURROUDING_POSTLIST = 'surroudingPostlist';
    const COMPONENT_TYPE_SURROUDING_USERLIST = 'surroudingUserlist';
    const COMPONENT_TYPE_RECOMMEND_USERLIST = 'recommendUserlist';

    const COMPONENT_STYLE_FLAT = 'flat';
    const COMPONENT_STYLE_CARD = 'card';
    const COMPONENT_STYLE_IMAGE = 'image';
    const COMPONENT_STYLE_LAYOUT_DEFAULT = 'layoutDefault';
    const COMPONENT_STYLE_DISCOVER_DEFAULT = 'discoverDefault';
    const COMPONENT_STYLE_DISCOVER_CUSTOM = 'discoverCustom';
    const COMPONENT_STYLE_DISCOVER_SLIDER = 'discoverSlider';

    public static function initNavigation()
    {
        return array(
            'type' => self::NAV_TYPE_BOTTOM,
            'navItemList' => array(),
        );
    }

    public static function initNavItem()
    {
        return array(
            'moduleId' => 0,
            'title' => '',
            'icon' => self::NAV_ITEM_ICON_1,
        );
    }

    public static function getNavigationInfo()
    {
        $data = DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT cvalue
            FROM %t
            WHERE ckey = %s
            ',
            array('appbyme_config', self::NAV_KEY)
        );
        return $data ? (array)unserialize($data) : array();
    }

    public static function saveNavigationInfo($navInfo)
    {
        $appUIDiyNavInfo = array('ckey' => self::NAV_KEY, 'cvalue' => serialize($navInfo));
        $config = DbUtils::getDzDbUtils(true)->queryRow('
            SELECT * 
            FROM %t 
            WHERE ckey=%s
            ',
            array('appbyme_config', self::NAV_KEY)
        );
        if (empty($config)) {
            DbUtils::getDzDbUtils(true)->insert('appbyme_config', $appUIDiyNavInfo);
        } else {
            DbUtils::getDzDbUtils(true)->update('appbyme_config', $appUIDiyNavInfo, array('ckey' => self::NAV_KEY));
        }
        return true;
    }

    public static function initModule()
    {
        return array(
            'id' => 0,
            'type' => self::MODULE_TYPE_FULL,
            'title' => '',
            'icon' => Yii::app()->getController()->rootUrl.'/images/admin/module-default.png',
            'leftTopbars' => array(),
            'rightTopbars' => array(),
            'componentList' => array(),
            'extParams' => array('padding' => '',),
        );
    }

    public static function initDiscoverModule()
    {
        return array_merge(self::initModule(), array(
            'id' => self::MODULE_ID_DISCOVER,
            'title' => '发现',
            'componentList' => array(
                self::initComponentDiscover(),
            ),
        ));
    }
    
    public static function initFastpostModule()
    {
        return array_merge(self::initModule(), array(
            'id' => self::MODULE_ID_FASTPOST,
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
        $config = DbUtils::getDzDbUtils(true)->queryRow('
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
        return true;
    }

    public static function deleteModules()
    {
        return DbUtils::getDzDbUtils(true)->delete('appbyme_config', array(
            'where' => 'ckey = %s',
            'arg' => array(self::MODULE_KEY),
        ));
    }

    public static function initComponent()
    {
        return array(
            'id' => '',
            'type' => self::COMPONENT_TYPE_FORUMLIST,
            'title' => '',
            'desc' => '',
            'icon' => '',
            'style' => self::COMPONENT_STYLE_FLAT,
            'componentList' => array(),
            'extParams' => array('padding' => '',),
        );
    }

    public static function initComponentDiscover()
    {
        return array_merge(self::initComponent(), array(
            'type' => self::COMPONENT_TYPE_DISCOVER,
            'componentList' => array(
                array_merge(self::initLayout(), array(
                    'style' => self::COMPONENT_STYLE_DISCOVER_SLIDER,
                    'componentList' => array(
                    ),
                )),
                array_merge(self::initLayout(), array(
                    'style' => self::COMPONENT_STYLE_DISCOVER_DEFAULT,
                    'componentList' => array(
                        array_merge(self::initComponent(), array(
                            'title' => '个人中心',
                            'type' => self::COMPONENT_TYPE_USERINFO,
                        )),
                        array_merge(self::initComponent(), array(
                            'title' => '设置',
                            'type' => self::COMPONENT_TYPE_SETTING,
                        )),
                        array_merge(self::initComponent(), array(
                            'title' => '关于',
                            'type' => self::COMPONENT_TYPE_ABOAT,
                        )),
                    ),
                )),
                array_merge(self::initLayout(), array(
                    'style' => self::COMPONENT_STYLE_DISCOVER_CUSTOM,
                    'componentList' => array(
                    ),
                )),
            ),
        ));
    }

    public static function initLayout()
    {
        return array_merge(self::initComponent(), array(
            'type' => self::COMPONENT_TYPE_LAYOUT,
            'style' => self::COMPONENT_STYLE_LAYOUT_DEFAULT,
        ));
    }
}
