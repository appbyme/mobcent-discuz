<?php

/**
 * 安米插件工具类
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 * @license http://opensource.org/licenses/LGPL-3.0
 */

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

class Appbyme {
    
    const PLUGIN_ID = 'appbyme_app';
    const APPBYME_URL = 'http://appbyme.com';

    static $config;

    private static $_isInit = false;

    // const STATUS_SEND_FROM_APP_ANDROID = 25165824;
    // const STATUS_SEND_FROM_APP_APPLE = 20971520;

    /**
     * 检测安米插件是否安装
     */
    public static function isInstalled() {
        $installDir = realpath(dirname(__FILE__).'/../../../mobcent/');
        return $installDir && file_exists($installDir.'/install.lock');
    }

    public static function init() {
        if (!self::$_isInit) {
            self::checkInstalled();

            require_once DISCUZ_ROOT . '/mobcent/app/config/mobcent_version.php';
            loadcache(self::PLUGIN_ID);

            global $_G;
            self::$config = $_G['cache']['plugin'][self::PLUGIN_ID];
            
            self::$_isInit = true;
        }
    }
    
    protected static function checkInstalled() {
        if (!self::isInstalled()) {
            cpmsg(self::lang('mobcent_error_not_installed'), '', 'error');
        }
    }

    public static function lang($key, $params = array()) {
        return lang('plugin/'.self::PLUGIN_ID, $key, $params);
    }

    public static function t($string) {
        global $_G;
        if (($enc = strtoupper($_G['charset'])) !== 'UTF-8') {
            $string = iconv('UTF-8', $enc, $string);
        }
        return $string;
    }

    public static function emptyReturnLine($str, $replace='') {
        $str = str_replace("\r", $replace, $str);
        $str = str_replace("\n", $replace, $str);
        return $str;
    }
    
    public static function setErrors($open=1, $level=E_ALL) {
        ini_set('display_errors', $open);
        error_reporting($level);
    }

    public static function dumpSql() {
        // 开启 DISCUZ_DEBUG 才有效
        foreach (DB::$db->sqldebug as $debug) {
            var_dump($debug[0]);
            var_dump($debug[1]);
        }
    }

    public static function getVersion() {
        $version = array(
            'user_version' => MOBCENT_VERSION,
            'user_release' => MOBCENT_RELEASE,
            'mobcent_version' => MOBCENT_VERSION,
            'mobcent_release' => MOBCENT_RELEASE,
        );

        $url = 'http://www.appbyme.com/mobcentACA/file/predefined.html';
        $info = dfsockopen($url);
        if (!empty($info)) {
            $matches = array();
            preg_match_all('/<mobcent_(version|release)>(.*?)<\/mobcent_\1>/s', $info, $matches, PREG_SET_ORDER);
            if (!empty($matches)) {
                foreach ($matches as $matches) {
                    switch ($matches[1]) {
                        case 'version': 
                        case 'release':
                            $version['mobcent_'.$matches[1]] = $matches[2]; 
                            break;
                        default: break;
                    }
                }
            }
        }

        return $version;
    }

    /**
     * 执行定时任务
     */
    public static function runCron() {
        self::_runCronUpdateCache();
        self::_runCronMakeThumb();
    }

    private static function _runCronUpdateCache() {
        $updateTime = self::$config['cache_update_time'];
        $updateCachePeriod = self::$config['cache_update_period'];
        $lastUpdateTime = self::getDzPluginCache('cache_update_time');
        
        $needUpdate = false;
        // 自动定时更新
        $matches = array();
        preg_match('/^\[(\d{2}):(\d{2})]$/', $updateTime, $matches);
        if (!empty($matches) && $matches[1] < 23 && $matches[2] < 59) {
            $updateTime = mktime($matches[1], $matches[2], 0);
            if ($updateTime < time() && $updateTime > $lastUpdateTime) {
                $needUpdate = true;
            }
        }
        // 自动间隔更新
        if ($updateCachePeriod > 0 && time()-$lastUpdateTime > $updateCachePeriod) {
            $needUpdate = true;
        }

        $needUpdate && self::updateCache();
    }

    private static function _runCronMakeThumb() {
        $makeThumbPeriod = self::$config['image_thumb_make_period'];
        $thumbTaskLength = self::$config['image_thumb_task_length'];
        $lastMakeTime = self::getDzPluginCache('thumb_make_time');
        $thumbTaskList = self::getDzPluginCache('thumb_task_list');
        if (self::$config['image_isthumb'] == 1 && 
            !empty($thumbTaskList) &&
            $makeThumbPeriod > 0 &&
            time()-$lastMakeTime > $makeThumbPeriod) {
            self::makeThumb($thumbTaskLength);
        }
    }

