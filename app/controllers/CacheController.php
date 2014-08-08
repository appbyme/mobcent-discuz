<?php

/**
 * @author  谢建平 <jianping_xie@aliyun.com>  
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class CacheController extends MobcentController  {
    
    protected function beforeAction($action) {
        // parent::beforeAction($action);
        return true;
    }

    public function actions() {
        return array(
        );
    }

    protected function mobcentAccessRules() {
        return array(
        );
    }

    public function init() {
        parent::init();

        set_time_limit(0);
    }

    public function actionClean($fid = 0, $gid = 0, $sort = '') {
        if ($fid == 0 && $gid == 0) {
            Yii::app()->cache->flush();
        } else {
            Yii::app()->cache->delete(CacheUtils::getForumListKey());

            $sortArr = array('', 'new', 'marrow', 'top');
            $fids = $this->_getFids($fid);
            $gids = $this->_getGids($gid);
            foreach ($sortArr as $sort) {
                foreach ($fids as $fid) {
                    foreach ($gids as $gid) {
                        $key = CacheUtils::getTopicListKey($fid, $gid, 1, 10, $sort);
                        Yii::app()->cache->delete($key);
                    }
                }
            }
        }

        echo '清空缓存成功!!!';
    }

    public function actionUpdate($fid = 0, $gid = 0, $sort = '') {
        $timer = new CountTimer;
        // $cacheToken = 'cache-update';
        // Yii::beginProfile($cacheToken);

        ob_start();
        // 生成版块列表缓存
        $this->forward('forum/forumlist', false);

        // 生成帖子列表缓存
        $sortArr = array('', 'new', 'marrow', 'top');
        $fids = $this->_getFids($fid);
        $uids = $this->_getUidsByGid($gid);
        foreach ($sortArr as $sort) {
            foreach ($fids as $fid) {
                foreach ($uids as $uid) {
                    $_GET = array_merge($_GET, array(
                        'hacker_uid' => $uid,
                        'boardId' => $fid, 'page' => 1, 
                        'pageSize' => 10, 'sortby' => $sort,
                    ));
                    $res = $this->forward('forum/topiclist', false);
                    ob_clean();
                }
            }
        }
        ob_end_clean();

        var_dump($timer->stop());
        // Yii::endProfile($cacheToken);
    }

    public function actionCleanThumb($id = 0, $type = 'post') {
        CFileHelper::removeDirectory(MOBCENT_THUMB_PATH);

        echo '清空缓存成功!!!';
    }

    public function actionMakeThumb($count=10) {
        $thumbTaskList = CacheUtils::getDzPluginCache('thumb_task_list');
        $thumbTaskList === false && $thumbTaskList = array();
        
        $count <= 0 && $count = count($thumbTaskList);
        $count = min(count($thumbTaskList), $count);
        $i = 0;
        foreach ($thumbTaskList as $key => $thumb) {
            if ($i >= $count) {
                break;
            }
            ImageUtils::getThumbImageEx($thumb, 20, false, false, true);
            $i++;
        }
        echo WebUtils::jsonEncode($thumbTaskList);
        
        array_splice($thumbTaskList, 0, $count);
        CacheUtils::setDzPluginCache('thumb_task_list', $thumbTaskList);
    }
    
    // 找到需要更新的版块id
    private function _getFids($fid) {
        if ($fid == 0) {
            $fids = ForumUtils::getForumShowFids();
        }
        $fids[] = $fid;
        return $fids;
    }

    private function _getGids($gid) {
        if ($gid == 0) {
            $config = WebUtils::getDzPluginAppbymeAppConfig('cache_usergroup');
            if ($config !== false && ($config = unserialize($config))) {
                $config[0] != '' && $gids = $config;
            } else {
                $gids = DzCommonUserGroup::getAllowVisitGids();
            }
        }
        $gids[] = $gid;
        return $gids;
    }

    // 找到需要更新缓存的用户id
    private function _getUidsByGid($gid) {
        $gids = $this->_getGids($gid);
        $uids = DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT uid
            FROM %t 
            WHERE groupid IN (%n)
            GROUP BY groupid
            ', 
            array(
                'common_member',
                $gids
            )
        );
        $uids[] = 0;
        return $uids;
    }
}