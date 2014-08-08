<?php

/**
 * 缓存相关工具类
 *
 * @author 谢建平 <xiejianping@mobcent.com>
 * @author HanPengyu 2012-7
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class CacheUtils {

    public static function getTopicListKey($params) {
        return self::_getCacheKey('topiclist', $params);
    }

    public static function getForumListKey($params) {
        return self::_getCacheKey('forumlist', $params);
    }

    // @author HanPengyu
    public static function getNewsListKey($params) {
        return self::_getCacheKey('newslist', $params);
    }

    public static function addThumbTaskList($image) {
        $thumbTaskList = CacheUtils::getDzPluginCache('thumb_task_list', true);
        $maxCount = WebUtils::getDzPluginAppbymeAppConfig('image_thumb_task_max_length');
        $maxCount === false && $maxCount = 20;
        if ($maxCount == 0 || $maxCount > count($thumbTaskList)) {
            $thumbTaskList[md5($image)] = $image;
        }
        CacheUtils::setDzPluginCache('thumb_task_list', $thumbTaskList, false);
    }

    public static function getDzPluginCache($key = '', $force = false) {
        $force && loadcache(MOBCENT_DZ_PLUGIN_ID, true);
        global $_G;
        $cache = isset($_G['cache'][MOBCENT_DZ_PLUGIN_ID]) ? $_G['cache'][MOBCENT_DZ_PLUGIN_ID] : array();
        if ($key == '') {
            return $cache;
        } else {
            $key = 'dzsyscache_' . $key;
            return isset($cache[$key]) ? $cache[$key] : false;
        }
    }

    public static function setDzPluginCache($key, $data, $force = true) {
        $key = 'dzsyscache_' . $key;
        $cache = self::getDzPluginCache('', $force);
        empty($cache) && $cache = array();
        is_array($cache) && $cache = array_merge($cache, array($key => $data));
        savecache(MOBCENT_DZ_PLUGIN_ID, $cache);
    }

    private static function _getCacheKey($key, $params) {
        array_unshift($params, $key);
        return implode('_', $params);
    }
}