<?php

/**
* 新版上传附件接口
*
* @author HanPengyu
*
* @param string $type 上传的类型（image|audio）
* @param string $module 上传图片的类型（帖子|相册）
* @param int $albumId 相册ID -1 为默认相册
*/

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class SendAttachmentExAction extends MobcentAction {
    private $uploadDir;
    private $maxSize = 2000000;
    private $allowTypes = array('image/png', 'image/jpeg', 'audio/mp3', 'audio/mpeg');
    private $allowExt = array('jpg', 'png', 'jpeg', 'mp3');

    public function run($type='image', $module='forum', $albumId=-1) {
        $res = $this->initWebApiArray();
        $albumId = $albumId == 0 ? -1 : $albumId;
        $res['body']['attachment'] = array();
        switch ($type) {
            case 'image':
                $this->uploadDir = $this->_getSavePath('image');
                $res = $this->_uploadMobcentImage($res, $module, $albumId);
                break;
            case 'audio':
                $this->uploadDir = $this->_getSavePath('audio', $module);
                $res = $this->_uploadMobcentAudio($res, $module);
                break;
            default:
                break;
        }
        echo WebUtils::outputWebApi($res, '', false);
    }

    /**
     * 上传图片
     */
    private function _uploadMobcentImage($res, $module, $albumId) {
        $savePath = $this->_getSavePath('image');
        return $this->_saveAttachment($res, 'image', $module, $albumId);
    }

    /**
     * 上传音频
     */
    private function _uploadMobcentAudio($res, $module) {
        $savePath = $this->_getSavePath('audio');
        return $this->_saveAttachment($res, 'audio', $module);
    }

    /**
     * 保存附件
     */
    private function _saveAttachment($res, $type, $module='', $albumId='') {
        global $_G;

        $allowFile = array();
        foreach ($_FILES['uploadFile']['name'] as $key => $file) {
            if ($this->_checkUploadFile($key)) {
                $allowFile[] = $key;
            }
        }

        if (!empty($allowFile)) {
            if ($type == 'image' && !WebUtils::getDzPluginAppbymeAppConfig('forum_allow_upload_with_plugin') && $module == 'forum') {
                foreach ( $allowFile as $allowValue) {
                    $res['body']['attachment'][] = $this->_uploadAttach($_G['uid'], $allowValue);
                }
                return $res;
            }

            if ($type == 'image' && $module == 'album') {

                if(!checkperm('allowupload') || !helper_access::check_module('album')) {
                    // 没有权限发相册,或者没有开启相册（没开启也可以$_G）
                    // return $this->makeErrorInfo($res, lang('message', 'no_privilege_postimage'));
                    return $this->makeErrorInfo($res, 'mobcent_no_privilege_postimage');
                }

                foreach ( $allowFile as $allowValue) {
                    $uploadInfo = $this->_uploadAlbum($allowValue, $albumId);
                    if (!empty($uploadInfo)) {
                        $res['body']['attachment'][] = $uploadInfo;
                    }
                }
                return $res;
            }

            if (in_array($module, array('forum', 'pm')) && in_array($type, array('image', 'audio'))) {
                foreach ($allowFile as $allowValue) {
                    $saveName = $this->_getSaveName($type, $this->uploadDir);
                    if (move_uploaded_file($_FILES['uploadFile']['tmp_name'][$allowValue], $saveName)) {
                        Yii::import('application.components.discuz.source.class.class_image', true);
                        $image = new Mobcent_Image;
                        if ($image->param['watermarkstatus']['forum'] > 0) {
                            $image->makeWatermark($saveName, '', 'forum');
                        }
                        $urlFileName = $this->_getUrlFileName($this->_getPathFileName($type), $saveName);
                        $type == 'image' && ImageUtils::getThumbImageEx($urlFileName, 10, false, false, true);
                        $res['body']['attachment'][] = array('id' => 0, 'urlName' => $urlFileName);
                    }
                }
            }
        }
        return $res;
    }

    /**
     * 检查要上传的文件
     * 
     * @param mixed $key 允许上传文件的编号（file=name[]）
     *
     * @return boolean
     */
    private function _checkUploadFile($key) {
        if ($_FILES['uploadFile']['error'][$key] > 0) {
            return false;
        }

        if ($_FILES['uploadFile']['size'][$key] > $this->maxSize || $_FILES['uploadFile']['size'][$key] == 0) {
            die('1');
            return false;
        }

        if (!in_array(strtolower($_FILES['uploadFile']['type'][$key]), $this->allowTypes)) {
            return false;
        }

        $ext = FileUtils::getFileExtension($_FILES['uploadFile']['name'][$key]);
        if (!in_array(strtolower($ext), $this->allowExt)) {
            return false;
        }

        return true;
    }

    /**
     * 获得保存附件的路径（存本地）
     *
     * @return string 路径
     */
    private function _getSavePath($type) {
        $path = '';
        $tempPath = $this->_getTempPath();
        switch ($type) {
            case 'audio':
                $path = UploadUtils::getUploadAudioBasePath($tempPath);
                break;
            case 'image':
                $path = UploadUtils::getUploadImageBasePath($tempPath);
                break;
            default: break;
        }
        return $path;
    }

    /**
     * 创建保存路径的方式
     */
    private function _getTempPath() {
        return (string)sprintf('%s/%s', date('Ym'), date('d'));
    }

    /**
     * 获取保存文件的名字（包括后缀）
     *
     * @return string 整个文件名 xxx.jpg/xxx.mp3
     */
    private function _getSaveName($type, $path) {
        $name = FileUtils::getRandomUniqueFileName($path);
        $ext = $type == 'image' ? 'jpg' : 'mp3';
        return $name.'.'.$ext;
    }

    /**
     * 获取附件路径 如：data/appbyme/upload/image/201412/02
     * 
     * @param mixed $type 附件的类型
     *
     * @return mixed Value.
     */
    private function _getPathFileName($type) {
        $path = '';
        $tempPath = $this->_getTempPath();
        $urlBasePath = '';
        switch ($type) {
            case 'audio': 
                $urlBasePath = UploadUtils::getUploadAudioBaseUrlPath($tempPath);
                break;
            case 'image': 
                $urlBasePath = UploadUtils::getUploadImageBaseUrlPath($tempPath);
                break;
            default: break;
        }
        $path = $urlBasePath.'/'.$tempPath;
        return $path;
    }

    private function _getUrlFileName($urlPath, $fileName) {
        return (string)sprintf('%s/%s/%s', $this->getController()->dzRootUrl, $urlPath, basename($fileName));
    }

    /**
     * 上传帖子图片（正常的附件目录）
     * 
     * @param mixed $uid        Description.
     * @param mixed $allowValue Description.
     *
     * @access private
     *
     * @return mixed Value.
     */
    private function _uploadAttach($uid, $allowValue) {
        $fileExtension = FileUtils::getFileExtension($_FILES['uploadFile']['name'][$allowValue], 'jpg');
        $type = 'forum';
        $extid = 0;
        $forcename = '';
        Yii::import('application.components.discuz.source.class.discuz.discuz_upload', true);
        $upload = new Mobcent_upload;
        $attach['extension'] = $fileExtension;
        $attach['attachdir'] = $upload->get_target_dir($type, $extid);
        $filename = $upload->get_target_filename($type, $extid, $forcename).'.'.$attach['extension'];
        $attach['attachment'] = $attach['attachdir'].$filename;
        $attach['target'] = getglobal('setting/attachdir').'./'.$type.'/'.$attach['attachment'];
        $savePath = getglobal('setting/attachdir').'./'.$type.'/'.$attach['attachdir'];

        if (!is_dir($savePath)) {
            mkdir($savePath, 0777, true);
        }

        $remote = $width = $thumb = 0;
        $res = array();
        $saveName = $savePath.$filename;
        if (move_uploaded_file($_FILES['uploadFile']['tmp_name'][$allowValue], $saveName)) {
                $aid = getattachnewaid($uid);
                $img_info = getimagesize($attach['target']);
                $size = filesize($attach['target']);
                $insert = array(
                    'aid' => $aid,
                    'dateline' => $_G['timestamp'],
                    'filename' => $filename,
                    'filesize' => $size,
                    'attachment' => $attach['attachment'],
                    'isimage' => 1,
                    'uid' => $uid,
                    'thumb' => $thumb,
                    'remote' => $remote,
                    'width' => $img_info[0],
                );
                C::t('forum_attachment_unused')->insert($insert);
                // 添加水印
                Yii::import('application.components.discuz.source.class.class_image', true);
                $image = new Mobcent_Image;
                if ($image->param['watermarkstatus']['forum'] > 0) {
                    $image->makeWatermark($attach['target'], '', 'forum');
                }
                $path_url = ImageUtils::getAttachUrl().'/'.$type.'/'.$attach['attachment'];
                ImageUtils::getThumbImageEx($path_url, 10, false, false, true);
                $res['id'] = $aid;
                $res['urlName'] = $path_url;
        }
        return $res;
    }

    /**
     * 上传相册图片
     * 
     * @param mixed $allowValue 允许上传图片编号(file=name[]).
     * @param int   $albumId 相册ID
     *
     * @return array 
     */
    private function _uploadAlbum($allowValue, $albumId) {
        global $_G;
        $_FILES["Filedata"] = array(
            'name' => $_FILES['uploadFile']['name'][$allowValue],
            'type' => $_FILES['uploadFile']['type'][$allowValue],
            'tmp_name' => $_FILES['uploadFile']['tmp_name'][$allowValue],
            'error' => $_FILES['uploadFile']['error'][$allowValue],
            'size' => $_FILES['uploadFile']['size'][$allowValue]
        );

        // /source/module/misc/misc_swfupload.php
        $res = array();
        if(helper_access::check_module('album')) {
            require_once libfile('function/spacecp');
            if($_FILES["Filedata"]['error']) {
                $file = lang('spacecp', 'file_is_too_big');
            } else {

                require_once libfile('function/home');
                $_FILES["Filedata"]['name'] = addslashes(diconv(urldecode($_FILES["Filedata"]['name']), 'UTF-8'));

                // 为了水印的问题来修改discuz的方法
                Yii::import('application.components.discuz.source.function.function_spacecp', true);
                $file = mobcent_pic_save($_FILES["Filedata"], $albumId, '', true, 0);

                if(!empty($file) && is_array($file)) {
                    $url = pic_get($file['filepath'], 'album', $file['thumb'], $file['remote']);
                    $bigimg = pic_get($file['filepath'], 'album', 0, $file['remote']);
                    // echo "{\"picid\":\"$file[picid]\", \"url\":\"$url\", \"bigimg\":\"$bigimg\"}";
                    // die;
                    $res['id'] = $file['picid'];
                    $res['urlName'] = $_G['setting']['ftp']['on'] == 1 ? $this->_processPath($bigimg) : $this->getController()->dzRootUrl.'/'.$bigimg;
                    
                    // ？？图片的描述：暂时albumPicDesc[]的方式传进来
                    // C::t('home_pic')->update_for_uid($_G['uid'], $file['picid'], array('title'=>'123321', 'albumid' => $albumId));
                    // if($albumId) {
                    //     album_update_pic($albumId);
                    // }
                }
                // echo WebUtils::u($file);
            }
        }
        return $res;

    }

    /**
     * 如果采用ftp上传相册，但是ftp设置错误，图片将上传到本地，对返回的图片路径进行处理
     */
    private function _processPath($path) {
        if (stripos($path, 'http://') !== false || stripos($path, 'ftp://') !== false) {
            return $path;
        } else {
            return $this->getController()->dzRootUrl.'/'.$path;
        }
    }

}
?>