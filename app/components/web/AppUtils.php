<?php

/**
 * 客户端相关工具类
 *
 * @author 谢建平 <xiejianping@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class AppUtils {

    const LEVEL_FREE = 0;

    public static function getAppId() {
        return AppbymeConfig::getForumkey();
    }

    public static function getAppLevel() {
        $appId = self::getAppId();
        // $appId = 'o8n5mW6eo6AP8A5Hmb';
        // $appId = 'pmjAXPiqj7RKAiPrbL';

        $url = sprintf('http://sdk.mobcent.com/baikesdk/pay/payState.do?gzip=0&forumKey=%s', $appId);
        $data = WebUtils::jsonDecode(WebUtils::httpRequest($url, 10));

        return (int)(isset($data['data']['paystate']['user_defined']) ? $data['data']['paystate']['user_defined'] : self::LEVEL_FREE);
    }

    public static function filterComponent($component) {
        $tempComponent = $component;

        // 临时处理
        if ($component['type'] == AppbymeUIDiyModel::COMPONENT_TYPE_FORUMLIST) {
            $tempComponent['extParams']['forumId'] = 0;
        }
        $tempComponent['style'] = self::_filterStyle($component['style']);
        $tempComponent['extParams']['subListStyle'] = self::_filterStyle($component['extParams']['subListStyle']);
        $tempComponent['extParams']['subDetailViewStyle'] = self::_filterStyle($component['extParams']['subDetailViewStyle']);

        // 转换componentList结构
        $tempComponentList = array();
        if ($tempComponent['style'] == AppbymeUIDiyModel::COMPONENT_STYLE_LAYOUT_NEWS_AUTO && 
            count($tempComponent['componentList']) > 0 &&
            $tempComponent['componentList'][0]['type'] == AppbymeUIDiyModel::COMPONENT_TYPE_NEWSLIST) {
            $newslist = WebUtils::httpRequestAppAPI('portal/newslist', array('moduleId' => $tempComponent['componentList'][0]['extParams']['newsModuleId']));
            if ($newslist = WebUtils::jsonDecode($newslist)) {
                foreach ($newslist['list'] as $key => $value) {
                    $tempParam = array(
                        'title' => $value['title'],
                        'desc' => $value['summary'],
                        'icon' => $value['pic_path'],
                    );
                    if ($value['source_type'] == 'topic') {
                        $tempParam = array_merge($tempParam, array(
                            'type' => AppbymeUIDiyModel::COMPONENT_TYPE_POSTLIST,
                            'extParams' => array('topicId' => $value['source_id']),
                        ));
                    } else if ($value['source_type'] == 'weblink') {
                        $tempParam = array_merge($tempParam, array(
                            'type' => AppbymeUIDiyModel::COMPONENT_TYPE_WEBAPP,
                            'extParams' => array('redirect' => $value['redirectUrl']),
                        ));
                    } else if ($value['source_type'] == 'news') {
                        $tempParam = array_merge($tempParam, array(
                            'type' => AppbymeUIDiyModel::COMPONENT_TYPE_NEWSVIEW,
                            'extParams' => array('articleId' => $value['source_id']),
                        ));
                    }
                    $tempComponentList[] = array_merge(AppbymeUIDiyModel::initComponent(), $tempParam);
                }
            }
        } else {
            foreach ($tempComponent['componentList'] as $subComponent) {
                $tempComponentList[] = self::filterComponent($subComponent);
            }
        }
        $tempComponent['componentList'] = $tempComponentList;

        return $tempComponent;
    }

    private static function _filterStyle($style) {
        $tempStyle = $style;
        if (!empty($_GET['sdkVersion']) && $_GET['sdkVersion'] < '2.4.0') {
            if ($style == AppbymeUIDiyModel::COMPONENT_STYLE_TIEBA || $style == AppbymeUIDiyModel::COMPONENT_STYLE_NETEASE_NEWS) {
                $tempStyle = AppbymeUIDiyModel::COMPONENT_STYLE_FLAT;
            }
            if ($style == AppbymeUIDiyModel::COMPONENT_STYLE_IMAGE_2) {
                $tempStyle = AppbymeUIDiyModel::COMPONENT_STYLE_IMAGE;
            }
        }
        return $tempStyle;
    }

    public static function filterTopbars($topbars) {
        $tempTopbars = array();
        foreach ($topbars as $topbar) {
            $tempTopbars[] = self::filterComponent($topbar);
        }
        return $tempTopbars;
    }

    public static function filterModule($module) {
        $tempModule = array();

        $tempModule['leftTopbars'] = self::filterTopbars($module['leftTopbars']);
        $tempModule['rightTopbars'] = self::filterTopbars($module['rightTopbars']);
        
        $tempComponentList = array();
        foreach ($module['componentList'] as $component) {
            $tempComponentList[] = self::filterComponent($component);
        }                    
        $tempModule['componentList'] = $tempComponentList;

        return $tempModule;
    }
}