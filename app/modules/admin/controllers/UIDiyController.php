<?php

/**
 * UI Diy 控制器
 *
 * @author 谢建平 <jianping_xie@aliyun.com>  
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

// Mobcent::setErrors();
class UIDiyController extends AdminController
{
    public $navItemIconBaseUrlPath = '';

    public function init()
    {
        parent::init();

        $this->navItemIconBaseUrlPath = $this->rootUrl . '/images/admin/icon1';
    }

    public function actionIndex()
    {   
        $newsModules = AppbymePoralModule::getModuleList();
        $forumList = ForumUtils::getForumListForHtml();

        $navInfo = AppbymeUIDiyModel::getNavigationInfo(true);
        empty($navInfo) && $navInfo = AppbymeUIDiyModel::initNavigation();
        $hasDiscoverNavItem = false;
        foreach ($navInfo['navItemList'] as $navItem) {
            if ($navItem['moduleId'] == AppbymeUIDiyModel::MODULE_ID_DISCOVER) {
                $hasDiscoverNavItem = true;
                break;
            }
        }
        !$hasDiscoverNavItem && array_unshift($navInfo['navItemList'], AppbymeUIDiyModel::initNavItemDiscover());

        $modules = array();
        $tempModules = AppbymeUIDiyModel::getModules(true);
        $isFindDiscover = $isFindFastpost = false;
        $discoverModule = AppbymeUIDiyModel::initDiscoverModule();
        $fastpostModule = AppbymeUIDiyModel::initFastpostModule();

        foreach ($tempModules as $module) {
            switch ($module['id']) {
                case AppbymeUIDiyModel::MODULE_ID_DISCOVER:
                    if (!$isFindDiscover) {
                        $isFindDiscover = true;
                        $discoverModule = $module;
                    }
                    break;
                case AppbymeUIDiyModel::MODULE_ID_FASTPOST:
                    if (!$isFindFastpost) {
                        $isFindFastpost = true;
                        $fastpostModule = $module;
                    }
                    break;
                default:
                    $modules[] = $module;
                    break;
            }
        }
        array_unshift($modules, $discoverModule, $fastpostModule);

        $this->renderPartial('index', array(
            'navInfo' => $navInfo,
            'modules' => $modules,
            'newsModules' => $newsModules,
            'forumList' => $forumList,
        ));
    }

    public function actionSaveNavInfo($navInfo, $isSync=false)
    {
        $res = WebUtils::initWebApiResult();

        $navInfo = WebUtils::jsonDecode($navInfo);
        AppbymeUIDiyModel::saveNavigationInfo($navInfo, true);
        $isSync && AppbymeUIDiyModel::saveNavigationInfo($navInfo);

        echo WebUtils::outputWebApi($res, '', false);
    }

    public function actionSaveModules($modules, $isSync=false)
    {
        $res = WebUtils::initWebApiResult();

        $modules = WebUtils::jsonDecode($modules);
        AppbymeUIDiyModel::saveModules($modules, true);

        if ($isSync) {
            $tempModules = array();
            foreach ($modules as $module) {
                $module['leftTopbars'] = $this->_filterTopbars($module['leftTopbars']);
                $module['rightTopbars'] = $this->_filterTopbars($module['rightTopbars']);
                
                $tempComponentList = array();
                foreach ($module['componentList'] as $component) {
                    $component = $this->_filterComponent($component);
                    if ($module['type'] == AppbymeUIDiyModel::MODULE_TYPE_SUBNAV) {
                        if ($component['title'] != '') {
                            $tempComponentList[] = $component;
                        }
                    } else {
                        $tempComponentList[] = $component;
                    }
                }                    
                $module['componentList'] = $tempComponentList;

                $tempModules[] = $module;
            }
            AppbymeUIDiyModel::saveModules($tempModules);
        }

        echo WebUtils::outputWebApi($res, '', false);
    }

    public function actionInit()
    {
        $res = WebUtils::initWebApiResult();

        AppbymeUIDiyModel::deleteNavInfo();
        AppbymeUIDiyModel::deleteNavInfo(true);
        AppbymeUIDiyModel::deleteModules();
        AppbymeUIDiyModel::deleteModules(true);

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _filterTopbars($topbars)
    {
        $tempTopbars = array();
        foreach ($topbars as $topbar) {
            $topbar['type'] != AppbymeUIDiyModel::COMPONENT_TYPE_DEFAULT && $tempTopbars[] = $topbar;
        }
        return $tempTopbars;
    }

    private function _filterComponent($component) 
    {
        loadcache('forums');
        global $_G;
        $forums = $_G['cache']['forums'];

        $tempComponent = $component;
        $tempFastpostForumIds = array();
        foreach ($component['extParams']['fastpostForumIds'] as $fid) {
            $tempFastpostForumIds[] = array(
                'fid' => $fid,
                'title' => WebUtils::u($forums[$fid]['name']),
            );
        }
        $tempComponent['extParams']['fastpostForumIds'] = $tempFastpostForumIds;
        return $tempComponent;
    }
}