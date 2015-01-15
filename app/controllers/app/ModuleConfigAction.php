<?php

/**
 * 获取模块配置接口
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class ModuleConfigAction extends MobcentAction
{
    public function run($moduleId)
    {
        $res = $this->initWebApiArray();
        $res['body'] = $this->_getModuleconfig($moduleId);
        $res['head']['errInfo'] = '';
        echo WebUtils::outputWebApi($res, 'utf-8', false);
    }

    private function _getModuleconfig($moduleId)
    {
        $module = array('padding' => '');
        foreach (AppbymeUIDiyModel::getModules() as $tmpModule) {
            if ($tmpModule['id'] == $moduleId) {
                $tempComponentList = array();
                foreach ($tmpModule['componentList'] as $component) {
                    $component = $this->_filterComponent($component);
                    $tempComponentList[] = $component;
                }
                $tmpModule['componentList'] = $tempComponentList;
                
                $module = $tmpModule;
                break;
            }
        }
        return array(
            'module' => $module,
        );
    }

    private function _filterComponent($component) {
        $tempComponent = $component;

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
                $tempComponentList[] = $this->_filterComponent($subComponent);
            }
        }
        $tempComponent['componentList'] = $tempComponentList;

        return $tempComponent;
    }
}
