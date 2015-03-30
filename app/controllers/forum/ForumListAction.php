<?php

/**
 * 版块列表接口
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

// Mobcent::setErrors();
class ForumListAction extends MobcentAction {

    public function run($fid=0, $type='') {
        global $_G;
        if ($type == 'rec') {
            $res = $this->getResult(array('fid' => $fid, 'type' => $type));
            echo WebUtils::outputWebApi($res, '', true);
        }
        
        $key = CacheUtils::getForumListKey(array($fid, $_G['groupid'], $type));
        $this->runWithCache($key, array('fid' => $fid));
    }

    protected function getCacheInfo() {
        $cacheInfo = array('enable' => 1, 'expire' => HOUR_SECONDS * 1);

        if (($cache = WebUtils::getDzPluginAppbymeAppConfig('cache_forumlist')) > 0) {
            $cacheInfo['expire'] = $cache;
        } else {
            $cacheInfo['enable'] = 0;
        }

        return $cacheInfo;
    }

    protected function getResult($params=array()) {
        $res = $this->initWebApiArray();

        $fid = (int)$params['fid'];
        $type = $params['type'];
        // $res['list'] = $this->_getForumList($fid);

        $forumList = $this->_getForumList($fid, $type);
        if ($type == 'rec') {
            $res['focusBoard'] = $forumList['focusBoard'];
            $res['recommendedBoard'] = $forumList['recommendedBoard'];  // 推荐板块
        } else {
            $res['list'] = $forumList;
        }

        $res['online_user_num'] = 0;
        $res['td_visitors'] = 0;

        return $res;
    }

    private function _getForumList($fid, $type) {
        require_once libfile('function/forumlist');

        $forumList = array();

        // 关注的板块
        $focusBoardIds = $this->_getFocusBoard();
        $params = array('focusBoardIds' => $focusBoardIds);

        // 子版块
        if ($fid > 0) {
            $tempForum = array();
            $tempForum['board_category_id'] = $fid;
            $tempForum['board_category_name'] = WebUtils::emptyHtml(DzForumForum::getNameByFid($fid));
            $tempForum['board_category_type'] = 1;
            $forums = ForumUtils::getForumSubList($fid);
            foreach ($forums as $forum) {
                $tempForum['board_list'][] = $this->_getForumInfo($forum, $params);
            }
            $forumList[] = $tempForum;
        } else {
            $forumColumnStyle = WebUtils::getDzPluginAppbymeAppConfig('dzsyscache_forum_column_style');
            $groups = ForumUtils::getForumGroupList();
            foreach ($groups as $group) {
                $gid = (int)$group['fid'];
                $tempGroup = array();
                $tempGroup['board_category_id'] = $gid;
                $tempGroup['board_category_name'] = WebUtils::emptyHtml($group['name']);
                $tempGroup['board_category_type'] = isset($forumColumnStyle[$gid]) ? (int)$forumColumnStyle[$gid] : 2;

                $forums = ForumUtils::getForumList($group['fid']);
                foreach ($forums as $forum) {
                    $tempGroup['board_list'][] = $this->_getForumInfo($forum, $params);
                }
                $forumList[] = $tempGroup;
            }
        }

        $fidList = ForumUtils::getForumShowFids();
        $imgFidList = ForumUtils::getForumImageShowFids();

        $tempGroupList = array();
        foreach ($forumList as $key => $group) {
            $tempForumList = array();
            foreach ($group['board_list'] as $forum) {
                if (in_array($forum['board_id'], $fidList)) {
                    if (!in_array($forum['board_id'], $imgFidList)) {
                        $forum['board_img'] = '';
                    }
                    $tempForumList[] = $forum;
                }
            }
            if (!empty($tempForumList)) {
                $tempGroup = $group;
                $tempGroup['board_list'] = $tempForumList;
                $tempGroupList[] = $tempGroup;
            }
        }
        $forumList = $tempGroupList;

        if ($type == 'rec') {
            // 推荐板块 按照总帖子进行排序
            $recommendedBoard = $topicTotalNum = $focusBoard = array();
            if ($fid == 0) {       
                foreach ($forumList as $k => $v) {
                    $board = $forumList[$k]['board_list'];
                    $recommendedBoard = array_merge($recommendedBoard, $board);
                }

                foreach($recommendedBoard as $k => $v ) {
                    $topicTotalNum[] = $v['topic_total_num'];
                    if (in_array($v['board_id'], $focusBoardIds)) {
                        $focusBoard[] = $v;
                    }
                }
                array_multisort($topicTotalNum, SORT_DESC, $recommendedBoard);

                $recommendedBoard = array_slice($recommendedBoard, 0, 5);
            }

            return array('forumList' => $forumList, 'focusBoard' => $focusBoard, 'recommendedBoard' => $recommendedBoard);
        }

        return $forumList;
    }

    private function _getForumInfo($forum, $params=array()) {
        extract($params);
        $fid = (int)$forum['fid'];
        $forum = array_merge($forum, DzForumForum::getForumFieldByFid($fid));

        $dateline = $this->_getDateLine($forum);

        // 判断该版块是否有权限访问
        if (!forum($forum))  {
            return array();
        }

        $matches = array();
        preg_match('/<img src="(.+?)"/', $forum['icon'], $matches);
        $image = !empty($matches[1]) ? $matches[1] : '';
        // $dateline = '0';
        // if (is_array($forum['lastpost'])) {
        //     $matches = array();
        //     preg_match('/<span title="(.+?)"/', $forum['lastpost']['dateline'], $matches);
        //     $dateline = !empty($matches[1]) ? $matches[1] : $forum['lastpost']['dateline'];
        //     $dateline = strtotime($dateline);
        //     $dateline !== false or $dateline = '0';
        // }

        $forumSubList = ForumUtils::getForumSubList($fid);

        $forumInfo = array();
        $forumInfo['board_id'] = (int)$fid;
        $forumInfo['board_name'] = WebUtils::emptyHtml($forum['name']);
        $forumInfo['description'] = (string)WebUtils::emptyHtml($forum['description']);
        $forumInfo['board_child'] = count($forumSubList) > 0 ? 1 : 0;
        $forumInfo['board_img'] = WebUtils::getHttpFileName($image);
        $forumInfo['last_posts_date'] = !empty($dateline) ? $dateline . '000' : '';
        $forumInfo['board_content'] = $forum['threads'] != 0 && !($forum['simple']&1) ? 1 : 0;
        $forumInfo['forumRedirect'] = $forum['redirect'];

        $todayPosts = (int)$forum['todayposts'];
        $threads = (int)$forum['threads'];
        $posts = (int)$forum['posts'];
        foreach ($forumSubList as $value) {
            $todayPosts += $value['todayposts'];
            $threads += $value['threads'];
            $posts += $value['posts'];
        }
        $forumInfo['td_posts_num'] = $todayPosts;
        $forumInfo['topic_total_num'] = $threads;
        $forumInfo['posts_total_num'] = $posts;
        $forumInfo['is_focus'] = in_array($fid, $focusBoardIds) ? 1 : 0 ;

        return $forumInfo;
    }

    private function _getDateLine($forum) {
        $lastpost = array(0, 0, '', '');
        $forum['lastpost'] = is_string($forum['lastpost']) ? explode("\t", $forum['lastpost']) : $forum['lastpost'];
        $forum['lastpost'] = count($forum['lastpost']) != 4 ? $lastpost : $forum['lastpost'];
        list($lastpost['tid'], $lastpost['subject'], $lastpost['dateline'], $lastpost['author']) = $forum['lastpost'];
        return $lastpost['dateline'];
    }

    private function _getFocusBoard() {
        global $_G;
        $focusBoardIds = array();
        if (!empty($_G['uid'])) {
            $offset = 0;
            $max = 10;
            $idtype = 'fid';
            $focusBoard = C::t('home_favorite')->fetch_all_by_uid_idtype($_G['uid'], $idtype, 0, $offset, $pageSize);

            foreach ($focusBoard as $v) {
                $focusBoardIds[] = $v['id'];
            }
        }
        return $focusBoardIds;
    }

}