    /**
     * 更新缓存
     * 
     * @param int $fid 版块id, 0则为全部版块.
     * @param int $gid 用户组id, 0则为全部用户组.
     *
     * @return boolean
     */
    public static function updateCache($fid = 0, $gid = 0) {
        self::setDzPluginCache('cache_update_time', time());
        self::httpRequestAppAPI('cache/update', array('fid' => $fid, 'gid' => $gid));
        return true;
    }

    /**
     * 清空缓存
     */
    public static function cleanCache($fid = 0, $gid = 0) {
        self::httpRequestAppAPI('cache/clean', array('fid' => $fid, 'gid' => $gid));
    }

    public static function makeThumb($count) {
        self::setDzPluginCache('thumb_make_time', time());
        self::httpRequestAppAPI('cache/makethumb', array('count' => $count));
    }

    public static function cleanThumb() {
        self::httpRequestAppAPI('cache/cleanthumb');
    }

    public static function createAppApiUrl($route, $params=array()) {
        global $_G;
        $urlParams = http_build_query(array_merge(array('r' => $route, 'sdkVersion' => '2.0.0', 'formhash' => FORMHASH), $params));
        return sprintf('%smobcent/app/web/index.php?%s', $_G['siteurl'], $urlParams);
    }

    public static function httpRequestAppAPI($route, $params=array()) {
        return mobcent_dfsockopen(self::createAppApiUrl($route, $params), array());
    }

    public static function getDzPluginCache($key = '') {
        global $_G;
        $cache = isset($_G['cache'][self::PLUGIN_ID]) ? $_G['cache'][self::PLUGIN_ID] : array();
        if ($key == '') {
            return $cache;
        } else {
            $key = 'dzsyscache_' . $key;
            return isset($cache[$key]) ? $cache[$key] : false;
        }
    }

    public static function setDzPluginCache($key, $data) {
        $key = 'dzsyscache_' . $key;

        loadcache(self::PLUGIN_ID, true);
        $cache = self::getDzPluginCache();
        empty($cache) && $cache = array();
        is_array($cache) && $cache = array_merge($cache, array($key => $data));
        savecache(self::PLUGIN_ID, $cache);
    }

    public static function getAppbymeConfig($key) {
        return DB::fetch_first('
            SELECT *
            FROM %t
            WHERE ckey = %s
            ',
            array('appbyme_config', $key)
        );
    }

    public static function setAppbymeConfig($key, $data) {
        $tempData = DB::fetch_first("SELECT * FROM %t WHERE ckey=%s", array('appbyme_config', $key));
        if (empty($tempData)) {
            return DB::insert('appbyme_config', array('ckey' => $key, 'cvalue' => $data));
        } else {
            return DB::update('appbyme_config', array('cvalue' => $data), array('ckey' => $key));
        }
    }
    
    public static function getSeoSetting($page, $data = array(), $defset = array()) {
        global $_G;
        $searchs = array('{bbname}');
        $replaces = array($_G['setting']['bbname']);

        $setting = array();
        $seoConfigs = array('seotitle', 'seokeywords', 'seodescription');
        foreach ($seoConfigs as $key) {
            $config = Appbyme::getAppbymeConfig($key);
            $setting[$key] = unserialize($config['cvalue']);
        }
        $_G['setting'] = array_merge($_G['setting'], $setting);

        $seotitle = $seodescription = $seokeywords = '';
        $titletext = $defset['seotitle'] ? $defset['seotitle'] : $_G['setting']['seotitle'][$page];
        $descriptiontext = $defset['seodescription'] ? $defset['seodescription'] : $_G['setting']['seodescription'][$page];
        $keywordstext = $defset['seokeywords'] ? $defset['seokeywords'] : $_G['setting']['seokeywords'][$page];
        preg_match_all("/\{([a-z0-9_-]+?)\}/", $titletext.$descriptiontext.$keywordstext, $pageparams);
        if($pageparams) {
            foreach($pageparams[1] as $var) {
                $searchs[] = '{'.$var.'}';
                if($var == 'page') {
                    $data['page'] = $data['page'] > 1 ? lang('core', 'page', array('page' => $data['page'])) : '';
                }
                $replaces[] = $data[$var] ? strip_tags($data[$var]) : '';
            }
            if($titletext) {
                $seotitle = helper_seo::strreplace_strip_split($searchs, $replaces, $titletext);
            }
            if($descriptiontext && (isset($_G['makehtml']) || CURSCRIPT == 'forum' || IS_ROBOT || $_G['adminid'] == 1)) {
                $seodescription = helper_seo::strreplace_strip_split($searchs, $replaces, $descriptiontext);
            }
            if($keywordstext && (isset($_G['makehtml']) || CURSCRIPT == 'forum' || IS_ROBOT || $_G['adminid'] == 1)) {
                $seokeywords = helper_seo::strreplace_strip_split($searchs, $replaces, $keywordstext);
            }
        }
        return array($seotitle, $seodescription, $seokeywords);
    }

