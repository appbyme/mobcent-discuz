<?php

/**
 * 上传附件接口
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class SendAttachmentAction extends CAction {

    public function run($attachment) {
        $res = WebUtils::initWebApiArray_oldVersion();

        $uid = $this->getController()->uid;

        // $attachment ="{'head': {'errCode': 0, 'errInfo': ''}, 'body': {'attachment': {'name': 'test', 'isPost': 1, 'data': 'ss', 'type': 'image', 'module':'forum'}, 'externInfo': {}}}";
        
        $attachment = rawurldecode($attachment);
        $attachment = WebUtils::jsonDecode($attachment);
        $attachment = isset($attachment['body']['attachment']) ? $attachment['body']['attachment'] : array();
        !isset($attachment['module']) && $attachment['module'] = 'forum';
        
        $resAttachment = false;
        if (!empty($attachment)) {
            if ($attachment['isPost'] == 1) {
                if (($data = file_get_contents('php://input')) === false)
                    $attachment['data'] = $GLOBALS['HTTP_RAW_POST_DATA'];
                else
                    $attachment['data'] = $data;
            }
            // $attachment['data'] = WebUtils::httpRequest('http://bbs.appbyme.com/static/image/common/logo.png');// test

            $resAttachment = $this->_saveAttachment($uid, $attachment);
        }

        if ($resAttachment === false) {
            $attachmentTypes = array('audio' => WebUtils::t('语音'), 'image' => WebUtils::t('图片'));
            $res = WebUtils::makeErrorInfo_oldVersion($res, 'UPLOAD_ATTACHMENT_ERROR', array('{attachment}' => $attachmentTypes[$attachment['type']]));
        } else {
            $res['body']['attachment'] = $resAttachment;
        }
        
        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _saveAttachment($uid, $attachment) {
        $res = false;

        switch ($attachment['type']) {
            case 'audio': $res = $this->_uploadMobcentAudio($res, $attachment); break;
            case 'image':
                if (!WebUtils::getDzPluginAppbymeAppConfig('forum_allow_upload_with_plugin') && 
                    $attachment['module'] == 'forum') {
                    $res = $this->_uploadAttach($uid, $attachment);
                } else {
                    $res = $this->_uploadMobcentImage($res, $attachment);
                }
                ImageUtils::getThumbImageEx($res['urlName'], 10, false, false, true);
                break;
            default:
                break;
        }

        return $res;
    }

    private function _uploadMobcentAudio($res, $attachment) {
        $savePath = $this->_getSavePath('audio');
        if (!empty($savePath)) {
            $urlFileName = $this->_getPathFileName('audio', $attachment['name']);
            $fileName = $savePath.'/'.$urlFileName['file'];
            if (FileUtils::saveFile($fileName, $attachment['data']) != false) {
                // amr 转 mp3
                $ffmpegCommand = Yii::app()->params['mobcent']['forum']['ffmpegCommand'];
                if (FileUtils::getFileExtension($fileName, 'mp3') == 'amr' && !empty($ffmpegCommand)) {
                    $tempFileName = $fileName;
                    $fileName .= '.mobcent.mp3';
                    $ffmpegCommand .= " -i {$tempFileName} {$fileName}";
                    SystemUtils::execInBackground($ffmpegCommand);
                }
                
                $res['urlName'] = $this->_getUrlFileName($urlFileName['path'], basename($fileName));
                $res['id'] = 0;
            }
        }
        return $res;
    }

    private function _uploadMobcentImage($res, $attachment) {
        $savePath = $this->_getSavePath('image');
        if (!empty($savePath)) {
            $urlFileName = $this->_getPathFileName('image', $attachment['name']);
            $fileName = $savePath.'/'.$urlFileName['file'];
            if (FileUtils::saveFile($fileName, $attachment['data']) != false) {
                // 添加水印
                Yii::import('application.components.discuz.source.class.class_image', true);
                $image = new Mobcent_Image;
                if ($image->param['watermarkstatus']['forum'] > 0) {
                    $image->makeWatermark($fileName, '', 'forum');
                }
        
                $res['urlName'] = $this->_getUrlFileName($urlFileName['path'], basename($fileName));
                $res['id'] = 0;
            }
        }
        return $res;
    }

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

    private function _getTempPath() {
        return (string)sprintf('%s/%s', date('Ym'), date('d'));
    }

    private function _getRondomFileName($type, $fileName) {
        $defExt = '';
        switch ($type) {
            case 'audio': $defExt = 'mp3'; break;
            case 'image': $defExt = 'jpg'; break;
            default: break;
        }
        return FileUtils::getRandomFileName('', 12, '.'.FileUtils::getFileExtension($fileName, $defExt));
    }

    private function _getPathFileName($type, $fileName) {
        $res = array('path' => '', 'file' => '');
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
        $res['path'] = $urlBasePath.'/'.$tempPath;
        $res['file'] = $this->_getRondomFileName($type, $fileName);
        return $res;
    }

    private function _getUrlFileName($urlPath, $fileName) {
        return (string)sprintf('%s/%s/%s', $this->getController()->dzRootUrl, $urlPath, basename($fileName));
    }

    private function _uploadAttach($uid, $attachment) {
        global $_G;
        $fileExtension = FileUtils::getFileExtension($attachment['name'], 'jpg');
        $type = 'forum';
        $extid = 0;
        $forcename = '';
        Yii::import('application.components.discuz.source.class.discuz.discuz_upload', true);
        $upload = new Mobcent_upload;
        $attach['extension'] = $fileExtension;
        $attach['attachdir'] = $upload->get_target_dir($type, $extid);
        $attach['attachment'] = $attach['attachdir'].$upload->get_target_filename($type, $extid, $forcename).'.'.$attach['extension'];
        $attach['target'] = getglobal('setting/attachdir').'./'.$type.'/'.$attach['attachment'];
        $savePath = getglobal('setting/attachdir').'./'.$type.'/'.$attach['attachdir'];
        if (!is_dir($savePath)) {
            mkdir($savePath, 0777, true);
        }
        $filename = $upload->get_target_filename($type, $extid, $forcename).'.'.$attach['extension'];
        $remote = $width = $thumb = 0;
        $res = false;
        if (($handle = fopen($attach['target'], 'w')) != false) {
            if (fwrite($handle,$attachment['data']) !== false) {
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
                $res['urlName'] = $path_url;
                $res['id'] = $aid;
            }
        }
        return $res;
    }
}
