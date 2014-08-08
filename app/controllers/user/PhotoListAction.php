<?php

/**
 * 个人中心相册图片列表接口
 *
 * @author 徐少伟 <xushaowei@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class PhotoListAction extends CAction {

    public function run($uid, $albumId, $page=1, $pageSize=10) {
        $res = WebUtils::initWebApiArray_oldVersion();

        $landUid = $this->getController()->uid;
        $res = $this->_getImageInfoList($res, $landUid, $uid, $page, $pageSize, $albumId);

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _getImageInfoList($res, $landUid, $uid, $page, $pageSize, $albumId) {
        $list = array();

        $imageList = UserPhotoInfo::getImageListByAlbumId($albumId, $uid, $page, $pageSize);

        foreach ($imageList as $image) {
            $tempimageInfo = PhotoUtils::getPhotoInfo($image); 
            // $imageInfo['board_id'] = (Int)$albumId;
            $imageInfo['pic_id'] = (int)$image;

            $imageInfo['title'] = $tempimageInfo['title'] == "" ? $tempimageInfo['filename'] : $tempimageInfo['title'];
            $imageInfo['title'] = WebUtils::emptyHtml($imageInfo['title']);

            $imageInfo['user_id'] = (int)$uid;
            $imageInfo['release_date'] = $tempimageInfo['dateline']."000";

            $lastReplyDate = PhotoUtils::getReplyDate($image);
            $imageInfo['last_update_date'] = $lastReplyDate['last_reply_date']."000";

            $imageInfo['user_nick_name'] = $tempimageInfo['username'];
            $imageInfo['hot'] = (int)$tempimageInfo['hot'];

            $imageInfo['replies'] = (int)PhotoUtils::getReplyCount($image);

            $imageInfo['thumb_pic'] = PhotoUtils::getImageCover($image);
            $imageInfo['origin_pic'] = PhotoUtils::getPhotosoRiginCover($tempimageInfo);

            $list[] = $imageInfo;
        }

        global $_G;
        if ($_G['adminid'] != 1) {
            $albumInfo = UserPhotoInfo::getAlbumInfo($uid, $albumId);
            switch ($albumInfo['friend']) {
                case 0:
                    $list;
                    break;
                case 1:
                    UserUtils::isFriend($uid, $landUid) || $landUid == $uid? $list : $list = 1;
                    break;
                case 2:
                    $list = PhotoUtils::judgeDesignatedFriends($albumInfo, $landUid, $uid, $list);
                    break;
                case 3:
                    $uid == $landUid ? $list : $list = 3;
                    break;
                case 4:
                    $landUid == $uid? $list : $list = 4;
                    break;
            }
        }
            $res['list'] = $list;

            if ($albumId == 0) {
                $count = UserPhotoInfo::getImageListCountByAlbumId($uid);
            } else {
                $count = UserPhotoInfo::getImageListCount($albumId, $uid);
            }

            $res = WebUtils::getWebApiArrayWithPage_oldVersion($page, $pageSize, $count, $res);

            if(empty($res['list'])) {
                if ($albumId == 0) {
                    return WebUtils::makeErrorInfo_oldVersion($res, 'to_view_the_defaultAlbum_does_not_exist');
                }
                    return WebUtils::makeErrorInfo_oldVersion($res, 'to_view_the_photo_does_not_exist');
            } elseif($res['list'] == 1 || $res['list'] == 2 || $res['list'] == 3 ||$res['list'] == 4) {
                $res['list'] = array();
                return WebUtils::makeErrorInfo_oldVersion($res, 'to_view_the_photo_set_privacy', array('{username}' => $albumInfo['username']));
            }
        return $res;
    }
}

class UserPhotoInfo extends DiscuzAR {

    public static function getAlbumInfo($uid, $albumId) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE uid = %d AND albumid = %d
            ORDER BY updatetime DESC
            ',
            array('home_album', $uid, $albumId)
        );
    }

    public static function getImageListByAlbumId($albumId, $uid, $page, $pageSize) {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT picid
            FROM %t
            WHERE albumid = %d
            AND uid = %d
            ORDER BY dateline DESC
            limit %d, %d
            ',
            array('home_pic', $albumId, $uid, $pageSize*($page-1), $pageSize)
        );
    }

    public static function getImageListCount($albumId, $uid) {
        return DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT picnum
            FROM %t
            WHERE albumid = %d
            AND uid = %d
            ',
            array('home_album', $albumId, $uid)
        );
    }

    public static function getImageListCountByAlbumId($uid) {
        $count = DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT COUNT(*) as num
            FROM %t
            WHERE albumid = 0
            AND uid = %d
            ',
            array('home_pic', $uid)
            );
        return $count['num'];
    }
}

class PhotoUtils {

    public static function getPhotoInfo($picId) {
        return DzPhotoAlbum::getAlbumByPicId($picId);
    }

    public static function getReplyDate($picId) {
        return DzPhotoAlbum::getImageLastReplyDate($picId);
    }

    public static function getReplyCount($picId) {
        return DzPhotoAlbum::getImageReplyCount($picId);
    }

    public static function getImageCover($picid) {
        $image = '';
        $albumImage = DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE picid = %d
            ',
            array('home_pic', $picid)
        );
        if (!empty($albumImage)) {
            require_once DISCUZ_ROOT.'./source/function/function_home.php';
            $image = pic_get($albumImage['filepath'], 'album', $albumImage['thumb'], $albumImage['remote']);
        }
        return WebUtils::getHttpFileName($image);
    }

    public static function getPhotosoRiginCover($albumImage) {
        require_once DISCUZ_ROOT.'./source/function/function_home.php';
        $image = pic_get($albumImage['filepath'], 'album', 0, $albumImage['remote']);
        return WebUtils::getHttpFileName($image);
    }

    public static function judgeDesignatedFriends($albumInfo, $landUid, $uid, $list) {
        $albumTargetIds = explode(',', $albumInfo['target_ids']);
        in_array($landUid, $albumTargetIds) || $landUid == $uid? $list : $list = 2;
        return $list;
    }
}

class DzPhotoAlbum extends DiscuzAR {
    
    const DISPLAY_ORDER_NORMAL = 0;

    public static function getAlbumByPicId($picId) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE picid = %d AND status = %d
            ',
            array('home_pic', $picId, self::DISPLAY_ORDER_NORMAL)
        );
    }

    public static function getImageLastReplyDate($picId) {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT max(dateline) as last_reply_date 
            FROM %t
            WHERE id = %d
            ',
            array('home_comment', $picId)
        );
    }

    public static function getImageReplyCount($picId) {
        $count = DbUtils::getDzDbUtils(true)->queryRow('
            SELECT count(*) as num
            FROM %t
            WHERE idtype = %s
            AND id = %d
            ',
            array('home_comment', 'picid', $picId)
        );
        return $count['num'];
    }
}