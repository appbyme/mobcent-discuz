<?php

/**
 * 上传头像接口
 *
 * @author  谢建平 <jianping_xie@aliyun.com>    
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class UploadAvatarAction extends MobcentAction {

    public function run() {
        $res = $this->initWebApiArray();

        $uid = $this->getController()->uid;
        $res = $this->_runAction($res, $uid);

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _runAction($res, $uid) {
        ($imageData = file_get_contents('php://input')) === false && $imageData = $GLOBALS['HTTP_RAW_POST_DATA']; 
        // $imageData = FileUtils::getFile('/home/xjp/data/images/2.jpg');
        if (!empty($imageData)) {
            if (($savePath = UploadUtils::getTempAvatarPath()) != '') {
                $fileName = sprintf('%s/avatar_%s.jpg', $savePath, $uid);
                if (($pfile = fopen($fileName, 'wb')) != false) {
                    if (fwrite($pfile, $imageData) !== false) {
                        $image = $this->_uploadAvatarByUcenter($uid, $fileName, $imageData);
                    }
                    fclose($pfile);
                    FileUtils::safeDeleteFile($fileName);
                    if (!empty($image)) {
                        return array_merge($res, array('icon_url' => '', 'pic_path' => $image)); 
                    }
                }
            }
        }
        return WebUtils::makeErrorInfo_oldVersion($res, WebUtils::t('上传文件失败'));
    }

    private function _uploadAvatarByUcenter($uid, $fileName, $fileData) {
        $image = '';

        loaducenter();
        $uc_avatarflash = uc_avatar($uid, 'virtual', 0);

        if (!empty($uc_avatarflash[7])) {
            $parse = parse_url($uc_avatarflash[7]);
            if (!empty($parse['query'])) {
                $url = sprintf('%s/index.php?m=user&a=uploadavatar&%s', UC_API, $parse['query']);
                $res = WebUtils::httpRequestByDiscuzApi($url, array('Filedata' => $fileData), '', array('Filedata' => $fileName));
                strpos($res, 'http') !== false && $image = $res;
            }
        }
        
        return $image;
    }
}