<?php

/**
* 保存相册信息
*
* @author HanPengyu
*
* @param int $pidId 相册图片的id
* @param string $pidDesc 相册图片的描述
* @param int $albumId 相册ID -1 为默认相册
*/

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class saveAlbumAction extends MobcentAction {
    public function run($ids, $picDesc, $albumId=-1) {
        $res = WebUtils::initWebApiArray();
        $res = $this->_saveAlbum($ids, $picDesc, $albumId, $interval);
        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _saveAlbum($ids, $picDesc, $albumId) {
        global $_G;
        $ids = rawurldecode($ids);
        $picDesc = rawurldecode($picDesc);
        $pidIdArray = explode(',', $ids);

        foreach ($pidIdArray as $picid) {
            C::t('home_pic')->update_for_uid($_G['uid'], $picid, array('title' => $picDesc, 'albumid' => $albumId));
        }

        require_once libfile('function/spacecp');
        album_update_pic($albumId);
        return $this->makeErrorInfo($res, 'mobcent_save_album_success', array('noError'=>1));
    }
}