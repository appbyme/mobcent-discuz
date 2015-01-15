<?php

/**
 * 新版上传头像接口
 *
 * @author  HanPengyu
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class UploadAvatarExAction extends MobcentAction {

    public function run() {
        $res = $this->initWebApiArray();
        $uid = $this->getController()->uid;
        $res = $this->_runAction($res, $uid);
        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _runAction($res, $uid) {
        if (empty($_FILES['userAvatar']['tmp_name'])) {
            return WebUtils::makeErrorInfo_oldVersion($res, WebUtils::t('请选择上传的文件'));
        }
        
        if ($_FILES['userAvatar']['error'] > 0) {
            return WebUtils::makeErrorInfo_oldVersion($res, WebUtils::t('上传文件失败'));
        }

        if ($_FILES['userAvatar']['size'] > 2000000) {
            return WebUtils::makeErrorInfo_oldVersion($res, WebUtils::t('上传文件太大'));
        }

        $savePath = UploadUtils::getTempAvatarPath();
        $fileName = sprintf('%s/avatar_%s.jpg', $savePath, $uid);
        if (move_uploaded_file($_FILES['userAvatar']['tmp_name'], $fileName)) {
            $imageData = file_get_contents($fileName);
            $image = $this->_uploadAvatarByUcenter($uid, $fileName, $imageData);
            FileUtils::safeDeleteFile($fileName);
            if (!empty($image)) {
                return array_merge($res, array('icon_url' => '', 'pic_path' => $image)); 
            }
            // WebUtils::httpRequestAppAPI('user/saveavatar', array('avatar' => $image, 'hacker_uid' => 1));
            // die();
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