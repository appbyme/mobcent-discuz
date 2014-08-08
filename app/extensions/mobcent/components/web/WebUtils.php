<?php

/**
 * 网络工具类
 *
 * @author  谢建平 <jianping_xie@aliyun.com>  
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class WebUtils {

    const USER_AGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36';

    public static function initWebApiArray() {
        return array(
            'head' => array(
                'errCode' => '00000000',
                'errInfo' => self::t('调用成功,没有任何错误'),
                'version' => MOBCENT_VERSION,
                'alert' => 0,
            ),
            'body' => array(
                'externInfo' => null,
            ),
        );
    }

    public static function initWebApiArray_oldVersion() {
        $res = WebUtils::initWebApiArray();
        $res = array_merge(array('rs' => 1, 'errcode' => ''), $res); 
        return $res;
    }

    public static function makeErrorInfo($res, $message, $params=array()) {
        $errInfo = explode(':', Yii::t('mobcent', $message, $params), 2);
        if (count($errInfo) == 1) {
            $errInfo[1] = $errInfo[0];
            $errInfo[0] = isset($params['noError']) && $params['noError'] > 0 ? MOBCENT_ERROR_NONE : MOBCENT_ERROR_DEFAULT;
        }
        $res['head']['errCode'] = !empty($errInfo[0]) ? $errInfo[0] : ''; 
        $res['head']['errInfo'] = !empty($errInfo[1]) ? WebUtils::emptyHtml($errInfo[1]) : '';
        $res['head']['alert'] = isset($params['alert']) ? (int)$params['alert'] : 1;

        return $res;
    }

    public static function checkError($res) {
        return $res['head']['errCode'] !== MOBCENT_ERROR_NONE;
    }

    public static function makeErrorInfo_oldVersion($res, $message, $params=array()) {
        $res = WebUtils::makeErrorInfo($res, $message, $params);
        $res['rs'] = isset($params['noError']) && $params['noError'] > 0 ? 1 : 0;
        $res['errcode'] = $res['head']['errInfo'];
        return $res;
    }

    public static function endAppWithErrorInfo($res, $message, $params=array()) {
        $tmpRes = WebUtils::initWebApiArray_oldVersion();
        $tmpRes = WebUtils::makeErrorInfo_oldVersion($tmpRes, $message, $params);
        $tmpRes = array_merge($tmpRes, $res);
        WebUtils::outputWebApi($tmpRes);
    }

    public static function outputWebApi($res, $charset='', $exit=true) {
        $res = WebUtils::jsonEncode($res, $charset);
        $res = html_entity_decode($res, ENT_NOQUOTES|ENT_HTML401, 'utf-8');
        $res = (string)str_replace('&quot;', '\\"', $res);
        if (!$exit) {
            return $res;
        } else {
            echo $res;
            Yii::app()->end();
        }
    }

    public static function t($string, $charset='') {
        $charset == '' && $charset = Yii::app()->charset;
        if (($enc = strtoupper($charset)) !== 'UTF-8') {
            $string = iconv('UTF-8', $enc, $string);
        }
        return $string;
    }

    public static function u($string, $charset='') {
        $charset == '' && $charset = Yii::app()->charset;
        if (($enc = strtoupper($charset)) !== 'UTF-8') {
            $string = iconv($enc, 'UTF-8', $string);
        }
        return $string;
    }

    public static function getWebApiArrayWithPage($res, $page, $pageSize, $count) {
        $res['body']['hasNext'] = ($count > $page * $pageSize) && $page > 0 ? 1 : 0; 
        $res['body']['count'] = (int)$count;
        return $res;
    }

    /**
     * 返回兼容老版本的分页格式
     */
    public static function getWebApiArrayWithPage_oldVersion($page, $pageSize, $count, $res=array()) {
        $res['page'] = (int)$page;
        $res['has_next'] = $count > $page * $pageSize ? 1 : 0; 
        $res['total_num'] = (int)$count;

        return $res;
    }

    public static function createUrl_oldVersion($route, $params=array()) {
        $params = array_merge(array(
                'sdkVersion' => MOBCENT_VERSION,
                'accessToken' => isset($_GET['accessToken']) ? $_GET['accessToken'] : '',    
                'accessSecret' => isset($_GET['accessSecret']) ? $_GET['accessSecret'] : '',    
            ), 
            $params
        );
        return Yii::app()->createAbsoluteUrl($route, $params);
    }

    public static function jsonEncode($var, $charset='') {
        $oldCharset = Yii::app()->charset;
        if ($charset != '') {
            Yii::app()->charset = $charset;
        }
        
        $res = CJSON::encode($var);

        Yii::app()->charset = $oldCharset;

        return $res;
    }

    public static function jsonDecode($str, $useArray=true) {
        return CJSON::decode($str, $useArray);
    }

    public static function subString($str, $start, $length=100) {
        return mb_substr($str, $start, $length, Yii::app()->charset);
    }

    public static function replaceLineMark($str) {
        $str = str_replace("\r", '\\r', $str);
        $str = str_replace("\n", '\\n', $str);
        return $str;
    }

    public static function emptyReturnLine($str, $replace='') {
        $str = str_replace("\r", $replace, $str);
        $str = str_replace("\n", $replace, $str);
        return $str;
    }

    public static function emptyHtml($str, $charset='', $transBr=false) {
        // $charset != '' or $charset = Yii::app()->charset;
        if ($transBr) {
            $str = str_replace('<br>', "\r\n", $str);
            $str = str_replace('<br />', "\r\n", $str);
        }
        $str = preg_replace('/<.*?>/', '', $str);
        $str = str_replace('&nbsp;', ' ', $str);
        return $str;
    }
        
    public static function parseXmlToArray($xmlString) {
        $res = array();
        if (($xml = simplexml_load_string($xmlString)) !== false) {
            $res = self::_transSimpleXMLElementToArray($xml);
        }
        return $res;
    }

    /**
     * 把相对url转换成绝对地址
     * 
     * @param string $url
     * @return string
     */
    public static function getHttpFileName($url) {
        strpos($url, 'www.') === 0 && $url = 'http://' . $url;
        return strpos($url, 'http') === 0 || $url == '' ? $url : Yii::app()->getController()->dzRootUrl . '/' . $url;
    }

    public static function getResByCurlWithPost($url, $postData) {
        $res = false;
        if (($ch = curl_init()) !== false) {
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $postData,
                CURLOPT_USERAGENT => self::USER_AGENT,
            ));
            $res = curl_exec($ch);
            curl_close($ch);
        }
        return $res;
    }

    public static function httpRequest($url, $timeout=15, $postData=array()) {
        return self::httpRequestByDiscuzApi($url, $postData, 'URLENCODE', array(), $timeout);
        // if (function_exists('curl_init')) {
        //     return self::getContentByCurl($url, $timeout);
        // } else {
        //     return self::getContentByFileGetContents($url, $timeout);
        // }
    }

    /**
     * 网络请求接口
     * 
     * @param string $url url 地址
     * @param array $postData post的数据, $fileData不为空时,$postData填入$fileData相对应key的文件内容
     * @param string $encodeType url编码, $fileData不为空时请改为''
     * @param int $timeout 超时时间, 0为无限制
     *
     * @return string 
     */
    public static function httpRequestByDiscuzApi($url, $postData=array(), 
                                                  $encodeType='URLENCODE', 
                                                  $fileData=array(), $timeout=15) {
        Mobcent::import(MOBCENT_APP_ROOT.'/components/discuz/source/function/function_filesock.php');
        return mobcent_dfsockopen($url, $postData, $encodeType, $fileData, $timeout);
    }

    public static function httpRequestAppAPI($route, $params=array(), $timeout=15) {
        $url = WebUtils::createUrl_oldVersion($route, $params);
        return WebUtils::httpRequest($url, $timeout);
    }

    public static function getContentByFileGetContents($url, $timeout=15) {
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'GET',
                'user_agent' => self::USER_AGENT,
                'follow_location' => 1,
                'timeout' => $timeout,
            ),
        ));
        return file_get_contents($url, false, $context);    
    }

    public static function getContentByCurl($url, $timeout=15) {
        $res = false;
        if (($ch = curl_init()) !== false) {
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                // CURLOPT_HEADER => false,
                CURLOPT_TIMEOUT => $timeout,
                CURLOPT_FRESH_CONNECT => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_USERAGENT => self::USER_AGENT,
            ));
            $res = curl_exec($ch);
            curl_close($ch);
        }
        return $res;
    }

    public static function getDzPluginAppbymeAppConfig($key = '') {
        global $_G;

        $cacheConfig = $_G['cache'][MOBCENT_DZ_PLUGIN_ID];
        $config = isset($_G['cache']['plugin'][MOBCENT_DZ_PLUGIN_ID]) ? $_G['cache']['plugin'][MOBCENT_DZ_PLUGIN_ID] : array();
        is_array($config) && is_array($cacheConfig) && $config = array_merge($cacheConfig, $config);
        
        if ($key == '') {
            return $config;
        } else {
            return isset($config[$key]) ? $config[$key] : false;
        }
    }

    public static function getMobcentConfig($key = '') {
        return $key == '' ? Yii::app()->params['mobcent'] : Yii::app()->params['mobcent'][$key];
    }

    public static function getMobcentPhizMaps() {
        $phizMaps = array();
        foreach (self::getMobcentConfig('phiz') as $key => $value) {
            $phizMaps[WebUtils::t($key)] = $value;
        }
        return $phizMaps;
    }

    public static function transMobcentPhiz($string, $prefixTag='[img]', $suffixTag='[/img]') {
        global $tempPhizs;
        $tempPhizs = array(WebUtils::getMobcentPhizMaps(), $prefixTag, $suffixTag);
        $string = preg_replace_callback(
            '/\[.*?\]/',
            create_function('$matches', '
                global $tempPhizs;
                list($phizMaps, $prefixTag, $suffixTag) = $tempPhizs;
                $phiz = $matches[0];
                if (!empty($phizMaps[$phiz])) {
                    $phiz = $prefixTag.WebUtils::getHttpFileName("mobcent/app/data/phiz/default/".$phizMaps[$phiz]).$suffixTag;
                }
                return $phiz;
            '), 
            $string
        );
        return $string;
    }

    private static function _transSimpleXMLElementToArray($element) {
        if ($element instanceof SimpleXMLElement) {
            $arr = (array)$element;
            foreach ($arr as $key => $value) {
                $arr[$key] = self::_transSimpleXMLElementToArray($value);
            }
            return $arr;
        } else {
            return $element;
        }
    }
}