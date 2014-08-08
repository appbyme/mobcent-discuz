<?php

/**
 * 应用 >> 安米手机客户端 >> 门户管理
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 * @license http://opensource.org/licenses/LGPL-3.0
 */

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

defined('MAX_PORTAL_MODULE_LEN') or define('MAX_PORTAL_MODULE_LEN', 6);
defined('MAX_PORTAL_MODULE_SLIDER_LEN') or define('MAX_PORTAL_MODULE_SLIDER_LEN', 5);

require_once dirname(__FILE__) . '/appbyme.class.php';
Appbyme::init();

loadcache('plugin');
global $_G;
$setting = $_G['cache']['plugin'][Appbyme::PLUGIN_ID];

if ($setting['portal_allow_open'] != 1) {
    cpmsg(Appbyme::lang('mobcent_error_portal_not_allow'), '', 'error');
}

$baseUrl = rawurldecode(cpurl());

!isset($_GET['anchor']) && $_GET['anchor'] = 'index';
$anchor = $_GET['anchor'];

if (!submitcheck('portal_submit')) {
    $formUrl = ltrim($baseUrl, 'action=') . '&page=1';
    showtagheader('div', 'portal_module', true);
    showformheader($formUrl);

    switch ($anchor) {
        case 'index': PortalController::showModuleView(); break;
        case 'source': PortalController::showSourceView(); break;
        case 'slider': PortalController::showSliderView(); break;
        case 'module_param': PortalController::showModuleParamView(); break;
        default: cpmsg(Appbyme::lang('mobcent_error_portal_not_allow'), '', 'error'); break;
    }

    showformfooter();
    showtagfooter('div');
} else {
    switch ($anchor) {
        case 'index': PortalController::submitModule(); break;
        case 'source': PortalController::submitSource(); break;
        case 'slider': PortalController::submitSlider(); break;
        case 'module_param': PortalController::submitModuleParam(); break;
        default: cpmsg(Appbyme::lang('mobcent_error_portal_not_allow'), '', 'error'); break;
    }

    cpmsg(Appbyme::lang('mobcent_portal_edit_succeed'), $baseUrl, 'succeed');
}

class PortalController {

    public static function showModuleView() {
        $url = rawurldecode(cpurl());

        showtableheader(Appbyme::lang('mobcent_portal_module_setting'));
        showsubtitle(array(
            '', 'display_order',
            Appbyme::lang('mobcent_portal_module_name'), 
        ));

        $moduleList = PortalModule::getModules();
        foreach ($moduleList as $module) {
            showtablerow('', array('class="td25"', 'class="td28"'), array(
                sprintf('<input type="checkbox" class="checkbox" name="delete[]" value="%d" />', $module['mid']),
                sprintf('<input type="text" class="txt" size="2" name="displayorder_new[%d]" value="%d" />', $module['mid'], $module['displayorder']),
                sprintf('<input type="text" size="30" name="name_new[%d]" value="%s" />', $module['mid'], $module['name']),
                sprintf('<a href="%s?%s&anchor=source&moduleid=%d" target="_self" class="act">%s</a>', 
                    ADMINSCRIPT, $url, $module['mid'], 
                    Appbyme::lang('mobcent_portal_module_source_edit')
                ),
                sprintf('<a href="%s?%s&anchor=slider&moduleid=%d" target="_self" class="act">%s</a>', 
                    ADMINSCRIPT, $url, $module['mid'],
                    Appbyme::lang('mobcent_portal_module_slider_edit')
                ),
                sprintf('<a href="%s?%s&anchor=module_param&moduleid=%d" target="_self" class="act">%s</a>', 
                    ADMINSCRIPT, $url, $module['mid'],
                    Appbyme::lang('mobcent_portal_module_param_edit')
                ),
            ));
        }
        if (count($moduleList) < MAX_PORTAL_MODULE_LEN) {
            showtablerow('', array('class="td25"', 'class="td28"'), array(
                cplang('add_new'),
                sprintf('<input type="text" class="txt" size="2" maxlength="4" name="new_displayorder" value="" />'),
                sprintf('<input type="text" size="30" name="new_name" value="" />'),
            ));
        }

        showsubmit('portal_submit', 'submit', 'del');
        showtablefooter();
    }

