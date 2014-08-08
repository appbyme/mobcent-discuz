<?php

/**
 * 应用 >> 安米手机客户端 >> 更新缓存
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 * @license http://opensource.org/licenses/LGPL-3.0
 */

if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}

set_time_limit(0);

require_once dirname(__FILE__) . '/appbyme.class.php';
Appbyme::init();

$baseUrl = rawurldecode(cpurl());
$step = max(1, intval($_GET['step']));
$cleanImage = true;

showsubmenusteps('nav_updatecache', array(
    array('nav_updatecache_confirm', $step == 1),
    array('nav_updatecache_verify', $step == 2),
    array('nav_updatecache_completed', $step == 3)
));

showtips(Appbyme::lang('mobcent_tips_updatecache'));

switch ($step) {
    case 1: 
        $thumbTaskList = Appbyme::getDzPluginCache('thumb_task_list');
        $thumbTaskList === false && $thumbTaskList = array();
        $thumbTaskCount = (int)count($thumbTaskList);
        cpmsg(sprintf('
            <input type="checkbox" name="type[]" value="cleandata" id="clean_datacache" class="checkbox" checked />
            <label for="clean_datacache">%s</label>
            <input type="checkbox" name="type[]" value="updatedata" id="update_datacache" class="checkbox" checked />
            <label for="update_datacache">%s</label>
            <input type="checkbox" name="type[]" value="cleanthumb" id="clean_thumbcache" class="checkbox" />
            <label for="clean_thumbcache">%s</label><br />
            %s
            <input type="text" name="thumb_task_length" value="0" class="text" />
            %s
            ',
            Appbyme::lang('mobcent_clean_datacache'),
            Appbyme::lang('mobcent_update_datacache'),
            Appbyme::lang('mobcent_clean_thumbcache'),
            Appbyme::lang('mobcent_thumb_task_length_setting'),
            Appbyme::lang('mobcent_thumb_task_length').' '.$thumbTaskCount
            ), 
            "{$baseUrl}&step=2", 'form'
        );
        break;
    case 2: 
        $type = implode('_', (array)$_GET['type']);
        cpmsg('tools_updatecache_waiting', 
            sprintf("{$baseUrl}&step=3&type=%s&thumb_task_length=%d", 
                $type, $_GET['thumb_task_length']
            ), 
            'loading'
        ); 
        break;
    case 3:
        $thumbTaskCount = (int)$_GET['thumb_task_length'];
        $type = explode('_', $_GET['type']);
        
        in_array('cleandata', $type) && Appbyme::cleanCache();
        in_array('updatedata', $type) && Appbyme::updateCache();
        in_array('cleanthumb', $type) && Appbyme::cleanThumb();
        $thumbTaskCount > 0 && Appbyme::makeThumb($thumbTaskCount);

        cpmsg('update_cache_succeed', '', 'succeed'); 
        break;
    default: cpmsg('step error', '', 'error'); break;
}