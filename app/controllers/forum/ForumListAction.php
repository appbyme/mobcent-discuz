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

    public function run($fid=0) {
        global $_G;
        $key = CacheUtils::getForumListKey(array($fid, $_G['groupid']));
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
        $res['list'] = $this->_getForumList($fid);

        $res['online_user_num'] = 0;
        $res['td_visitors'] = 0;

        return $res;
    }

    private function _getForumList($fid) {
        require_once libfile('function/forumlist');

        $forumList = array();

        // 子版块
        if ($fid > 0) {
            $tempForum = array();
            $tempForum['board_category_id'] = $fid;
            $tempForum['board_category_name'] = WebUtils::emptyHtml(DzForumForum::getNameByFid($fid));
            $tempForum['board_category_type'] = 1;
            $forums = ForumUtils::getForumSubList($fid);
            foreach ($forums as $forum) {
                $tempForum['board_list'][] = $this->_getForumInfo($forum);
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
                    $tempGroup['board_list'][] = $this->_getForumInfo($forum);
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

        return $forumList;
    }

    private function _getForumInfo($forum) {
        $fid = (int)$forum['fid'];
        $forum = array_merge($forum, DzForumForum::getForumFieldByFid($fid));

        $dateline = $this->_getDateLine($forum);

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

        return $forumInfo;
    }

    private function _getDateLine($forum) {
        $lastpost = array(0, 0, '', '');
        $forum['lastpost'] = is_string($forum['lastpost']) ? explode("\t", $forum['lastpost']) : $forum['lastpost'];
        $forum['lastpost'] = count($forum['lastpost']) != 4 ? $lastpost : $forum['lastpost'];
        list($lastpost['tid'], $lastpost['subject'], $lastpost['dateline'], $lastpost['author']) = $forum['lastpost'];
        return $lastpost['dateline'];
    }

}
