<?php

/**
 * 个人中心相册列表接口
 *
 * @author 徐少伟 <xushaowei@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class AlbumListAction extends CAction {

    public function run($uid, $page=1, $pageSize=10) {

        $res = WebUtils::initWebApiArray_oldVersion();

        $landUid = $this->getController()->uid;
        $res = $this->_getUserAlbumList($res, $landUid, $uid, $page, $pageSize);

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _getUserAlbumList($res, $landUid, $uid, $page, $pageSize) {
        $res['list'] = $this->_getAlbumInfoList($res, $landUid, $uid, $page, $pageSize);
        $count = UserAlbumInfo::getAlbumListCount($uid);
        if ($landUid != $uid) {
            $count = $count - 1;
        }
        $res = WebUtils::getWebApiArrayWithPage_oldVersion($page, $pageSize, $count, $res);
        return $res;
    }

    private function _getAlbumInfoList($res, $landUid, $uid, $page, $pageSize) {
        $list = array();
        
        $albumList = UserAlbumInfo::getAlbumList($uid, $page, $pageSize);

        foreach ($albumList as $album) {
            $albumInfo['album_id'] = (int)$album['albumid'];
            $albumInfo['user_id'] = (int)$uid;
            $albumInfo['release_date'] = $album['dateline']."000";
            $albumInfo['last_update_date'] = $album['updatetime']."000";
            $albumInfo['user_nick_name'] = $album['username'];
            $albumInfo['title'] = WebUtils::emptyHtml($album['albumname']);
            $albumInfo['info'] = $album['depict'];

            $albumPic = $this->getAlbumImagePic($album);
            $albumInfo['thumb_pic'] = PhotoUtils::getPhotoAlbumCover($albumPic);

            $list[] = $albumInfo;
        }

        $count = UserAlbumInfo::getAlbumListCount($uid) + 1;
        if ($page == ceil($count/$pageSize) && $landUid == $uid) {
            $list[] = $this->getDefaultAlbum($uid);
        }

        return $list;
    }

    public static function getDefaultAlbum($uid) {
        $album = array(
            'album_id' => 0,
            'user_id' => (int)$uid,
            'release_date' => '',
            'last_update_date'=> '',
            'user_nick_name' => '',
            'title' => WebUtils::t('默认相册'),
            'info' => '',
            'thumb_pic' => PhotoUtils::getPhotoAlbumCover(STATICURL.'image/common/nophoto.gif'),
        );
        return $album;
    }

    public static function getAlbumImagePic($value) {
        loadcache('albumcategory');
        require_once DISCUZ_ROOT.'./source/function/function_home.php';
        if($value['friend'] != 4 && ckfriend($value['uid'], $value['friend'], $value['target_ids'])) {
            $value['pic'] = pic_cover_get($value['pic'], $value['picflag']);            
        } elseif ($value['picnum']) {
            $value['pic'] = STATICURL.'image/common/nopublish.gif';
        } else {
            $value['pic'] = '';
        }
        return $value['pic'];
    }
}

class UserAlbumInfo extends DiscuzAR {

    public static function getAlbumList($uid, $page, $pageSize) {
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT *
            FROM %t
            WHERE uid = %d
            ORDER BY updatetime DESC
            LIMIT %d, %d
            ',
            array('home_album', $uid, $pageSize*($page-1), $pageSize)
        );
    }

    public static function getAlbumListCount($uid) {
        $count = DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT COUNT(*) as num
            FROM %t
            WHERE uid = %d
            ',
            array('home_album', $uid)
        );
        return $count + 1;
    }
}

class PhotoUtils {
 
    public static function getPhotoAlbumCover($image) {
        return WebUtils::getHttpFileName($image);
    }
}