<?php

/**
 * 保存头像接口
 *
 * @author  谢建平 <jianping_xie@aliyun.com>  
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class SaveAvatarAction extends MobcentAction {

    public function run($avatar) {
        $res = $this->initWebApiArray();

        $uid = $this->getController()->uid;
        $res = $this->_runAction(array('res' => $res, 'uid' => $uid, 'avatar' => $avatar));

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _runAction($params) {
        extract($params);

        $isSaveSuccess = false;
        $image = $avatar;
        if (!empty($image) && ($imageData = WebUtils::httpRequest($image)) != '') {
            $savePath = UploadUtils::getTempAvatarPath();
            if (!empty($savePath)) {
                $config = Yii::app()->params['mobcent']['user'];
                $this->_deleteTempAvatarFiles($uid);
                $avatarFiles = $this->_getTempAvatarFiles($uid);
                
                $avatarBigFile = $savePath . '/' . $avatarFiles['big'];
                $avatarMidFile = $savePath . '/' . $avatarFiles['mid'];
                $avatarSmallFile = $savePath . '/' . $avatarFiles['small'];

                file_put_contents($avatarBigFile, $imageData);
                file_put_contents($avatarMidFile, $imageData);
                file_put_contents($avatarSmallFile, $imageData);
                
                require_once MOBCENT_APP_ROOT . '/components/discuz/source/class/class_image.php';
                $thumb = new Mobcent_Image;
                $zoomRes = true;
                $zoomRes &= $thumb->makeThumb($avatarBigFile, '', $config['avatarBigLength']);
                $zoomRes &= $thumb->makeThumb($avatarMidFile, '', $config['avatarMidLength']);
                $zoomRes &= $thumb->makeThumb($avatarSmallFile, '', $config['avatarSmallLength']);
                $isSaveSuccess = $zoomRes && $this->_saveAvatarByUcenter($uid, 
                    $this->flashdata_encode(file_get_contents($avatarBigFile)),
                    $this->flashdata_encode(file_get_contents($avatarMidFile)),
                    $this->flashdata_encode(file_get_contents($avatarSmallFile))
                );
                $this->_deleteTempAvatarFiles($uid);
            }
        }

        return $isSaveSuccess ? $res : WebUtils::makeErrorInfo_oldVersion($res, WebUtils::t('保存文件失败'));
    }

    private function _getTempAvatarFiles($uid) {
        return array(
            'big' => sprintf('avatar_%s_big.jpg', $uid),
            'mid' => sprintf('avatar_%s_mid.jpg', $uid),
            'small' => sprintf('avatar_%s_small.jpg', $uid),
        );
    }

    private function _deleteTempAvatarFiles($uid) {
        $files = $this->_getTempAvatarFiles($uid);
        $path = UploadUtils::getTempAvatarPath();
        foreach ($files as $fileName) {
            FileUtils::safeDeleteFile($path.'/'.$fileName);
        }
    }

    private function _saveAvatarByUcenter($uid, $avatarBig, $avatarMid, $avatarSmall) {
        loaducenter();
        $uc_avatarflash = uc_avatar($uid, 'virtual', 0);

        if (!empty($uc_avatarflash[7])) {
            $parse = parse_url($uc_avatarflash[7]);
            if (!empty($parse['query'])) {
                $url = sprintf('%s/index.php?m=user&a=rectavatar&%s', UC_API, $parse['query']);
                $saveRes = WebUtils::httpRequestByDiscuzApi($url, sprintf('avatar1=%s&avatar2=%s&avatar3=%s', $avatarBig, $avatarMid, $avatarSmall));
                $saveRes = WebUtils::parseXmlToArray($saveRes);
                if (!empty($saveRes['face']['@attributes']['success']) && 
                    $saveRes['face']['@attributes']['success'] == 1) {
                    return true;
                }
            }
        }
        return false;
    }

    private function flashdata_encode($s) {
        // return bin2hex($s);
        $r = '';
        $l = strlen($s);
        for ($i=0; $i < $l; $i++) { 
            $temp = ord($s[$i]);
            $k1 = $temp >> 4;
            $k2 = $temp - ($k1 << 4);
            $k1 += $k1 > 9 ? 7 : 0;
            $k1 = chr($k1 + 48);
            $k2 += $k2 > 9 ? 7 : 0;
            $k2 = chr($k2 + 48);
            $r .= $k1 . $k2;
        }
        return $r;
    }
}