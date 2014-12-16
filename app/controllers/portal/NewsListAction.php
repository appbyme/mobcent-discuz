<?php

/**
 * 门户资讯列表
 *
 * @author HanPengyu
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}
// Mobcent::setErrors();
class NewsListAction extends MobcentAction {
    const HANDPAGE = 10;
    public function run($moduleId, $page = 1, $pageSize = 10, $longitude='', $latitude='', $radius=100000, $isImageList=0) {
        $key = CacheUtils::getNewsListKey(array($moduleId, $page, $pageSize, $longitude, $latitude, $radius, $imageList));
        $this->runWithCache($key, array('mid' => $moduleId, 'page' => $page, 'pageSize' => $pageSize, 'longitude' => $longitude, 'latitude' => $latitude, 'radius' => $radius, 'isImageList' => $isImageList));
    }

    protected function runWithCache($key, $params=array()) {
        extract($params);
        
        $res = array();
        $cache = $this->getCacheInfo();
        if (!$cache['enable'] || ($res = Yii::app()->cache->get($key)) === false) {
            $res = WebUtils::outputWebApi($this->getResult($params), '', false);
            if ($page == 1 && $cache['enable']) {
                Yii::app()->cache->set($key, $res, $cache['expire']);
            }
        }
        echo $res;
    }

    protected function getCacheInfo() {
        $cacheInfo = array('enable' => 1, 'expire' => DAY_SECONDS * 1);
        if (($cache = WebUtils::getDzPluginAppbymeAppConfig('cache_newslist')) > 0) {
            $cacheInfo['expire'] = $cache;
        } else {
            $cacheInfo['enable'] = 0;
        }
        return $cacheInfo;
    }

    protected  function getResult($params) {
        extract($params);

        $res = $this->initWebApiArray();
        if ($page == 1) {
            $res['piclist'] = $this->_getPicList($mid);
        } else {
            $res['piclist'] = array();
        }

        $portals   = AppbymePortalModuleSource::getPortalByMid($mid);
        $handCount = $this->_handCount($mid, $portals);
        $autoAdd   = AppbymePortalModuleSource::getAutoAdd($mid);
        $params    = unserialize(AppbymePoralModule::getModuleParam($mid));
        $params    == false && $params = array();  

        // 对错误mid的处理
        if (empty($portals)) {
            $res['list'] = array();
            return $res;
        }

        $count = 0; // 自动添加的数目
        if (!empty($autoAdd)) {
            if ($autoAdd[0]['idtype'] == 'fid') {
                foreach ($autoAdd as $auto) {
                    $fids[] = $auto['id'];
                }
                $count = DzForumThread::getByFidCount($fids, $params, $longitude, $latitude, $radius);
                // Mobcent::dumpSql();
            } else {
                foreach ($autoAdd as $auto) {
                    $catids[] = $auto['id'];
                }
                $count = DzPortalArticle::getByCatidCount($catids, $params);
            }
        }

        $total = $handCount + $count;
        // 没有查到数据
        if ($total == 0) {
            $res['list'] = array();
            return $res;
        }

        $pageInfo = WebUtils::getWebApiArrayWithPage_oldVersion($page, $pageSize, $total);
        $res = array_merge($res, $pageInfo);
        $offset = ($page - 1)*$pageSize;
        if ($page == 1) {
            if ($handCount <= self::HANDPAGE) {
                $autoData = array();
                $handData = ($handCount != 0 && $page = 1) ? $this->_handData($mid, $offset, $handCount, $params) : array();
                $pageInfo = WebUtils::getWebApiArrayWithPage_oldVersion($page, $handCount, $handCount);
                $res = array_merge($res, $pageInfo);
                if ($count != 0) {
                    if ($autoAdd[0]['idtype'] == 'fid') {
                        $autoData = $this->_autoFidData($fids, $offset, $pageSize, $params, $longitude, $latitude, $radius);
                    } else {
                        $autoData = $this->_autoCatidData($catids, $offset, $pageSize, $params);
                    }
                    $total = $count !=0 ? ($count + $handCount) : $handCount ;
                    $pageInfo = WebUtils::getWebApiArrayWithPage_oldVersion($page, $pageSize, $total);
                    $res = array_merge($res, $pageInfo);
                }
                $rows = array_merge((array)$handData, (array)$autoData);
                $res['list'] = $rows;
                return $res;
            }

            $total = $count !=0 ? ($count + $handCount) : $handCount ;
            $pageInfo = WebUtils::getWebApiArrayWithPage_oldVersion($page, $handCount, $total);
            $res = array_merge($res, $pageInfo);
            $res['list'] = $this->_handData($mid, $offset, $handCount);

            return $res;
        }

        $page = ($handCount <= self::HANDPAGE) ? $page : ($page - 1) ;
        $offset = ($page - 1) * $pageSize;

        if ($count != 0) {
            if ($autoAdd[0]['idtype'] == 'fid') {
                $autoData = $this->_autoFidData($fids, $offset, $pageSize, $params, $longitude, $latitude, $radius);
            } else {
                $autoData = $this->_autoCatidData($catids, $offset, $pageSize, $params);
            } 
            $pageInfo = WebUtils::getWebApiArrayWithPage_oldVersion($page, $pageSize, $count);
            $res = array_merge($res, $pageInfo);
            $res['list'] = $autoData;
        } else {
            $pageInfo = WebUtils::getWebApiArrayWithPage_oldVersion($page, $handCount, $handCount);
            $res = array_merge($res, $pageInfo);
            $res['list'] = array();
        }

        return $res;
    }

    // 取出手动添加的数据
    private function _handData($mid, $offset, $pageSize, $params) {
        $rows = array();
        $portals = AppbymePortalModuleSource::getPortalByMid($mid);
        $handCount = $this->_handCount($mid, $portals);    // 手动添加总数
        $forumCount = AppbymePortalModuleSource::getHandCount($mid);    // 真实手动添加的tid、aid的总数
        $customCount = $handCount - $forumCount;    // discuz bid下面的数目        
        $bids = $this->_handCount($mid, $portals, 'bid');

        foreach($portals as $portal) {
           if ($portal['idtype'] == 'tid') {
                $topicSummary = ForumUtils::getTopicSummary($portal['id'], 'portal', true, array('imageList' => $_GET['imageList'], 'imageListLen' => 9, 'imageListThumb' => 1));
                $rows[] = $this->_getListField(ForumUtils::getTopicInfo($portal['id']), $topicSummary, 'topic', $portal['id']);
           } elseif ($portal['idtype'] == 'aid') {
                $articleSummary = PortalUtils::getArticleSummary($portal['id']);
                $articleInfo = $this->_getArticleByAid($portal['id']);
                $rows[] = $this->_getListField($articleInfo, $articleSummary, 'news', $portal['id']);
           } elseif ($portal['idtype'] == 'bid') {
                $rows = array_merge($rows, $this->getTopArtData(AppbymePortalModuleSource::getDataByBid($portal['id'])));
           }
        }

        return $rows;
    }

    // 获取手动添加文章或者是帖子的内容
    private function getTopArtData($handInfos) {
        $rows = array();
        if(empty($handInfos)) return $rows;
        foreach ($handInfos as $hand) {
            if ($hand['idtype'] == 'tid') {
                $topicSummary = ForumUtils::getTopicSummary($hand['id'], 'portal', true, array('imageList' => $_GET['imageList'], 'imageListLen' => 9, 'imageListThumb' => 1));
                $topicInfo = ForumUtils::getTopicInfo($hand['id']);

                //  add:在添加自定义内容的时候，手动修改的帖子标题 
                $hand['title'] && $topicInfo['subject'] = $hand['title'];
                $rows[] = $this->_getListField($topicInfo, $topicSummary, 'topic', $hand['id']);
            } elseif($hand['idtype'] == 'aid') {
                $articleSummary = PortalUtils::getArticleSummary($hand['id']);
                $articleInfo = $this->_getArticleByAid($hand['id']);

                //  add:在添加自定义内容的时候，手动修改的文章标题 
                $hand['title'] && $articleInfo['title'] = $hand['title'];
                $rows[] = $this->_getListField($articleInfo, $articleSummary, 'news', $hand['id']);
            }
        }

        return $rows;
    }

    // 通过fids来取出数据
    private function _autoFidData($fids, $offset, $pageSize, $params, $longitude, $latitude, $radius) {
        // [add]板块设置用户组权限后，针对当前用户进行过滤（在列表中是否显示）Author：HanPengyu，Data：14.11.28
        require_once libfile('function/forumlist');
        $tmpFids = array();
        foreach ($fids as $fid) {
            $forum = DzForumForum::getForumFieldByFid($fid);
            forum($forum) && $tmpFids[] = $fid;
        }
        $fids = $tmpFids;
        
        $lists = DzForumThread::getByFidData($fids, $offset, $pageSize, $params, $longitude, $latitude, $radius);
        // Mobcent::dumpSql();
        foreach ($lists as $list) {
            $topicSummary = ForumUtils::getTopicSummary($list['tid'], 'portal', true, array('imageList' => $_GET['imageList'], 'imageListLen' => 9, 'imageListThumb' => 1));
            $rows[] = $this->_getListField($list, $topicSummary, 'topic', $list['tid'], $params);
        }
        return $rows;        
    }

    // 通过catids来取出数据
    private function _autoCatidData($catids, $offset, $pageSize, $params) {
        $lists = DzPortalArticle::getByCatidData($catids, $offset, $pageSize, $params);
        $rows = array();
        foreach ($lists as $list) {

            // [add]考虑文章是通过帖子来发布的时候的评论数问题 Author：HanPengyu，Data：14.09.28
            if ($list['idtype'] == 'tid') {
                $topicInfo = ForumUtils::getTopicInfo($list['id']);
                $list['commentnum'] = (int)$topicInfo['replies'];
            }

            $articleSummary = PortalUtils::getArticleSummary($list['aid']);
            $rows[] = $this->_getListField($list, $articleSummary, 'news', $list['aid']);
        }
        return $rows;        
    }

    // 通过aid来取出文章的信息
    private function _getArticleByAid($aid) {
        $articleCount = PortalUtils::getArticleCount($aid);
        $articleInfo = PortalUtils::getNewsInfo($aid);

        // [add]考虑文章是通过帖子来发布的时候的评论数问题 Author：HanPengyu，Data：14.09.27
        if ($articleInfo['idtype'] == 'tid') {
            $topicInfo = ForumUtils::getTopicInfo($articleInfo['id']);
            $articleCount['commentnum'] = (int)$topicInfo['replies'];
        }

        $articleInfo = array_merge($articleCount, $articleInfo);
        return $articleInfo;
    }

    /**
     * 列表显示需要的字段
     * 
     * @param array $list      帖子或者文章的详细字段信息.
     * @param array $summary      帖子或者文章的摘要和图片.
     * @param string $source_type 源的类型.
     * @param int $source_id      源的id.
     *
     * @return array 整理好的字段.
     */
    private function _getListField($list, $summary, $source_type, $source_id, $params=array()){
        $row = array();

        // 显示样式
        if ($params['topic_style'] == 2 ) {
            $statu = 'lastpost';
        } else {
            $statu = 'dateline';
        }

        if ($source_type == 'topic') {
            $row['fid'] = (int)$list['fid'];
        }
        $row['source_type'] = $source_type;
        $row['source_id'] = (int)$source_id;
        $row['title'] = $source_type == 'topic' ? (string)$list['subject'] : (string)$list['title'];
        $row['title'] = WebUtils::emptyHtml($row['title']);
        $row['user_id'] = $source_type == 'topic' ? (int)$list['authorid'] : (int)$list['uid'];
        $row['last_reply_date'] = $source_type == 'topic' ? $list[$statu].'000' : $list['dateline'].'000';
        $row['user_nick_name'] = $source_type == 'topic' ? (string)$list['author'] : (string)$list['username'];
        $row['hits'] = $source_type == 'topic' ? (int)$list['views'] : (int)$list['viewnum'];
        $row['summary'] = $summary['msg'];
        $row['replies'] = $source_type == 'topic' ? (int)$list['replies'] : (int)$list['commentnum'];

        $tempRow = ImageUtils::getThumbImageEx($summary['image'], 15, true, true);
        $row['pic_path'] = $tempRow['image'];
        $row['ratio'] = $tempRow['ratio'];
        $row['redirectUrl'] = (string)$list['url'];

        $row['userAvatar'] = UserUtils::getUserAvatar($row['user_id']);
        $row['recommendAdd'] = $source_type == 'topic' ? ForumUtils::getRecommendAdd($row['source_id']) : 0;
        $row['isHasRecommendAdd'] = $source_type == 'topic' ? ForumUtils::isHasRecommendAdd($row['source_id']) : 0;
        $isFavor = ForumUtils::isFavoriteTopic($_G['uid'], $row['source_id']) ? 1 : 0;
        $row['is_favor'] = $source_type == 'topic' ? $isFavor : 0;
        $row['distance'] = isset($list['distance']) ? (string)$list['distance'] : '';
        $row['location'] = isset($list['location']) ? (string)$list['location'] : '';
        $row['imageList'] = $summary['imageList'];

        return $row;
    }


    /**
     * 获取手动添加的总数包括tid，aid，还有bid，也可以取出bids
     * 
     * @param int $mid
     * @param array  $portals 门户表信息.
     * @param string $type 取出的类型，$type=count,取出总数。$type=bid，取出bids.
     *
     * @access private
     *
     * @return mixed Value.
     */
    private function _handCount($mid, $portals, $type="count") {
        // tid、aid的数目
        $forumCount = AppbymePortalModuleSource::getHandCount($mid);
        // bid 下面内容的数目
        $portalCount = 0;
        $bids = array();
        foreach($portals as $k => $v) {
            if($v['idtype'] == 'bid') {
                $bids[] = (int)$v['id'];
                $portalCount += AppbymePortalModuleSource::getCountByBid($v['id']);
            }
        }
        $count = $forumCount + $portalCount;
        $res = $type == 'count' ? $count : $bids;
        return $res;
    }

    // 获取一个模块下面幻灯片的信息
    private function _getPicList($mid) {
        $portals = AppbymePortalModuleSource::getPortalByMid($mid, 2);
        $piclist = array();
        foreach ($portals as $portal) {
            $pic_path = ImageUtils::getThumbImage(WebUtils::getHttpFileName($portal['imgurl']));
            if ($portal['idtype'] == 'tid') {
                $topicinfo = ForumUtils::getTopicInfo($portal['id']);
                $piclist[] = $this->_fieldPicList($topicinfo['fid'], 'topic', $portal['id'], $portal['title'], $pic_path);
            } elseif ($portal['idtype'] == 'aid') {
                $piclist[] = $this->_fieldPicList(0, 'news', $portal['id'], $portal['title'], $pic_path);
            } elseif ($portal['idtype'] == 'url') {
                $piclist[] = $this->_fieldPicList(0, 'weblink', $portal['id'], $portal['title'], $pic_path, $portal['url']);
            } elseif ($portal['idtype'] == 'bid') {
                $piclist = array_merge($piclist, $this->_getPicByBid($portal['id']));
            }
        }
        return $piclist;
    }

    // 幻灯片需要的字段
    private function _fieldPicList($fid, $source_type, $source_id, $title, $pic_path, $pic_toUrl='') {
        $row = array();
        $row['fid'] = (int)$fid;
        $row['source_type'] = $source_type;
        $row['source_id'] = (int)$source_id;
        $row['title'] = WebUtils::emptyHtml($title);
        $row['pic_path'] = $pic_path;
        if ($source_type == 'weblink') {
            $row['pic_toUrl'] = $pic_toUrl;
        }
        return $row;
    }

    // 通过bid取出幻灯片的数据
    private function _getPicByBid($bid) {
        global $_G;
        block_get($bid);
        $itemList = $_G['block'][$bid]['itemlist'];
        $list = array();
        foreach ($itemList as $item) {
            $sourceType = $item['idtype'] == 'aid' ? 'news' : 'topic';
            $sourceId = $item['id'];
            $title = $item['title'];

            if ($item['makethumb'] == 1) {  // 生成缩略图成功
                if ($item['picflag'] == 1) {    // 本地
                    $picPath = $_G['setting']['attachurl'].$item['thumbpath'];                
                } elseif($item['picflag'] == 2) {    // 远程
                    $picPath = $_G['setting']['ftp']['attachurl'].$item['thumbpath'];
                }
            } elseif ($item['makethumb'] == 0) {    // 缩略图生成失败
                $picPath = $item['pic'];
            }

            $picPath = ImageUtils::getThumbImage(WebUtils::getHttpFileName($picPath));
            $list[] = $this->_fieldPicList(0, $sourceType, $sourceId, $title, $picPath);
        }
        return $list;
    }
}