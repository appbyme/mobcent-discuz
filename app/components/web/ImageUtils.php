<?php

/**
 * 图片相关工具类
 *
 * @author 谢建平 <xiejianping@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class ImageUtils {

    const DISCUZ_THUMB_SUFFIX = '.thumb.jpg';

    // 获取dz附件url地址
    public static function getAttachUrl($remote=false) {
        global $_G;
        $setting = $_G['setting'];
        $attachUrl = WebUtils::getHttpFileName($setting['attachurl']);
        $remote && $_G['setting']['ftp']['on'] == 1 && $attachUrl = WebUtils::getHttpFileName($setting['ftp']['attachurl']);
        return $attachUrl;
    }

	public static function getThumbImage($image, $timeout=15) {
        $inBackgroud = WebUtils::getDzPluginAppbymeAppConfig('image_thumb_make_period') > 0;
        $res = self::getThumbImageEx($image, $timeout, false, $inBackgroud);
        return $res['image'];
    }

    public static function getThumbImageEx($image, $timeout=15, $getImageInfo=false, $inBackgroud=false, $force=false) {
        $res = array('image' => $image, 'ratio' => '1');

        if (empty($image)) return $res;

        $config = self::_getThumbConfig();
        if (!$force && !$config['isThumb']) {
            return $res;
        }
        
        $thumbImage = $image;

        // 获取缩略图文件名
        $savePath = sprintf('%s/%s', MOBCENT_THUMB_PATH, self::_getThumbTempPath($image));
        $tempFileName = self::_getThumbTempFile($image);

        $smallFileName = $savePath . '/mobcentSmallPreview_' . $tempFileName;
        $bigFileName = $savePath . '/mobcentBigPreview_' . $tempFileName;
        
        if (file_exists($smallFileName) && file_exists($bigFileName)) {
            $res['image'] = self::_getThumbUrlFile($image, $tempFileName);
            if ($getImageInfo) {
                $imageInfo = ImageUtils::getImageInfo($smallFileName);
                $res['ratio'] = $imageInfo['ratio'];
            }
            return $res;
        }

        global $_G;
        $attachFile = '';
        // 是否用discuz生成的缩略图
        $allowDiscuzThumb = WebUtils::getDzPluginAppbymeAppConfig('image_thumb_allow_discuz');
        if ($allowDiscuzThumb === false || $allowDiscuzThumb > 0) {
            if ($_G['setting']['ftp']['on'] == 0) {
                $attachUrl = self::getAttachUrl();
                $attachUrl = str_replace($attachUrl, '', $image);
                $attachFile = $_G['setting']['attachdir'].$attachUrl;
                $attachThumbFile = $attachFile.self::DISCUZ_THUMB_SUFFIX;
                if (file_exists($attachThumbFile)) {
                    $res['image'] = $thumbImage . self::DISCUZ_THUMB_SUFFIX;
                    if ($getImageInfo) {
                        $imageInfo = ImageUtils::getImageInfo($attachThumbFile); 
                        $res['ratio'] = $imageInfo['ratio'];
                    }
                    return $res;
                }
            }
        }

        if ($inBackgroud) {
            CacheUtils::addThumbTaskList($image);
            return $res;
        }
            
        if (!is_dir($savePath)) {
            mkdir($savePath, 0777, true);
        }
        if (is_writable($savePath)) {
            // $timer = new CountTimer;

            // 先查看是否是本地附件的图片, 如果不是才去网络取图片数据
            $imageData = '';
            if ($attachFile != '' && file_exists($attachFile)) {
                $imageData = file_get_contents($attachFile);
            }
            if ($imageData == '') {
                $imageData = WebUtils::httpRequest($image, $timeout);
                if ($imageData == '') { return $res; }
            }
            
            $thumb = null;
            $zoomRes = true;
            require_once MOBCENT_APP_ROOT . '/components/discuz/source/class/class_image.php';

            if (!file_exists($smallFileName)) {
                if (file_put_contents($smallFileName, $imageData) == false) {
                    return $res;
                }
                $thumb = new Mobcent_Image;
                $zoomRes &= $thumb->makeThumb($smallFileName, '', $config['imageSmallLength']);
            }
            if (!file_exists($bigFileName)) {
                if (file_put_contents($bigFileName, $imageData) == false) {
                    return $res;
                }
                $thumb == null && $thumb = new Mobcent_Image;
                $zoomRes &= $thumb->makeThumb($bigFileName, '', $config['imageBigLength']);
            }
    
            if (file_exists($smallFileName) && file_exists($bigFileName) && $zoomRes) {
                $thumbImage = self::_getThumbUrlFile($image, $tempFileName);
                if ($getImageInfo) {
                    $imageInfo = ImageUtils::getImageInfo($smallFileName);    
                    $res['ratio'] = $imageInfo['ratio'];
                }
            } else {
                FileUtils::safeDeleteFile($smallFileName);
                FileUtils::safeDeleteFile($bigFileName);
            }
            
            // var_dump($timer->stop());
        }
        $res['image'] = $thumbImage;
        return $res;
    }

    private static function getImageInfo($image) {
        $info = array('ratio' => '1', 'width' => 0, 'height' => 0);
        $imageInfo = getimagesize($image);
        if (!empty($imageInfo)) {
            $info['width'] = $imageInfo[0];
            $info['height'] = $imageInfo[1];
            // $info['ratio'] = bcdiv($info['width'], $info['height'], 2);
            // $info['ratio'] = bcdiv($info['height'], $info['width'], 2);
            $info['ratio'] = (string)($info['height']/$info['width']);
        }
        return $info;
    }

    private static function _getThumbConfig() {
        $config = array('isThumb' => true, 'imageBigLength' => 600, 'imageSmallLength' => 380);
        $config['isThumb'] = ($isThumb = WebUtils::getDzPluginAppbymeAppConfig('image_isthumb')) > 0;
        ($bigLength = WebUtils::getDzPluginAppbymeAppConfig('image_thumb_big_length')) > 0 && $config['imageBigLength'] = $bigLength; 
        ($smallLength = WebUtils::getDzPluginAppbymeAppConfig('image_thumb_small_length')) > 0 && $config['imageSmallLength'] = $smallLength; 
        return $config;
    }

    private static function _getThumbTempPath($image) {
        $temp = md5($image);
        $tempArr = str_split($temp, strlen($temp)/4);
        return sprintf('%s/%s/%s', $tempArr[0]%30, $tempArr[1]%30, $tempArr[2]%30);
    }

    private static function _getThumbTempFile($image) {
        $tempFileName = md5($image);
        $fileExt = FileUtils::getFileExtension($image, 'jpg');
        strlen($fileExt) > 5 && $fileExt = 'jpg';
        $tempFileName .= '.' . $fileExt;
        return $tempFileName;
    }

    private static function _getThumbUrlFile($image, $thumb) {
        return sprintf('%s/%s/%s/%s_%s', 
            Yii::app()->getController()->dzRootUrl,
            MOBCENT_THUMB_URL_PATH,
            self::_getThumbTempPath($image),
            (isset($_GET['sdkVersion']) && $_GET['sdkVersion'] > '1.0.0') ? 'xgsize' : 'mobcentSmallPreview',
            $thumb
        );
    }
}