    public static function showSourceView() {
        $url = rawurldecode(cpurl());

        showtips(Appbyme::lang('mobcent_tips_portal_module_source'));

        showtableheader(Appbyme::lang('mobcent_portal_module_source_edit'));
        showsubtitle(array(
            '', 'display_order', 'id', Appbyme::lang('mobcent_portal_module_source_type'), 'name',
        ));

        $mid = (int)$_GET['moduleid'];
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $page >= 1 || $page = 1;
        $pagesize = 10;

        $disableSourceTypeFid = PortalModule::getSourceCount($mid, PortalModule::SOURCE_TYPE_NORMAL, array('idtype' => array(PortalModule::SOURCE_TYPE_CATID))) > 0;
        $disableSourceTypeCatid = PortalModule::getSourceCount($mid, PortalModule::SOURCE_TYPE_NORMAL, array('idtype' => array(PortalModule::SOURCE_TYPE_FID))) > 0;
        
        $sourceCount = PortalModule::getSourceCount($mid);
        $sourceList = PortalModule::getSources($mid, PortalModule::SOURCE_TYPE_NORMAL, $page, $pagesize);
        foreach ($sourceList as $source) {
            showtablerow('', array('class="td25"', 'class="td28"'), array(
                sprintf('<input type="checkbox" class="checkbox" name="delete[]" value="%d" />', $source['sid']),
                sprintf('<input type="text" class="txt" size="2" name="displayorder_new[%d]" value="%d" />', $source['sid'], $source['displayorder']),
                sprintf('<input type="text" size="40" name="id_new[%d]" value="%s" />', $source['sid'], $source['id']),
                sprintf('
                    <select name="idtype_new[%d]">
                        <option value="%s" %s>%s</option>
                        <option value="%s" %s>%s</option>
                        <option value="%s" %s %s>%s</option>
                        <option value="%s" %s %s>%s</option>
                    </select>',
                    $source['sid'],
                    PortalModule::SOURCE_TYPE_AID,
                    $source['idtype'] == PortalModule::SOURCE_TYPE_AID ? 'selected' : '',
                    Appbyme::lang('mobcent_portal_module_source_type_aid'),
                    PortalModule::SOURCE_TYPE_TID,                
                    $source['idtype'] == PortalModule::SOURCE_TYPE_TID ? 'selected' : '',
                    Appbyme::lang('mobcent_portal_module_source_type_tid'),
                    PortalModule::SOURCE_TYPE_FID,
                    $source['idtype'] == PortalModule::SOURCE_TYPE_FID ? 'selected' : '',
                    $disableSourceTypeFid ? 'disabled' : '',
                    Appbyme::lang('mobcent_portal_module_source_type_fid'),
                    PortalModule::SOURCE_TYPE_CATID,
                    $source['idtype'] == PortalModule::SOURCE_TYPE_CATID ? 'selected' : '',
                    $disableSourceTypeCatid ? 'disabled' : '',
                    Appbyme::lang('mobcent_portal_module_source_type_catid')
                ),
                $source['title'],
            ));
        }
        $multipage = multi(
            $sourceCount, $pagesize, $page, 
            sprintf('%s?%s&pagesize=%d', ADMINSCRIPT, $url, $pagesize), 
            0, 6
        );

        showtablerow('', array('class="td25"', 'class="td28"'), array(
            cplang('add_new'),
            sprintf('<input type="text" class="txt" size="2" maxlength="4" name="new_displayorder" value="" />'),
            sprintf('<input type="text" size="40" name="new_ids" value="" />%s', 
                Appbyme::lang('mobcent_tips_portal_module_source_add')
            ),
            sprintf('
                <select name="new_idtype">
                    <option value="%s">%s</option>
                    <option value="%s">%s</option>
                    <option value="%s" %s>%s</option>
                    <option value="%s" %s>%s</option>
                </select>',
                PortalModule::SOURCE_TYPE_AID,
                Appbyme::lang('mobcent_portal_module_source_type_aid'),
                PortalModule::SOURCE_TYPE_TID,                
                Appbyme::lang('mobcent_portal_module_source_type_tid'),
                PortalModule::SOURCE_TYPE_FID,
                $disableSourceTypeFid ? 'disabled' : '',
                Appbyme::lang('mobcent_portal_module_source_type_fid'),
                PortalModule::SOURCE_TYPE_CATID,
                $disableSourceTypeCatid ? 'disabled' : '',
                Appbyme::lang('mobcent_portal_module_source_type_catid')
            ),
        ));

        showsubmit('portal_submit', 'submit', 'del', '', $multipage);
        showtablefooter();
    }