    public static function getPostSign($key, $type, $status) {
        global $_G;
        $defDownUrl = $_G['siteurl'] . '/mobcent/download/down.php';
        $defPostSign = array(
            'forumdisplay_thread' => array(
                'android' => '<img src="mobcent/app/web/images/mobile/mobile-attach-1.png" alt="android" align="absmiddle">',
                'apple' => '<img src="mobcent/app/web/images/mobile/mobile-attach-1.png" alt="apple" align="absmiddle">',                
            ),
            'viewthread_post' => array(
                'android' => sprintf('<a href="%s" target="_blank">%s</a>', $defDownUrl, self::t('来自安卓手机客户端')),
                'apple' => sprintf('<a href="%s" target="_blank">%s</a>', $defDownUrl, self::t('来自苹果手机客户端')),                
            ),
            'viewthread_avatar' => array(
                'android' => '<div><img style="margin: -145px 0 0 100px;" src="mobcent/app/web/images/mobile/mobile-android.png" /></div>',
                'apple' => '<div><img style="margin: -145px 0 0 100px;" src="mobcent/app/web/images/mobile/mobile-ios.png" /></div>',
            )
        );
        $postSigns = $defPostSign[$type];
        $postSignText = self::$config[$key];
        $postSignText = self::emptyReturnLine($postSignText);
        $matches = array();
        preg_match_all('/\[title_(all|android|apple)\](.*?)\[\/title_\1\]/', $postSignText, $matches, PREG_SET_ORDER);
        foreach ($matches as $matche) {
            if ($matche[1] == 'all') {
                $postSigns['android'] = $postSigns['apple'] = $matche[2];
                break;
            } else if ($matche[1] == 'android' || $matche[1] == 'apple') {
                $postSigns[$matche[1]] = $matche[2];
            }
        }

        $postSign = '';
        // if ($status & self::STATUS_SEND_FROM_APP_ANDROID) {
        if (getstatus($status, 16)) {
            $postSign = $postSigns['android'];
        // } else if ($status & self::STATUS_SEND_FROM_APP_APPLE) {
        } else if (getstatus($status, 15)) {
            $postSign = $postSigns['apple'];
        } else {
            $postSign = '';
        }

        return $postSign;
    }
}

function mobcent_dfsockopen($url, $post, $encodeType = 'URLENCODE', $files = array(), 
                            $timeout = 15, $dataLen = 0) {
    return _mobcent_dfsockopen(
        $url, $dataLen, $post, '', false, '', $timeout, true, $encodeType, true, 0, $files,
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36'
    );
}

