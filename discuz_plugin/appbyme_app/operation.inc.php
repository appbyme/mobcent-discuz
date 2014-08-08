<?php

/**
 * 应用 >> 安米手机客户端 >> 运营
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 * @license http://opensource.org/licenses/LGPL-3.0
 */

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

require_once dirname(__FILE__) . '/appbyme.class.php';
Appbyme::init();

$baseUrl = rawurldecode(cpurl());

if (!submitcheck('operation_submit')) {
    $formUrl = ltrim($baseUrl, 'action=');
    
    showtagheader('div', 'operation_manage', true);
    showformheader($formUrl);

    showtableheader(Appbyme::lang('mobcent_operation_setting'));
    
    loadcache('plugin');
    global $_G;
    $dsuPaulsignSetting = $_G['cache']['plugin']['dsu_paulsign'];
    $extcreditsSetting = $_G['setting']['extcredits'];

    // 签到奖励基数设置
    $signExtcreditBase = Appbyme::getDzPluginCache('sign_extcredit_base');
    $signExtcreditBase == false && $signExtcreditBase = 100;
    if (isset($extcreditsSetting[$dsuPaulsignSetting['nrcredit']])) {
        showsetting(
            Appbyme::lang('mobcent_operation_sign_extcredit_base') . ' ' .
            $_G['setting']['extcredits'][$dsuPaulsignSetting['nrcredit']]['title'], 
            'signextcreditbase_new', 
            (int)$signExtcreditBase, 
            'text', '', 0,
            Appbyme::lang('mobcent_tips_extcredit_base'),
            '', '', true
        );
    }
    // 发回帖奖励基数设置
    $forumExtcreditBase = Appbyme::getDzPluginCache('forum_extcredit_base');
    !is_array($forumExtcreditBase) && $forumExtcreditBase = array();
    foreach ($extcreditsSetting as $id => $extcredit) {
        showsetting(
            Appbyme::lang('mobcent_operation_forum_extcredit_base') . ' ' .
            $extcredit['title'], 
            sprintf('forumextcreditbase_new[%d]', $id),
            (int)(isset($forumExtcreditBase[$id]) ? $forumExtcreditBase[$id] : 100),
            'text', '', 0,
            Appbyme::lang('mobcent_tips_extcredit_base'),
            '', '', true
        );   
    }

    showtablefooter();
    
    showsubmit('operation_submit', 'submit');
    showformfooter();
    showtagfooter('div');
} else {
    if (!empty($_POST['signextcreditbase_new'])) {
        Appbyme::setDzPluginCache('sign_extcredit_base', (int)$_POST['signextcreditbase_new']);
    }
    if (!empty($_POST['forumextcreditbase_new'])) {
        $bases = array();
        foreach ($_POST['forumextcreditbase_new'] as $id => $base) {
            $bases[$id] = (int)$base;
        }
        Appbyme::setDzPluginCache('forum_extcredit_base', $bases);
    }

    cpmsg(Appbyme::lang('mobcent_operation_edit_succeed'), $baseUrl, 'succeed');
}