    public static function showSliderView() {
        showtableheader(Appbyme::lang('mobcent_portal_module_slider_edit'));
        showsubtitle(array(
            '', 'display_order',
            Appbyme::lang('mobcent_portal_module_slider_title'),
            Appbyme::lang('mobcent_portal_module_source_type'),
            Appbyme::lang('mobcent_portal_module_source'),
            Appbyme::lang('mobcent_portal_module_slider_type'),
            Appbyme::lang('mobcent_portal_module_slider'),
        ));

        $mid = (int)$_GET['moduleid'];

        $sliderList = PortalModule::getSources($mid, PortalModule::SOURCE_TYPE_SLIDER);
        foreach ($sliderList as $slider) {
            showtablerow('', array('class="td25"', 'class="td28"'), array(
                sprintf('<input type="checkbox" class="checkbox" name="delete[]" value="%d" />', $slider['sid']),
                sprintf('<input type="text" class="txt" size="2" maxlength="4" name="displayorder_new[%d]" value="%d" />', $slider['sid'], $slider['displayorder']),
                sprintf('<input type="text" size="50" name="title_new[%d]" value="%s" />', $slider['sid'], $slider['title']),
                sprintf('
                    <select name="idtype_new[%d]">
                        <option value="%s" %s>%s</option>
                        <option value="%s" %s>%s</option>
                        <option value="%s" %s>%s</option>
                    </select>',
                    $slider['sid'],
                    PortalModule::SOURCE_TYPE_AID,
                    $slider['idtype'] == PortalModule::SOURCE_TYPE_AID ? 'selected' : '',
                    Appbyme::lang('mobcent_portal_module_source_type_aid'),
                    PortalModule::SOURCE_TYPE_TID,
                    $slider['idtype'] == PortalModule::SOURCE_TYPE_TID ? 'selected' : '',
                    Appbyme::lang('mobcent_portal_module_source_type_tid'), 
                    PortalModule::SOURCE_TYPE_URL,
                    $slider['idtype'] == PortalModule::SOURCE_TYPE_URL ? 'selected' : '',
                    'url'
                ),
                sprintf(
                    '<input type="text" size="50" name="id_new[%d]" value="%s" />', 
                    $slider['sid'], 
                    $slider['idtype'] == PortalModule::SOURCE_TYPE_URL ? $slider['url'] : $slider['id']
                ),
                sprintf('
                    <select name="imgtype_new[%d]">
                        <option value="%s" %s>%s</option>
                        <option value="%s" %s>%s</option>
                        <option value="%s" %s>%s</option>
                    </select>',
                    $slider['sid'],
                    PortalModule::SOURCE_TYPE_AID,
                    $slider['imgtype'] == PortalModule::SOURCE_TYPE_AID ? 'selected' : '',
                    Appbyme::lang('mobcent_portal_module_source_type_aid'),
                    PortalModule::SOURCE_TYPE_TID,
                    $slider['imgtype'] == PortalModule::SOURCE_TYPE_TID ? 'selected' : '',
                    Appbyme::lang('mobcent_portal_module_source_type_tid'), 
                    PortalModule::SOURCE_TYPE_URL,
                    $slider['imgtype'] == PortalModule::SOURCE_TYPE_URL ? 'selected' : '',
                    'url'
                ),
                sprintf(
                    '<input type="text" size="50" name="imgid_new[%d]" value="%s" />', 
                    $slider['sid'],
                    $slider['imgtype'] == PortalModule::SOURCE_TYPE_URL ? $slider['imgurl'] : $slider['imgid']
                ),
            ));
        }
        if (count($sliderList) < MAX_PORTAL_MODULE_SLIDER_LEN) {
            showtablerow('', array('class="td25"', 'class="td28"'), array(
                cplang('add_new'),
                sprintf('<input type="text" class="txt" size="2" maxlength="4" name="new_displayorder" value="" />'),
                sprintf('<input type="text" size="50" name="new_title" value="" />'),
                sprintf('
                    <select name="new_idtype">
                        <option value="%s">%s</option>
                        <option value="%s">%s</option>
                        <option value="%s">%s</option>
                    </select>',
                    PortalModule::SOURCE_TYPE_AID,
                    Appbyme::lang('mobcent_portal_module_source_type_aid'),
                    PortalModule::SOURCE_TYPE_TID,
                    Appbyme::lang('mobcent_portal_module_source_type_tid'), 
                    PortalModule::SOURCE_TYPE_URL,
                    'url'
                ),
                sprintf('<input type="text" size="50" name="new_id" value="" />'),
                sprintf('
                    <select name="new_imgtype">
                        <option value="%s">%s</option>
                        <option value="%s">%s</option>
                        <option value="%s">%s</option>
                    </select>',
                    PortalModule::SOURCE_TYPE_AID,
                    Appbyme::lang('mobcent_portal_module_source_type_aid'),
                    PortalModule::SOURCE_TYPE_TID,
                    Appbyme::lang('mobcent_portal_module_source_type_tid'), 
                    PortalModule::SOURCE_TYPE_URL,
                    'url'
                ),
                sprintf('<input type="text" size="50" name="new_imgid" value="" />')
            ));
        }

        showsubmit('portal_submit', 'submit', 'del');
        showtablefooter();

        $style = '
        <style>
            #slider_images_wrapper{width:100%;overflow:hidden;text-align:center;margin:0 auto 10px;}
            .slider_image{width:18%;height:120px;margin:0 1% 1%;display:inline-block;position: relative;border-radius:5px;}
            .slider_image img{border-radius:5px;width:100%;height:100%;position:relative;}
            .slider_image a{position: relative;width: 100%;height: 120px;display: block;}
            .slider_image span{position: relative;font-size: 14px;color: #fff;z-index: 2;text-align: center;margin-top: -20px;display: inline-block;overflow:hidden;height:20px;}
            .slider_image em{position:absolute;bottom:0;left:0;width:100%;height:20px;background:#000;opacity:0.5;}
        </style>';
        echo $style;
        showtagheader('div', 'slider_images_wrapper', true);
        foreach ($sliderList as $slider) {
            echo sprintf(
                '<div class="slider_image"><a href="%s" target="_blank"><img src="%s" alt="%s"><span>%s</span><em></em></a></div>', 
                $slider['url'], $slider['imgurl'], $slider['title'], $slider['title']);
        }
        showtagfooter('div');
    }

    public static function showModuleParamView() {
        $mid = (int)$_GET['moduleid'];

        $showSourceTypeFid = PortalModule::getSourceCount($mid, PortalModule::SOURCE_TYPE_NORMAL, array('idtype' => array(PortalModule::SOURCE_TYPE_FID))) > 0;
        $showSourceTypeCatid = PortalModule::getSourceCount($mid, PortalModule::SOURCE_TYPE_NORMAL, array('idtype' => array(PortalModule::SOURCE_TYPE_CATID))) > 0;

        !$showSourceTypeFid && !$showSourceTypeCatid && cpmsg(Appbyme::lang('mobcent_error_portal_module_param'), '', 'error');
        
        showtagheader('div', 'portal_module_param', true);
        showtableheader(Appbyme::lang('mobcent_portal_module_param_edit'));

        $digestLang = explode(',', Appbyme::lang('mobcent_portal_module_param_topic_digest'));
        $stickLang = explode(',', Appbyme::lang('mobcent_portal_module_param_topic_stick'));
        $specialLang = explode(',', Appbyme::lang('mobcent_portal_module_param_topic_special'));
        $topicOrderbyLang = explode(',', Appbyme::lang('mobcent_portal_module_param_topic_orderby'));
        $timeLang = explode(',', Appbyme::lang('mobcent_portal_module_param_time'));
        $topicStyleLang = explode(',', Appbyme::lang('mobcent_portal_module_param_topic_style'));

        $articleOrderbyLang = explode(',', Appbyme::lang('mobcent_portal_module_param_article_ordby'));
        
        $module = PortalModule::getModule($mid);
        $param = unserialize($module['param']);
        $param == false && $param = PortalModule::initModuleParam();
        
        if ($showSourceTypeFid) {
            showsetting($digestLang[0], array('param[topic_digest]', array(
                array(1, $digestLang[1]),
                array(2, $digestLang[2]),
                array(3, $digestLang[3]),
                array(0, $digestLang[4]),
            )), $param['topic_digest'], 'mcheckbox', '', 0, '', '', '', true);
            showsetting($stickLang[0], array('param[topic_stick]', array(
                array(1, $stickLang[1]),
                array(2, $stickLang[2]),
                array(3, $stickLang[3]),
                array(0, $stickLang[4]),
            )), $param['topic_stick'], 'mcheckbox', '', 0, '', '', '', true);
            showsetting($specialLang[0], array('param[topic_special]', array(
                array(1, $specialLang[1]),
                array(2, $specialLang[2]),
                array(3, $specialLang[3]),
                array(4, $specialLang[4]),
                array(5, $specialLang[5]),
                array(0, $specialLang[6]),
            )), $param['topic_special'], 'mcheckbox', '', 0, '', '', '', true);
            showsetting(Appbyme::lang('mobcent_portal_module_param_topic_picrequired'), 
                'param[topic_picrequired]', $param['topic_picrequired'], 
                'radio', '', 0, '', '', '', true
            );
            showsetting($topicOrderbyLang[0], array('param[topic_orderby]', array(
                array('lastpost', $topicOrderbyLang[1]),
                array('dateline', $topicOrderbyLang[2]),
                array('replies', $topicOrderbyLang[3]),
                array('views', $topicOrderbyLang[4]),
                array('heats', $topicOrderbyLang[5]),
                array('recommends', $topicOrderbyLang[6]),
            )), $param['topic_orderby'], 'select', '', 0, '', '', '', true);
            showsetting(Appbyme::lang('mobcent_portal_module_param_topic_postdateline'), array('param[topic_postdateline]', array(
                array(0, $timeLang[0]),
                array(3600, $timeLang[1]),
                array(86400, $timeLang[2]),
                array(604800, $timeLang[3]),
                array(2592000, $timeLang[4]),
            )), $param['topic_postdateline'], 'select', '', 0, '', '', '', true);
            showsetting(Appbyme::lang('mobcent_portal_module_param_topic_lastpost'), array('param[topic_lastpost]', array(
                array(0, $timeLang[0]),
                array(3600, $timeLang[1]),
                array(86400, $timeLang[2]),
                array(604800, $timeLang[3]),
                array(2592000, $timeLang[4]),
            )), $param['topic_lastpost'], 'select', '', 0, '', '', '', true);
            showsetting(Appbyme::lang('mobcent_portal_module_param_style'), array('param[topic_style]', array(
                array(1, $topicStyleLang[0]),
                array(2, $topicStyleLang[1]),
            )), $param['topic_style'], 'select', '', 0, '', '', '', true);
        }

        if ($showSourceTypeCatid) {
            showsetting(Appbyme::lang('mobcent_portal_module_param_article_picrequired'),
                'param[article_picrequired]', $param['article_picrequired'],
                'radio', '', 0, '', '', '', true
            );
            showsetting(Appbyme::lang('mobcent_portal_module_param_article_starttime'),
                'param[article_starttime]', 
                $param['article_starttime'] ? dgmdate($param['article_starttime'], 'Y-n-j H:i') : '',
                'calendar', '', 0, '', 1, '', true
            );
            showsetting(Appbyme::lang('mobcent_portal_module_param_article_endtime'),
                'param[article_endtime]', 
                $param['article_endtime'] ? dgmdate($param['article_endtime'], 'Y-n-j H:i') : '',
                'calendar', '', 0, '', 1, '', true
            );
            showsetting($articleOrderbyLang[0], array('param[article_orderby]', array(
                array('dateline', $articleOrderbyLang[1]),
                array('viewnum', $articleOrderbyLang[2]),
                array('commentnum', $articleOrderbyLang[3]),
            )), $param['article_orderby'], 'select', '', 0, '', '', '', true);
            showsetting(Appbyme::lang('mobcent_portal_module_param_article_publishdateline'), array('param[article_publishdateline]', array(
                array(0, $timeLang[0]),
                array(3600, $timeLang[1]),
                array(86400, $timeLang[2]),
                array(604800, $timeLang[3]),
                array(2592000, $timeLang[4]),
            )), $param['article_publishdateline'], 'select', '', 0, '', '', '', true);
        }
        echo '<script type="text/javascript" src="static/js/calendar.js"></script>';

        showsubmit('portal_submit', 'submit');

        showtablefooter();
        showtagfooter('div');
    }

    public static function submitModule() {
        // 删除选中模块
        if (isset($_POST['delete'])) {
            PortalModule::deleteModules($_POST['delete']);
        }
        // 更新模块
        if (!empty($_POST['displayorder_new'])) {
            foreach ($_POST['displayorder_new'] as $id => $displayorder) {
                PortalModule::updateModule($id, array(
                    'name' => $_POST['name_new'][$id],
                    'displayorder' => $displayorder,
                ));
            }
        }
        // 增加新模块
        if (!empty($_POST['new_name'])) {
            PortalModule::insertModule(array(
                'name' => $_POST['new_name'],
                'displayorder' => (int)$_POST['new_displayorder'],
                'param' => serialize(PortalModule::initModuleParam()),
            ));
        }
    }

    public static function submitSource() {
        // 删除选中来源
        $mid = (int)$_GET['moduleid'];
        if (isset($_POST['delete'])) {
            PortalModule::deleteSources($_POST['delete']);
        }
        // 更新来源
        if (!empty($_POST['displayorder_new'])) {
            foreach ($_POST['displayorder_new'] as $id => $displayorder) {
                $source = PortalModule::getSourceInfo($_POST['id_new'][$id], $_POST['idtype_new'][$id]);
                $source = array_merge($source, array('displayorder' => $displayorder,));
                PortalModule::updateSource($id, $source);
            }
        }
        // 增加新来源
        if (!empty($_POST['new_ids'])) {
            $sourceIds = explode(',', $_POST['new_ids']);
            foreach ($sourceIds as $id) {
                $source = PortalModule::getSourceInfo($id, $_POST['new_idtype']);
                $source = array_merge($source, array(
                        'mid' => $mid,
                        'displayorder' => (int)$_POST['new_displayorder'],
                        'type' => PortalModule::SOURCE_TYPE_NORMAL,
                        'param' => '',
                ));
                PortalModule::insertSource($source);
            }
        }
    }

    public static function submitSlider() {
        $mid = (int)$_GET['moduleid'];
        // 删除选中幻灯片
        if (isset($_POST['delete'])) {
            PortalModule::deleteSources($_POST['delete']);
        }
        // 更新幻灯片
        if (!empty($_POST['displayorder_new'])) {
            foreach ($_POST['displayorder_new'] as $id => $displayorder) {
                $source = PortalModule::getSourceInfo(
                    $_POST['id_new'][$id], $_POST['idtype_new'][$id], 
                    $_POST['imgid_new'][$id], $_POST['imgtype_new'][$id],
                    $_POST['title_new'][$id]
                );
                $source = array_merge($source, array('displayorder' => $displayorder,));
                PortalModule::updateSource($id, $source);
            }
        }
        // 增加新幻灯片
        if (!empty($_POST['new_id']) && !empty($_POST['new_imgid'])) {
            $source = PortalModule::getSourceInfo(
                $_POST['new_id'], $_POST['new_idtype'],
                $_POST['new_imgid'], $_POST['new_imgtype'],
                $_POST['new_title']
            );
            $source = array_merge($source, array(
                    'mid' => $mid,
                    'displayorder' => (int)$_POST['new_displayorder'],
                    'type' => PortalModule::SOURCE_TYPE_SLIDER,
                    'param' => '',
            ));
            PortalModule::insertSource($source);
        }
    }

    public static function submitModuleParam() {
        $mid = (int)$_GET['moduleid'];
        $param = PortalModule::initModuleParam();
        // 更新模块参数
        if (!empty($_GET['param']) && is_array($_GET['param'])) {
            $param = array_merge($param, $_GET['param']);
            $param['article_starttime'] = self::_strToTime($param['article_starttime']);
            $param['article_endtime'] = self::_strToTime($param['article_endtime']);
            if($param['article_endtime'] && $param['article_starttime'] > $param['article_endtime']) {
                cpmsg(Appbyme::lang('mobcent_error_time_invalid'), '', 'error');
            }
            PortalModule::updateModule($mid, array(
                'param' => serialize($param),
            ));
        }
    }

    private static function _strToTime($time) {
        if (strpos($time, '-')){
            if (($time = strtotime($time)) === false || $time === -1) {
                $time = 0;
            }
        } else {
            $time = 0;
        }
        return $time;
    }
}

class PortalModule {

    const SOURCE_TYPE_NORMAL = 1;
    const SOURCE_TYPE_SLIDER = 2;

    const SOURCE_TYPE_AID = 'aid';
    const SOURCE_TYPE_TID = 'tid';
    const SOURCE_TYPE_FID = 'fid';
    const SOURCE_TYPE_CATID = 'catid';
    const SOURCE_TYPE_URL = 'url';

    public static function getModules() {
        return DB::fetch_all('
            SELECT *
            FROM %t
            ORDER BY displayorder ASC
            ', 
            array('appbyme_portal_module')
        );
    }
    
    public static function getModule($mid) {
        return DB::fetch_first('
            SELECT *
            FROM %t
            WHERE mid=%d
            ', 
            array('appbyme_portal_module', $mid)
        );
    }

    public static function initModuleParam() {
        return array(
            'topic_digest' => '',
            'topic_stick' => '',
            'topic_special' => '',
            'topic_picrequired' => 0,
            'topic_orderby' => 'lastpost',
            'topic_postdateline' => 0,
            'topic_lastpost' => 0,
            'topic_style' => 0,
            'article_picrequired' => 0,
            'article_starttime' => 0,
            'article_endtime' => 0,
            'article_orderby' => 'dateline',
            'article_publishdateline' => 0,
        );
    }

    public static function insertModule($module) {
        DB::insert('appbyme_portal_module', $module);
    }

    public static function updateModule($id, $module) {
        DB::update('appbyme_portal_module', $module, 'mid='.$id);
    }

    public static function deleteModules($mids) {
        DB::delete('appbyme_portal_module', array(
            'where' => 'mid IN (%n)', 
            'arg' => array($mids)
        ));
        DB::delete('appbyme_portal_module_source', array(
            'where' => 'mid IN (%n)', 
            'arg' => array($mids)
        ));
    }

    public static function getSourceCount($mid, $type=self::SOURCE_TYPE_NORMAL, 
                                        $params=array('idtype' => array(
                                            self::SOURCE_TYPE_AID,
                                            self::SOURCE_TYPE_TID,
                                            self::SOURCE_TYPE_FID,
                                            self::SOURCE_TYPE_CATID,
                                            self::SOURCE_TYPE_URL,
                                        ))) {
        $count = DB::fetch_first('
            SELECT COUNT(*) AS count
            FROM %t
            WHERE mid=%d AND type=%d 
            AND idtype IN (%n)
            ',
            array('appbyme_portal_module_source', $mid, $type, $params['idtype'])
        );
        return (int)$count['count'];
    }

    public static function getSources($mid, $type=self::SOURCE_TYPE_NORMAL, $page=1, $pagesize=10, $params=array()) {
        return DB::fetch_all('
            SELECT *
            FROM %t
            WHERE mid=%d AND type=%d
            ORDER BY displayorder ASC
            LIMIT %d, %d
            ', 
            array('appbyme_portal_module_source', $mid, $type, ($page-1)*$pagesize, $pagesize)
        );
    }

    public static function insertSource($source) {
        DB::insert('appbyme_portal_module_source', $source);
    }

    public static function updateSource($id, $source) {
        DB::update('appbyme_portal_module_source', $source, 'sid='.$id);
    }

    public static function deleteSources($ids) {
        DB::delete('appbyme_portal_module_source', array(
            'where' => 'sid IN (%n)',
            'arg' => array($_POST['delete'])
        ));
    }

    public static function getSourceInfo($id, $idtype, $imgid=0, $imgtype='', $title='') {
        $source = array(
            'id' => 0, 'url' => '', 'idtype' => $idtype, 
            'imgid' => 0, 'imgurl' => '', 'imgtype' => $imgtype,
            'title' => '',
        );
        switch ($idtype) {
            case self::SOURCE_TYPE_AID:
                $article = DB::fetch_first('
                    SELECT title
                    FROM %t
                    WHERE aid=%d
                    ',
                    array('portal_article_title', $id) 
                );
                $source['id'] = $id;
                $source['url'] = 'portal.php?mod=view&aid='.$id;
                $source['title'] = !empty($article['title']) ? $article['title'] : '';
                break;
            case self::SOURCE_TYPE_TID:
                $topic = DB::fetch_first('
                    SELECT subject
                    FROM %t
                    WHERE tid=%d
                    ',
                    array('forum_thread', $id) 
                );
                $source['id'] = $id;
                $source['url'] = 'forum.php?mod=viewthread&tid='.$id;
                $source['title'] = !empty($topic['subject']) ? $topic['subject'] : '';
                break;
            case self::SOURCE_TYPE_URL:
                $source['url'] = $id;
                break;
            case self::SOURCE_TYPE_FID:
                $forum = DB::fetch_first('
                    SELECT name
                    FROM %t
                    WHERE fid=%d
                    ',
                    array('forum_forum', $id) 
                );
                $source['id'] = $id;
                $source['url'] = 'forum.php?mod=forumdisplay&fid='.$id;
                $source['title'] = !empty($forum['name']) ? $forum['name'] : '';
                break;
            case self::SOURCE_TYPE_CATID:
                $category = DB::fetch_first('
                    SELECT catname
                    FROM %t
                    WHERE catid=%d
                    ',
                    array('portal_category', $id) 
                );
                $source['id'] = $id;
                $source['url'] = 'portal.php?mod=list&catid='.$id;
                $source['title'] = !empty($category['catname']) ? $category['catname'] : '';
                break;
            default:
                break;
        }
        !empty($title) && $source['title'] = $title;

        if ($imgid == 0 && $imgtype == '') {
            $source['imgid'] = $imgid = $id;
            $source['imgtype'] = $imgtype = $idtype;
        }
        switch ($imgtype) {
            case PortalModule::SOURCE_TYPE_AID:
                $article = DB::fetch_first('
                    SELECT pic
                    FROM %t
                    WHERE aid=%d
                    ',
                    array('portal_article_title', $imgid)
                );
                $source['imgid'] = $imgid;
                if (!empty($article)) {
                    require_once DISCUZ_ROOT.'./source/function/function_home.php';
                    $source['imgurl'] = pic_get($article['pic'], '', 0, $article['remote']);
                }
                break;
            case PortalModule::SOURCE_TYPE_TID:
                $topicImage = DB::fetch_first('
                    SELECT *
                    FROM %t
                    WHERE tid=%d
                    ',
                    array('forum_threadimage', $imgid)
                );
                $source['imgid'] = $imgid;
                if (!empty($topicImage)) {
                    require_once DISCUZ_ROOT.'./source/function/function_home.php';
                    $source['imgurl'] = pic_get($topicImage['attachment'], 'forum', 0, $topicImage['remote']);
                }
                break;
            case PortalModule::SOURCE_TYPE_URL:
                $source['imgurl'] = $imgid;
                break;
            default:
                break;
        }
        return $source;
    }
}