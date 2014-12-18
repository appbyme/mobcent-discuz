<?php

/**
 * 图片墙设置接口
 *
 * @author 徐少伟 <xushaowei@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class PhotoGalleryAction extends CAction {

    public function run($page=1, $pageSize=10) {

        $res = WebUtils::initWebApiArray_oldVersion();
        $res = $this->_getImageList($res, $page, $pageSize);

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _getImageList($res, $page, $pageSize) {
        $res['list'] = $this->_getImageInfoByTids($page, $pageSize);
        $count = $this->_getImageInfoCount();
        $res = WebUtils::getWebApiArrayWithPage_oldVersion($page, $pageSize, $count, $res);
        return $res;
    }

    // 获取图片信息
    private function _getImageInfoByTids($page, $pageSize) {
        $imageList = $this->_getImageTidsByFids($page, $pageSize);

        $list = array();
        global $_G;
        $forum = $_G['forum'];
        foreach ($imageList as $image) {
            $tmpImageInfo = ForumUtils::getTopicInfo($image);
            $imageSummary = ForumUtils::getTopicCover((int)$image);
            $imageInfo['board_id'] = (int)$tmpImageInfo['fid'];
            $imageInfo['board_name'] = $fid != 0 ? $forum['name'] : ForumUtils::getForumName($tmpImageInfo['fid']);
            $imageInfo['board_name'] = WebUtils::emptyHtml($imageInfo['board_name']);
            $imageInfo['topic_id'] = (int)$image;
            $imageInfo['title'] = WebUtils::emptyHtml($tmpImageInfo['subject']);
            $imageInfo['user_id'] = (int)$tmpImageInfo['authorid'];
            $imageInfo['last_reply_date'] = ($tmpImageInfo['lastpost']) . "000";
            $imageInfo['user_nick_name'] = $tmpImageInfo['author']; 
            $imageInfo['hits'] = (int)$tmpImageInfo['views']; 
            $imageInfo['replies'] = (int)$tmpImageInfo['replies'];
            $imageInfo['top'] = (int)ForumUtils::isTopTopic($image) ? 1 : 0; 
            $imageInfo['status'] = (int)$tmpImageInfo['status']; 
            $imageInfo['essence'] = (int)$tmpImageInfo['digest'] ? 1 : 0;
            $imageInfo['hot'] = (int)$tmpImageInfo['highlight'] ? 1 : 0;
            $tempImageInfo = ImageUtils::getThumbImageEx($imageSummary, 15, true, false);
            $imageInfo['pic_path'] = $tempImageInfo['image'];
            $imageInfo['ratio']= $tempImageInfo['ratio'];
            $imageInfo['userAvatar'] = UserUtils::getUserAvatar($tmpImageInfo['authorid']);
            $imageInfo['recommendAdd'] = (int)ForumUtils::getRecommendAdd($image);
            $imageInfo['isHasRecommendAdd'] = (int)ForumUtils::isHasRecommendAdd($image);
            $imageInfo['imageList'] = array();
            $imageInfo['sourceWebUrl'] = (string)ForumUtils::getSourceWebUrl($image, 'topic');
            $list[] = $imageInfo;
        }
        return $list;
    }

    private function _getImageTidsByFids($page, $pageSize) {
        loadcache('plugin', true);

        $fids = ForumUtils::getForumPhotoGalleryShowFids();
        $tids = DzPictureSet::getImageTidsByFids($fids, $page, $pageSize);
        return $tids;
    }

    private function _getImageInfoCount() { 
        loadcache('plugin', true);

        $fids = ForumUtils::getForumPhotoGalleryShowFids();
        $count = DzPictureSet::getImageListCount($fids);
        return $count;
    }
}


class DzPictureSet extends DiscuzAR {
    
    // 查询图片帖子tid
    public static function getImageTidsByFids($fids, $page, $pageSize) {

        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT tid 
            FROM %t 
            WHERE fid IN (%n)
            AND displayorder >= 0
            AND attachment = 2
            ORDER BY dateline DESC
            LIMIT %d, %d
            ', 
            array('forum_thread', $fids, $pageSize*($page-1), $pageSize)
        );
    }

    // 查询图片帖子总数
    public static function getImageListCount($fids) {
        return DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT count(*) as num
            FROM %t
            WHERE fid IN (%n)
            AND displayorder >= 0
            AND attachment = 2
            ', 
            array('forum_thread', $fids)
        );
    }
}