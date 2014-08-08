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

    public function run($moduleId, $page = 1, $pageSize = 10) {
        $key = CacheUtils::getNewsListKey(array($moduleId, $page, $pageSize));
        $this->runWithCache($key, array('mid' => $moduleId, 'page' => $page, 'pageSize' => $pageSize));
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

        $portals = AppbymePortalModuleSource::getPortalByMid($mid);
        $handCount = AppbymePortalModuleSource::getHandCount($mid);
        $autoAdd = AppbymePortalModuleSource::getAutoAdd($mid);
        $params = unserialize(AppbymePoralModule::getModuleParam($mid));
        $params == false && $params = array();        

        // 对错误mid的处理
        if (empty($portals)) {
            $res['list'] = array();
            return $res;
        }

        $count = 0;
        if (!empty($autoAdd)) {
            if ($autoAdd[0]['idtype'] == 'fid') {
                foreach ($autoAdd as $auto) {
                    $fids[] = $auto['id'];
                }
                $count = DzForumThread::getByFidCount($fids, $params);
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
        
        // 只有手动添加的
        if (empty($autoAdd) && $handCount) {
            $res['list'] = $this->_handData($mid, $offset, $pageSize, $params);
            return $res;
        }

        // 只有自动添加的
        if (!empty($autoAdd) && !$handCount) {
            $rows = array();
            if ($autoAdd[0]['idtype'] == 'fid') {
                $rows = $this->_autoFidData($fids, $offset, $pageSize, $params);
            } else {
                $rows = $this->_autoCatidData($catids, $offset, $pageSize, $params);
            }
            $res['list'] = $rows;
            return $res;
        }

        // 以下是混合的状况
        if ($offset == 0) {
            if ($handCount >= $pageSize) {
                $rows = $this->_handData($mid, $offset, $pageSize, $params);
            } elseif ($handCount < $pageSize) {
                $row1 = $this->_handData($mid, $offset, $handCount, $params);
                $limit = $pageSize - $handCount;
                if ($autoAdd[0]['idtype'] == 'fid') {
                    $row2 = $this->_autoFidData($fids, $offset, $limit, $params);
                } else {
                    $row2 = $this->_autoCatidData($catids, $offset, $limit, $params);
                }
                $rows = array_merge($row1, $row2);
            }            
        } elseif ($offset == $handCount) {
            if ($autoAdd[0]['idtype'] == 'fid') {
                $rows = $this->_autoFidData($fids, 0, $pageSize, $params);
            } else {
                $rows = $this->_autoCatidData($catids, 0, $pageSize, $params);
            }                
        } elseif ($offset > $handCount) {
            $start = $offset - $handCount;          
            if ($autoAdd[0]['idtype'] == 'fid') {
                $rows = $this->_autoFidData($fids, $start, $pageSize, $params);
            } else {
                $rows = $this->_autoCatidData($catids, $start, $pageSize, $params);
            }   
        } elseif ($offset < $handCount) {
            $hCount = $handCount - $offset;
            if ($hCount >= $pageSize) {
                $rows = $this->_handData($mid, $offset, $pageSize, $params);
            } elseif ($hCount < $pageSize) {
                $row1 = $this->_handData($mid, $offset, $hCount, $params);
                $limit = $pageSize - $hCount;
                if ($autoAdd[0]['idtype'] == 'fid') {
                    $row2 = $this->_autoFidData($fids, 0, $limit, $params);
                } else {
                    $row2 = $this->_autoCatidData($catids, 0, $limit, $params);
                }
                $rows = array_merge($row1, $row2);
            } 
        }
        $res['list'] = $rows;
        return $res;
    }

    // 取出手动添加的数据
    private function _handData($mid, $offset, $pageSize, $params) {
        $handiInfos = AppbymePortalModuleSource::getHandData($mid, $offset, $pageSize);
        foreach ($handiInfos as $hand) {
            if ($hand['idtype'] == 'tid') {
                $topicSummary = ForumUtils::getTopicSummary($hand['id'], 'portal');
                $rows[] = $this->_getListField(ForumUtils::getTopicInfo($hand['id']), $topicSummary, 'topic', $hand['id'], $params);
            } else {
                $articleSummary = PortalUtils::getArticleSummary($hand['id']);
                $articleInfo = $this->_getArticleByAid($hand['id']);
                $rows[] = $this->_getListField($articleInfo, $articleSummary, 'news', $hand['id'], $params);
            }
        }
        return $rows;        
    }

    // 通过fids来取出数据
    private function _autoFidData($fids, $offset, $pageSize, $params) {
        $lists = DzForumThread::getByFidData($fids, $offset, $pageSize, $params);
        foreach ($lists as $list) {
            $topicSummary = ForumUtils::getTopicSummary($list['tid'], 'portal');
            // if ($list['tid'] == 85) {
            //     var_dump($topicSummary);
            //     die;
            // }
            $rows[] = $this->_getListField($list, $topicSummary, 'topic', $list['tid'], $params);
        }
        return $rows;        
    }

    // 通过catids来取出数据
    private function _autoCatidData($catids, $offset, $pageSize, $params) {
        $lists = DzPortalArticle::getByCatidData($catids, $offset, $pageSize, $params);
        foreach ($lists as $list) {
            $articleSummary = PortalUtils::getArticleSummary($list['aid']);
            $rows[] = $this->_getListField($list, $articleSummary, 'news', $list['aid']);
        }
        return $rows;        
    }

    // 通过aid来取出文章的信息
    private function _getArticleByAid($aid) {
        $articleCount = PortalUtils::getArticleCount($aid);
        $articleInfo = PortalUtils::getNewsInfo($aid);
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
        $row['pic_path'] = ImageUtils::getThumbImage($summary['image']);
        $row['redirectUrl'] = (string)$list['url'];

        return $row;
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
}