function _mobcent_dfsockopen($url, $limit = 0, $post = '', $cookie = '', 
                            $bysocket = FALSE, $ip = '', $timeout = 15, 
                            $block = TRUE, $encodetype  = 'URLENCODE', 
                            $allowcurl = TRUE, $position = 0, $files = array(), 
                            $userAgent = '') {
    $return = '';
    $matches = parse_url($url);
    $scheme = $matches['scheme'];
    $host = $matches['host'];
    $path = $matches['path'] ? $matches['path'].($matches['query'] ? '?'.$matches['query'] : '') : '/';
    $port = !empty($matches['port']) ? $matches['port'] : ($scheme == 'http' ? '80' : '');
    $boundary = $encodetype == 'URLENCODE' ? '' : random(40);
    $userAgent == '' && $userAgent = $_SERVER['HTTP_USER_AGENT'];

    if($post) {
        if(!is_array($post)) {
            parse_str($post, $post);
        }
        _mobcent_format_postkey($post, $postnew);
        $post = $postnew;
    }

    if(function_exists('curl_init') && function_exists('curl_exec') && $allowcurl) {
        $ch = curl_init();
        $httpheader = array();
        if($ip) {
            $httpheader[] = "Host: ".$host;
        }
        if($httpheader) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
        }
        curl_setopt($ch, CURLOPT_URL, $scheme.'://'.($ip ? $ip : $host).($port ? ':'.$port : '').$path);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        if($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            if($encodetype == 'URLENCODE') {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            } else {
                foreach($post as $k => $v) {
                    if(isset($files[$k])) {
                        $post[$k] = '@'.$files[$k];
                    }
                }
                foreach($files as $k => $file) {
                    if(!isset($post[$k]) && file_exists($file)) {
                        $post[$k] = '@'.$file;
                    }
                }
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            }
        }
        if($cookie) {
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $data = curl_exec($ch);
        $status = curl_getinfo($ch);
        $errno = curl_errno($ch);
        curl_close($ch);
        if($errno || $status['http_code'] != 200) {
            return '';
        } else {
            $GLOBALS['filesockheader'] = substr($data, 0, $status['header_size']);
            $data = substr($data, $status['header_size']);
            return !$limit ? $data : substr($data, 0, $limit);
        }
    }

    if($post) {
        if($encodetype == 'URLENCODE') {
            $data = http_build_query($post);
        } else {
            $data = '';
            foreach($post as $k => $v) {
                $data .= "--$boundary\r\n";
                $data .= 'Content-Disposition: form-data; name="'.$k.'"'.(isset($files[$k]) ? '; filename="'.basename($files[$k]).'"; Content-Type: application/octet-stream' : '')."\r\n\r\n";
                $data .= $v."\r\n";
            }
            foreach($files as $k => $file) {
                if(!isset($post[$k]) && file_exists($file)) {
                    if($fp = @fopen($file, 'r')) {
                        $v = fread($fp, filesize($file));
                        fclose($fp);
                        $data .= "--$boundary\r\n";
                        $data .= 'Content-Disposition: form-data; name="'.$k.'"; filename="'.basename($file).'"; Content-Type: application/octet-stream'."\r\n\r\n";
                        $data .= $v."\r\n";
                    }
                }
            }
            $data .= "--$boundary\r\n";
        }
        $out = "POST $path HTTP/1.0\r\n";
        $header = "Accept: */*\r\n";
        $header .= "Accept-Language: zh-cn\r\n";
        $header .= $encodetype == 'URLENCODE' ? "Content-Type: application/x-www-form-urlencoded\r\n" : "Content-Type: multipart/form-data; boundary=$boundary\r\n";
        $header .= 'Content-Length: '.strlen($data)."\r\n";
        $header .= "User-Agent: $userAgent\r\n";
        $header .= "Host: $host:$port\r\n";
        $header .= "Connection: Close\r\n";
        $header .= "Cache-Control: no-cache\r\n";
        $header .= "Cookie: $cookie\r\n\r\n";
        $out .= $header;
        $out .= $data;
    } else {
        $out = "GET $path HTTP/1.0\r\n";
        $header = "Accept: */*\r\n";
        $header .= "Accept-Language: zh-cn\r\n";
        $header .= "User-Agent: $userAgent\r\n";
        $header .= "Host: $host:$port\r\n";
        $header .= "Connection: Close\r\n";
        $header .= "Cookie: $cookie\r\n\r\n";
        $out .= $header;
    }

    $fpflag = 0;
    if(!$fp = @fsocketopen(($ip ? $ip : $host), $port, $errno, $errstr, $timeout)) {
        $context = array(
            'http' => array(
                'method' => $post ? 'POST' : 'GET',
                'header' => $header,
                'content' => $post,
                'timeout' => $timeout,
            ),
        );
        $context = stream_context_create($context);
        $fp = @fopen($scheme.'://'.($ip ? $ip : $host).':'.$port.$path, 'b', false, $context);
        $fpflag = 1;
    }

    if(!$fp) {
        return '';
    } else {
        stream_set_blocking($fp, $block);
        stream_set_timeout($fp, $timeout);
        @fwrite($fp, $out);
        $status = stream_get_meta_data($fp);
        if(!$status['timed_out']) {
            while (!feof($fp) && !$fpflag) {
                $headers = '';
                $header = @fgets($fp);
                $headers .= $header;
                if($header && ($header == "\r\n" ||  $header == "\n")) {
                    break;
                }
            }
            $GLOBALS['filesockheader'] = $headers;

            if($position) {
                for($i=0; $i<$position; $i++) {
                    $char = fgetc($fp);
                    if($char == "\n" && $oldchar != "\r") {
                        $i++;
                    }
                    $oldchar = $char;
                }
            }

            if($limit) {
                $return = stream_get_contents($fp, $limit);
            } else {
                $return = stream_get_contents($fp);
            }
        }
        @fclose($fp);
        return $return;
    }
}

function _mobcent_format_postkey($post, &$result, $key = '') {
    foreach($post as $k => $v) {
        $_k = $key ? $key.'['.$k.']' : $k;
        if(is_array($v)) {
            _mobcent_format_postkey($v, $result, $_k);
        } else {
            $result[$_k] = $v;
        }
    }
}