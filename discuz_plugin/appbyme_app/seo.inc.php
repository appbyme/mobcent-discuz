<?php

/**
 * 应用 >> 安米手机客户端 >> SEO设置
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

if (!submitcheck('seo_submit')) {
    $formUrl = ltrim($baseUrl, 'action=');
    $setting = array();
    $seoConfigs = array('seotitle', 'seokeywords', 'seodescription');
    foreach ($seoConfigs as $key) {
        $config = Appbyme::getAppbymeConfig($key);
        $setting[$key] = unserialize($config['cvalue']);
    }

    showtagheader('div', 'seo_setting', true);

    showformheader($formUrl);

    showtableheader();
    showtitle('<em class="right">'.cplang('setting_seo_robots_output').'</em>'.cplang('setting_seo'));
    showtablerow('', array('class="vtop tips2" colspan="4" style="padding-left:20px;"'), array('<ul><li>'.cplang('setting_seo_seotitle_comment').'</li><li>'.cplang('setting_seo_seodescription_comment').'</li><li>'.cplang('setting_seo_seokeywords_comment').'</li></ul>'));

    showtitle(Appbyme::lang('appbyme_seo_title_download'));
    showtablerow('', array('width="80"', ''), array(
            cplang('setting_seo_seotitle'),
            '<input type="text" name="settingnew[seotitle][download]" value="'.$setting['seotitle']['download'].'" class="txt" style="width:280px;" />',
        )
    );
    showtablerow('', array('width="80"', ''), array(
            cplang('setting_seo_seokeywords'),
            '<input type="text" name="settingnew[seokeywords][download]" value="'.$setting['seokeywords']['download'].'" class="txt" style="width:280px;" />'
        )
    );
    showtablerow('', array('width="80"', ''), array(
            cplang('setting_seo_seodescription'),
            '<input type="text" name="settingnew[seodescription][download]" value="'.$setting['seodescription']['download'].'" class="txt" style="width:280px;" />',
        )
    );

    showtablefooter();
    // showtableheader();
    // showsetting('setting_seo_seohead', 'settingnew[seohead]', $setting['seohead'], 'textarea');
    // showtablefooter();

    showsubmit('seo_submit', 'submit');
    showformfooter();

    showtagfooter('div');
} else {
    foreach ($_POST['settingnew'] as $key => $setting) {
        Appbyme::setAppbymeConfig($key, serialize($setting));   
    }
    cpmsg(Appbyme::lang('mobcent_seo_edit_succeed'), $baseUrl, 'succeed');
}