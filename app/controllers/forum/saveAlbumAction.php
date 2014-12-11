<?php

/**
* 保存相册信息
*
* @author HanPengyu
*
* @param int $pidId 相册图片的id
* @param string $pidDesc 相册图片的描述
* @param int $albumId 相册ID -1 为默认相册
* @param string $interval $ids、$picDescs字符串的分隔符 默认为英文的逗号
*/

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class saveAlbumAction extends MobcentAction {
    public function run($ids, $picDescs, $albumId=-1, $interval=',') {
        $res = WebUtils::initWebApiArray();
        $res = $this->_saveAlbum($ids, $picDescs, $albumId, $interval);
        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _saveAlbum($ids, $picDescs, $albumId, $interval) {
        global $_G;
        $ids = rawurldecode($ids);
        $picDescs = rawurldecode($picDescs);
        $interval = rawurldecode($interval);

        $pidIdArray = explode($interval, $ids);
        $descArray = explode($interval, $picDescs);
        $conditions = array_combine($pidIdArray, $descArray);
        
        foreach ($conditions as $picid => $desc) {
            C::t('home_pic')->update_for_uid($_G['uid'], $picid, array('title' => $desc, 'albumid' => $albumId));
        }
        require_once libfile('function/spacecp');
        album_update_pic($albumId);
        return $this->makeErrorInfo($res, 'mobcent_save_album_success', array('noError'=>1));
    }
}