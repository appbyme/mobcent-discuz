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
    const CONFIG_VERSION = '1.0';
    
    // navigator
    const NAV_KEY = 'app_uidiy_nav_info';
    const NAV_KEY_TEMP = 'app_uidiy_nav_info_temp';

    const NAV_TYPE_BOTTOM = 'bottom';
    const NAV_ITEM_ICON = 'mc_forum_main_bar_button';

    // module
    const MODULE_KEY = 'app_uidiy_modules';
    const MODULE_KEY_TEMP = 'app_uidiy_modules_temp';
    
    const MODULE_ID_DISCOVER = 1;
    const MODULE_ID_FASTPOST = 2;

    const MODULE_TYPE_FULL = 'full';
    const MODULE_TYPE_SUBNAV = 'subnav';
    const MODULE_TYPE_NEWS = 'news';
    const MODULE_TYPE_FASTPOST = 'fastpost';
    const MODULE_TYPE_CUSTOM = 'custom';

    const MODULE_STYLE_CARD = 'card';
    const MODULE_STYLE_FLAT = 'flat';

    // component
    const COMPONENT_TYPE_EMPTY = 'empty';
    const COMPONENT_TYPE_DEFAULT = 'forumlist';
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
    const COMPONENT_TYPE_TOPICLIST_SIMPLE = 'topiclistSimple';
    const COMPONENT_TYPE_POSTLIST = 'postlist';
    const COMPONENT_TYPE_NEWSVIEW = 'newsview';
    const COMPONENT_TYPE_SIGN = 'sign';
    const COMPONENT_TYPE_MESSAGELIST = 'messagelist';
    const COMPONENT_TYPE_SETTING = 'setting';
    const COMPONENT_TYPE_ABOUT = 'about';
    const COMPONENT_TYPE_USERINFO = 'userinfo';
    const COMPONENT_TYPE_USERLIST = 'userlist';
    const COMPONENT_TYPE_MODULEREF = 'moduleRef';
    const COMPONENT_TYPE_WEBAPP = 'webapp';
    const COMPONENT_TYPE_LAYOUT = 'layout';
    const COMPONENT_TYPE_SURROUDING_POSTLIST = 'surroudingPostlist';

    const COMPONENT_ICON_STYLE_TEXT = 'text';
    const COMPONENT_ICON_STYLE_IMAGE = 'image';
    const COMPONENT_ICON_STYLE_TEXT_IMAGE = 'textImage';
    const COMPONENT_ICON_STYLE_TEXT_OVERLAP_UP = 'textOverlapUp';
    const COMPONENT_ICON_STYLE_TEXT_OVERLAP_DOWN = 'textOverlapDown';
    const COMPONENT_ICON_STYLE_CIRCLE = 'circle';
    const COMPONENT_ICON_STYLE_NEWS = 'news';
    const COMPONENT_ICON_STYLE_TEXT_OVERLAP_UP_VIDEO = 'textOverlapUp_Video';
    const COMPONENT_ICON_STYLE_TEXT_OVERLAP_DOWN_VIDEO = 'textOverlapDown_Video';

    const COMPONENT_STYLE_FLAT = 'flat';
    const COMPONENT_STYLE_CARD = 'card';
    const COMPONENT_STYLE_IMAGE = 'image';
    const COMPONENT_STYLE_IMAGE_BIG = 'imageBig';
    const COMPONENT_STYLE_IMAGE_SUDOKU = 'imageSudoku';
    const COMPONENT_STYLE_1 = 'style1';
    const COMPONENT_STYLE_2 = 'style2';

    const COMPONENT_STYLE_LAYOUT_DEFAULT = 'layoutDefault';
    const COMPONENT_STYLE_LAYOUT_IMAGE = 'layoutImage';
    // const COMPONENT_STYLE_LAYOUT_SUDOKU = 'layoutSudoku';
    const COMPONENT_STYLE_LAYOUT_SLIDER = 'layoutSlider';
    const COMPONENT_STYLE_LAYOUT_LINE = 'layoutLine';
    const COMPONENT_STYLE_LAYOUT_NEWS_AUTO = 'layoutNewsAuto';
    const COMPONENT_STYLE_LAYOUT_NEWS_MANUAL = 'layoutNewsManual';

    const COMPONENT_STYLE_LAYOUT_ONE_COL = 'layoutOneCol';
    const COMPONENT_STYLE_LAYOUT_ONE_COL_HIGH = 'layoutOneCol_High';
    const COMPONENT_STYLE_LAYOUT_ONE_COL_MID = 'layoutOneCol_Mid';
    const COMPONENT_STYLE_LAYOUT_ONE_COL_LOW = 'layoutOneCol_Low';
    const COMPONENT_STYLE_LAYOUT_TWO_COL = 'layoutTwoCol';
    const COMPONENT_STYLE_LAYOUT_TWO_COL_TEXT = 'layoutTwoColText';
    const COMPONENT_STYLE_LAYOUT_TWO_COL_HIGH = 'layoutTwoCol_High';
    const COMPONENT_STYLE_LAYOUT_TWO_COL_MID = 'layoutTwoCol_Mid';
    const COMPONENT_STYLE_LAYOUT_TWO_COL_LOW = 'layoutTwoCol_Low';
    const COMPONENT_STYLE_LAYOUT_THREE_COL = 'layoutThreeCol';
    const COMPONENT_STYLE_LAYOUT_THREE_COL_TEXT = 'layoutThreeColText';
    const COMPONENT_STYLE_LAYOUT_THREE_COL_HIGH = 'layoutThreeCol_High';
    const COMPONENT_STYLE_LAYOUT_THREE_COL_MID = 'layoutThreeCol_Mid';
    const COMPONENT_STYLE_LAYOUT_THREE_COL_LOW = 'layoutThreeCol_Low';
    const COMPONENT_STYLE_LAYOUT_FOUR_COL = 'layoutFourCol';
    const COMPONENT_STYLE_LAYOUT_FOUR_COL_HIGH = 'layoutFourCol_High';
    const COMPONENT_STYLE_LAYOUT_FOUR_COL_MID = 'layoutFourCol_Mid';
    const COMPONENT_STYLE_LAYOUT_FOUR_COL_LOW = 'layoutFourCol_Low';
    const COMPONENT_STYLE_LAYOUT_ONE_COL_ONE_ROW = 'layoutOneColOneRow';
    const COMPONENT_STYLE_LAYOUT_ONE_COL_TWO_ROW = 'layoutOneColTwoRow';
    const COMPONENT_STYLE_LAYOUT_ONE_COL_THREE_ROW = 'layoutOneColThreeRow';
    const COMPONENT_STYLE_LAYOUT_ONE_ROW_ONE_COL = 'layoutOneRowOneCol';
    const COMPONENT_STYLE_LAYOUT_TWO_ROW_ONE_COL = 'layoutTwoRowOneCol';
    const COMPONENT_STYLE_LAYOUT_THREE_ROW_ONE_COL = 'layoutThreeRowOneCol';

    const COMPONENT_STYLE_DISCOVER_DEFAULT = 'discoverDefault';
    const COMPONENT_STYLE_DISCOVER_CUSTOM = 'discoverCustom';
    const COMPONENT_STYLE_DISCOVER_SLIDER = 'discoverSlider';

    const COMPONENT_TITLE_POSITION_LEFT = 'left';
    const COMPONENT_TITLE_POSITION_CENTER = 'center';
    const COMPONENT_TITLE_POSITION_RIGHT = 'right';

    const COMPONENT_ICON_FASTPOST = 'mc_forum_ico';
    const COMPONENT_ICON_DISCOVER_DEFAULT = 'mc_forum_squre_icon';
    const COMPONENT_ICON_TOPBAR = 'mc_forum_top_bar_button';

    const USERLIST_FILTER_ALL = 'all';
    const USERLIST_FILTER_FRIEND = 'friend';
    const USERLIST_FILTER_FOLLOW = 'follow';
    const USERLIST_FILTER_FOLLOWED = 'followed';
    const USERLIST_FILTER_RECOMMEND = 'recommend';
    const USERLIST_ORDERBY_DATELINE = 'dateline';
    const USERLIST_ORDERBY_REGISTER = 'register';
    const USERLIST_ORDERBY_LOGIN = 'login';
    const USERLIST_ORDERBY_FOLLOWED = 'followed';
    const USERLIST_ORDERBY_DISTANCE = 'distance';

    public static function initNavigation()
    {
        return array(
            'type' => self::NAV_TYPE_BOTTOM,
            'navItemList' => array(
                self::initNavItem(array(
                    'moduleId' => 3,
                    'title' => '首页',
                    'icon' => self::NAV_ITEM_ICON . '1',
                )),
                self::initNavItem(array(
                    'moduleId' => 4,
                    'title' => '社区',
                    'icon' => self::NAV_ITEM_ICON . '2',
                )),
                self::initNavItemFastpost(),
                self::initNavItem(array(
                    'moduleId' => 5,
                    'title' => '消息',
                    'icon' => self::NAV_ITEM_ICON . '4',
                )),
                self::initNavItemDiscover(),
            ),
        );
    }

    public static function initNavItem($params=array())
    {
        return array_merge(array(
            'moduleId' => 0,
            'title' => '',
            'icon' => self::NAV_ITEM_ICON . '1',
        ), $params);
    }

    public static function initNavItemDiscover()
    {
        return self::initNavItem(array(
            'moduleId' => self::MODULE_ID_DISCOVER,
            'title' => '发现',
            'icon' => self::NAV_ITEM_ICON . '5',
        ));
    }

    public static function initNavItemFastpost()
    {
        return self::initNavItem(array(
            'moduleId' => self::MODULE_ID_FASTPOST,
            'title' => '快速发表',
            'icon' => self::NAV_ITEM_ICON . '17',
        ));
    }

    public static function getNavigationInfo($isTemp=false)
    {
        $data = DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT cvalue
            FROM %t
            WHERE ckey = %s
            ',
            array('appbyme_config', $isTemp ? self::NAV_KEY_TEMP : self::NAV_KEY)
        );
        return $data ? (array)unserialize(WebUtils::u($data)) : array();
    }

    public static function saveNavigationInfo($navInfo, $isTemp=false)
    {
        $key = $isTemp ? self::NAV_KEY_TEMP : self::NAV_KEY;
        $appUIDiyNavInfo = array(
            'ckey' => $key, 
            'cvalue' => WebUtils::t(serialize($navInfo)),
        );
        $config = DbUtils::getDzDbUtils(true)->queryRow('
            SELECT * 
            FROM %t 
            WHERE ckey=%s
            ',
            array('appbyme_config', $key)
        );
        if (empty($config)) {
            DbUtils::getDzDbUtils(true)->insert('appbyme_config', $appUIDiyNavInfo);
        } else {
            DbUtils::getDzDbUtils(true)->update('appbyme_config', $appUIDiyNavInfo, array('ckey' => $key));
        }
        return true;
    }

    public static function initModules()
    {
        return array(
            self::initDiscoverModule(),
            self::initFastpostModule(),
            self::initModule(array(
                'id' => 3,
                'title' => '首页',
                'type' => self::MODULE_TYPE_SUBNAV,
                'rightTopbars' => array(
                    self::initComponent(array(
                        'type' => self::COMPONENT_TYPE_USERINFO,
                        'icon' => self::COMPONENT_ICON_TOPBAR.'6',
                    )),
                    self::initComponent(array(
                        'type' => self::COMPONENT_TYPE_EMPTY,
                    )),
                ),
            )),
            self::initModule(array(
                'id' => 4,
                'title' => '社区',
                'type' => self::MODULE_TYPE_SUBNAV,
                'componentList' => array(
                    self::initComponent(array(
                        'type' => self::COMPONENT_TYPE_FORUMLIST,
                        'title' => '版块',
                    )),
                    self::initComponent(),
                    self::initComponent(),
                    self::initComponent(),
                ),
                'rightTopbars' => array(
                    self::initComponent(array(
                        'type' => self::COMPONENT_TYPE_SEARCH,
                        'icon' => self::COMPONENT_ICON_TOPBAR.'10',
                    )),
                    self::initComponent(array(
                        'type' => self::COMPONENT_TYPE_USERINFO,
                        'icon' => self::COMPONENT_ICON_TOPBAR.'6',
                    )),
                ),
            )),
            self::initModule(array(
                'id' => 5,
                'title' => '消息',
                'type' => self::MODULE_TYPE_FULL,
                'componentList' => array(
                    self::initComponent(array(
                        'type' => self::COMPONENT_TYPE_MESSAGELIST,
                    )),
                ),
            )),
        );
    }

    public static function initModule($params=array())
    {
        return array_merge(array(
            'id' => 0,
            'type' => self::MODULE_TYPE_FULL,
            'style' => self::MODULE_STYLE_CARD,
            'title' => '',
            'icon' => Yii::app()->getController()->rootUrl.'/images/admin/module-default.png',
            'leftTopbars' => array(
                self::initComponent(array(
                    'type' => self::COMPONENT_TYPE_EMPTY,
                )),
            ),
            'rightTopbars' => array(
                self::initComponent(array(
                    'type' => self::COMPONENT_TYPE_EMPTY,
                )),
                self::initComponent(array(
                    'type' => self::COMPONENT_TYPE_EMPTY,
                )),
            ),
            'componentList' => array(),
            'extParams' => array('padding' => '',),
        ), $params);
    }

    public static function initDiscoverModule()
    {
        return self::initModule(array(
            'id' => self::MODULE_ID_DISCOVER,
            'title' => '发现',
            'componentList' => array(self::initComponentDiscover()),
        ));
    }
    
    public static function initFastpostModule()
    {
        return self::initModule(array(
            'id' => self::MODULE_ID_FASTPOST,
            'title' => '快速发表',
            'type' => self::MODULE_TYPE_FASTPOST,
            'componentList' => array(
                self::initComponent(array(
                    'type' => self::COMPONENT_TYPE_FASTTEXT,
                    'title' => '文字',
                    'icon' => self::COMPONENT_ICON_FASTPOST . '27',
                )),
                self::initComponent(array(
                    'type' => self::COMPONENT_TYPE_FASTIMAGE,
                    'title' => '图片',
                    'icon' => self::COMPONENT_ICON_FASTPOST . '28',
                )),
                self::initComponent(array(
                    'type' => self::COMPONENT_TYPE_FASTCAMERA,
                    'title' => '拍照',
                    'icon' => self::COMPONENT_ICON_FASTPOST . '29',
                )),
                self::initComponent(array(
                    'type' => self::COMPONENT_TYPE_FASTAUDIO,
                    'title' => '语音',
                    'icon' => self::COMPONENT_ICON_FASTPOST . '45',
                )),
                // self::initComponent(array(
                //     'type' => self::COMPONENT_TYPE_SIGN,
                //     'title' => '签到',
                //     'icon' => self::COMPONENT_ICON_FASTPOST . '30',
                // )),
            ),
        ));
    }

    public static function getModules($isTemp=false)
    {
        $data = DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT cvalue
            FROM %t
            WHERE ckey = %s
            ',
            array('appbyme_config', $isTemp ? self::MODULE_KEY_TEMP : self::MODULE_KEY)
        );
        return $data ? (array)unserialize(WebUtils::u($data)) : array();
    }

    public static function saveModules($modules, $isTemp=false)
    {
        $key = $isTemp ? self::MODULE_KEY_TEMP : self::MODULE_KEY;
        $appUIDiyModules = array(
            'ckey' => $key, 
            'cvalue' => WebUtils::t(serialize($modules)),
        );
        $config = DbUtils::getDzDbUtils(true)->queryRow('
            SELECT * 
            FROM %t 
            WHERE ckey=%s
            ',
            array('appbyme_config', $key)
        );
        if (empty($config)) {
            DbUtils::getDzDbUtils(true)->insert('appbyme_config', $appUIDiyModules);
        } else {
            DbUtils::getDzDbUtils(true)->update('appbyme_config', $appUIDiyModules, array('ckey' => $key));
        }
        return true;
    }

    public static function deleteNavInfo($isTemp=false)
    {
        return DbUtils::getDzDbUtils(true)->delete('appbyme_config', array(
            'where' => 'ckey = %s',
            'arg' => array($isTemp ? self::NAV_KEY_TEMP : self::NAV_KEY),
        ));
    }

    public static function deleteModules($isTemp=false)
    {
        return DbUtils::getDzDbUtils(true)->delete('appbyme_config', array(
            'where' => 'ckey = %s',
            'arg' => array($isTemp ? self::MODULE_KEY_TEMP : self::MODULE_KEY),
        ));
    }

    public static function initComponent($params=array())
    {
        return array_merge(array(
            'id' => '',
            'type' => self::COMPONENT_TYPE_DEFAULT,
            'style' => self::COMPONENT_STYLE_FLAT,
            'title' => '',
            'desc' => '',
            // 'icon' => Yii::app()->getController()->rootUrl.'/images/admin/module-default.png',
            'icon' => '',
            'iconStyle' => self::COMPONENT_ICON_STYLE_IMAGE,
            'componentList' => array(),
            'extParams' => array(
                'titlePosition' => self::COMPONENT_TITLE_POSITION_LEFT,
                // 'isShowForumIcon' => 1,
                // 'isShowForumTwoCols' => 1,
                'pageTitle' => '',
                'newsModuleId' => 0,
                'forumId' => 0,
                'moduleId' => 0,
                'topicId' => 0,
                'articleId' => 0,
                'fastpostForumIds' => array(),
                'isShowTopicTitle' => 1,
                // 'isShowTopicSort' => 0,
                'isShowMessagelist' => 1,
                'filter' => '',
                'orderby' => '',
                'redirect' => '',
            ),
        ), $params);
    }

    public static function initComponentDiscover()
    {
        return self::initComponent(array(
            'type' => self::COMPONENT_TYPE_DISCOVER,
            'componentList' => array(
                self::initLayout(array(
                    'style' => self::COMPONENT_STYLE_DISCOVER_SLIDER,
                    'componentList' => array(
                    ),
                )),
                self::initLayout(array(
                    'style' => self::COMPONENT_STYLE_DISCOVER_DEFAULT,
                    'componentList' => array(
                        self::initComponent(array(
                            'title' => '个人中心',
                            'type' => self::COMPONENT_TYPE_USERINFO,
                            'icon' => self::COMPONENT_ICON_DISCOVER_DEFAULT . '9',
                        )),
                        self::initComponent(array(
                            'title' => '周边用户',
                            'type' => self::COMPONENT_TYPE_USERLIST,
                            'icon' => self::COMPONENT_ICON_DISCOVER_DEFAULT . '5',
                            'extParams' => array(
                                'filter' => self::USERLIST_FILTER_ALL,
                                'orderby' => self::USERLIST_ORDERBY_DISTANCE,
                            ),
                        )),
                        self::initComponent(array(
                            'title' => '周边帖子',
                            'type' => self::COMPONENT_TYPE_SURROUDING_POSTLIST,
                            'icon' => self::COMPONENT_ICON_DISCOVER_DEFAULT . '4',
                        )),
                        self::initComponent(array(
                            'title' => '推荐用户',
                            'type' => self::COMPONENT_TYPE_USERLIST,
                            'icon' => self::COMPONENT_ICON_DISCOVER_DEFAULT . '6',
                            'extParams' => array(
                                'filter' => self::USERLIST_FILTER_RECOMMEND,
                                'orderby' => self::USERLIST_ORDERBY_DATELINE,
                            )
                        )),
                        self::initComponent(array(
                            'title' => '设置',
                            'type' => self::COMPONENT_TYPE_SETTING,
                            'icon' => self::COMPONENT_ICON_DISCOVER_DEFAULT . '7',
                        )),
                        // self::initComponent(array(
                        //     'title' => '关于',
                        //     'type' => self::COMPONENT_TYPE_ABOAT,
                        // )),
                    ),
                )),
                self::initLayout(array(
                    'style' => self::COMPONENT_STYLE_DISCOVER_CUSTOM,
                    'componentList' => array(
                    ),
                )),
            ),
        ));
    }

    public static function initLayout($params=array())
    {
        return self::initComponent(array_merge(array(
            'type' => self::COMPONENT_TYPE_LAYOUT,
            'style' => self::COMPONENT_STYLE_LAYOUT_DEFAULT,
        ), $params));
    }
}
