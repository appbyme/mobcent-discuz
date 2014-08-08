<?php

/**
 * 网络请求接口
 *
 * 在 DISCUZ_ROOT/source/function_filesock.php 基础上做了改动
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
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

?>