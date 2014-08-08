<?php

/**
 * 应用 >> 安米手机客户端 >> 论坛管理
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

if (!submitcheck('forum_submit')) {
    $formUrl = ltrim($baseUrl, 'action=');
    
    showtagheader('div', 'forum_manage', true);
    showformheader($formUrl);

    showtagheader('div', 'forum_stype', true);
    showtableheader(Appbyme::lang('mobcent_forum_style_setting'));
    
    $groupList = DB::fetch_all('
        SELECT *
        FROM %t
        WHERE type=%s AND status=%d
        ORDER BY displayorder ASC
        ',
        array('forum_forum', 'group', 1)
    );
    $forumColumnStyle = Appbyme::getDzPluginCache('forum_column_style');
    foreach ($groupList as $group) {
        echo sprintf('
            <tr>
                <td class="td27">%s</td>
                <td><input type="radio" name="forumcolumnstyle_new[%d]" value="2" %s />%s</td>
                <td><input type="radio" name="forumcolumnstyle_new[%d]" value="1" %s />%s</td>
            </tr>
            ',
            $group['name'], $group['fid'],
            !isset($forumColumnStyle[$group['fid']]) ? 'checked' : ($forumColumnStyle[$group['fid']] == '2'? 'checked' : ''),
            Appbyme::lang('mobcent_forum_column_style_col2'), $group['fid'],
            isset($forumColumnStyle[$group['fid']]) && $forumColumnStyle[$group['fid']] == '1'? 'checked' : '',
            Appbyme::lang('mobcent_forum_column_style_col1')
        );
        // showsetting(
        //     $group['name'], sprintf('group_new[%d]', $group['fid']),
        //     1, 'radio', '', 0, '', '', '', true
        // );
    }
    showtablefooter();
    showtagfooter('div');
    
    showsubmit('forum_submit', 'submit');
    showformfooter();
    showtagfooter('div');
} else {
    if (!empty($_POST['forumcolumnstyle_new'])) {
        Appbyme::setDzPluginCache('forum_column_style', $_POST['forumcolumnstyle_new']);
    }

    cpmsg(Appbyme::lang('mobcent_forum_edit_succeed'), $baseUrl, 'succeed');
}