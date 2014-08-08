<?php

/**
 * 主题详情接口
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

// Mobcent::setErrors();
class PostListAction extends MobcentAction {

    public function run($topicId, $authorId=0, $order=0, $page=1, $pageSize=10) {
        $tid = (int)$topicId;

        global $_G;
        $key = "{$this->id}_{$_G['groupid']}_{$tid}_{$authorId}_{$order}_{$page}_{$pageSize}";

        $this->runWithCache($key, array('tid' => $tid, 'page' => $page, 'pageSize' => $pageSize, 'order' => $order, 'authorId' => $authorId));
        
        // Mobcent::dumpSql();
    }

    protected function getCacheInfo() {
        return Yii::app()->params['mobcent']['cache']['postList'];
    }

    protected function getResult($params=array()) {
        extract($params);

        $res = WebUtils::initWebApiArray();
        $res = array_merge(array('rs' => 1, 'errcode' => ''), $res);
        
        $topic = ForumUtils::getTopicInfo($tid);
        if (empty($topic)) {
            return $this->_makeErrorInfo($res, 'thread_nonexistence');
        }
        
        // 该主题是由别的版块移动过来的
        if($topic['closed'] > 1) {
            $tid = $topic['closed'];
            $topic['tid'] = $tid;
        }

        $app = Yii::app()->getController()->mobcentDiscuzApp;
        $app->loadForum($topic['fid'], $topic['tid']);
        
        // 检查权限
        global $_G;
        if(empty($_G['forum']['allowview'])) {
            if(!$_G['forum']['viewperm'] && !$_G['group']['readaccess']) {
                return $this->_makeErrorInfo($res, 'group_nopermission', array(
                    '{grouptitle}' => $_G['group']['grouptitle'],
                ));
            } elseif($_G['forum']['viewperm'] && !forumperm($_G['forum']['viewperm'])) {
                $msg = mobcent_showmessagenoperm('viewperm', $_G['fid']);
                return $this->_makeErrorInfo($res, $msg['message'], $msg['params']);
            }

        } elseif($_G['forum']['allowview'] == -1) {
            return $this->_makeErrorInfo($res, 'forum_access_view_disallow');
        }

        if($_G['forum']['formulaperm']) {
            $msg = mobcent_formulaperm($_G['forum']['formulaperm']);
            if ($msg['message'] != '') {
                return $this->_makeErrorInfo($res, $msg['message'], $msg['params']);
            }
        }

        // if($_G['forum']['password'] && $_G['forum']['password'] != $_G['cookie']['fidpw'.$_G['fid']]) {
            // dheader("Location: $_G[siteurl]forum.php?mod=forumdisplay&fid=$_G[fid]");
        // }
        if ($_G['forum']['password']) {
            return $this->_makeErrorInfo($res, 'mobcent_forum_passwd');
        }

        if($_G['forum']['price'] && !$_G['forum']['ismoderator']) {
            $membercredits = C::t('common_member_forum_buylog')->get_credits($_G['uid'], $_G['fid']);
            $paycredits = $_G['forum']['price'] - $membercredits;
            if($paycredits > 0) {
                // dheader("Location: $_G[siteurl]forum.php?mod=forumdisplay&fid=$_G[fid]");
            }
        }

        if ($_G['forum_thread']['readperm'] && $_G['forum_thread']['readperm'] > $_G['group']['readaccess'] && !$_G['forum']['ismoderator'] && $_G['forum_thread']['authorid'] != $_G['uid']) {
            return $this->_makeErrorInfo($res, 'thread_nopermission', array(
                '{readperm}' => $_G['forum_thread']['readperm'],
            ));
        }

        if ($page <= 1 && ($authorId == 0 || $authorId == $topic['authorid'])) {
            $res['topic'] = $this->_getTopicInfo($topic);
            if (empty($res['topic'])) {
                return $this->_makeErrorInfo($res, 'post_not_found');
            }
        }
    
        $res = $this->_getPostInfos($res, $topic, $page, $pageSize, $order, $authorId);

        $res['forumName'] = WebUtils::emptyHtml($_G['forum']['name']);
        $res['forumTopicUrl'] = Yii::app()->getController()->dzRootUrl . "/forum.php?mod=viewthread&tid=" . $tid;
        $res['img_url'] = '';
        $res['icon_url'] = '';

        Mobcent::import(sprintf(
            '%s/forum_viewthread_%s.php', 
            MOBCENT_APP_ROOT . '/components/discuz/forum', 
            MobcentDiscuz::getMobcentDiscuzVersion()
        ));
        viewthread_updateviews($_G['forum_thread']['threadtableid']);

        return $res;
    }

    private function _makeErrorInfo($res, $message, $params=array()) {
        return WebUtils::makeErrorInfo_oldVersion($res, $message, $params);
    }

    private function _getTopicInfo($topic) {
        $topicInfo = array();

        $tid = (int)$topic['tid'];
        if (!empty($topic)) {
            $post = ForumUtils::getTopicPostInfo($tid);
            if (!empty($post)) {
                $content = ForumUtils::getTopicContent($tid, $post);
                $sort = $this->_getSortInfo($topic);

                global $_G;
                $typeTitle = $sortTitle = '';
                if($_G['forum_thread']['typeid'] && $_G['forum']['threadtypes']['types'][$_G['forum_thread']['typeid']]) { 
                    if(!IS_ROBOT && ($_G['forum']['threadtypes']['listable'] || $_G['forum']['status'] == 3)) {
                        $typeTitle = "[{$_G['forum']['threadtypes']['types'][$_G['forum_thread']['typeid']]}]";
                    } else {
                        $typeTitle = "[{$_G['forum']['threadtypes']['types'][$_G['forum_thread']['typeid']]}]";
                    } 
                } 
                if(!empty($sort['title'])) {
                    $sortTitle = "[{$sort['title']}]";
                }
                $topicInfo['topic_id'] = $tid;
                $topicInfo['title'] = $typeTitle . $sortTitle . $topic['subject'];
                $topicInfo['title'] = WebUtils::emptyHtml($topicInfo['title']);
                
                // 修正帖子查看数
                if (isset($_G['forum_thread']['views']) && 
                    $_G['forum_thread']['tid'] == $topic['tid'] &&
                    $_G['forum_thread']['views'] > $topic['views']) {
                    $topic['views'] = $_G['forum_thread']['views'];
                }

                $topicInfo['type'] = ForumUtils::getTopicType($topic);
                $topicInfo['sortId'] = (int)$topic['sortid'];
                $topicInfo['user_id'] = ForumUtils::isAnonymousPost($tid, $post) ? 0 : (int)$topic['authorid'];
                $topicInfo['user_nick_name'] = ForumUtils::isAnonymousPost($tid, $post) ? $this->_getAnonymoustext() : $topic['author'];
                $topicInfo['replies'] = (int)$topic['replies'];
                $topicInfo['hits'] = (int)($topic['replies'] > $topic['views'] ? $topic['replies'] : $topic['views']);
                $topicInfo['essence'] = ForumUtils::isMarrowTopic($topic) ? 1 : 0;
                $topicInfo['vote'] = ForumUtils::isVoteTopic($topic) ? 1 : 0;
                $topicInfo['hot'] = ForumUtils::isHotTopic($topic) ? 1 : 0;
                $topicInfo['top'] = ForumUtils::isTopTopic($topic) ? 1 : 0;
                $topicInfo['is_favor'] = ForumUtils::isFavoriteTopic($_G['uid'], $tid) ? 1 : 0;
                $topicInfo['create_date'] = $topic['dateline'] . '000';
                $topicInfo['icon'] = UserUtils::getUserAvatar($topicInfo['user_id']);
                $topicInfo['level'] = $this->_getUserLevel($topicInfo['user_id']);
                $topicInfo['userTitle'] = UserUtils::getUserTitle($topicInfo['user_id']);

                $postContent = $this->_getPostContent($content);
                if (!empty($sort['summary'])) {
                    array_unshift($postContent, $this->_transSortInfo($sort));
                }
                $topicInfo['content'] = $postContent;
                
                if ($topicInfo['type'] == 'normal') {
                    $this->_isComplexContent($topicInfo['content']) && $topicInfo['type'] = 'normal_complex';
                }

                $topicInfo['poll_info'] = ForumUtils::isVoteTopic($topic) ? $this->_getVoteInfo($topic) : null;
                $topicInfo['activityInfo'] = ForumUtils::isActivityTopic($topic) ? TopicUtils::getActivityInfo($topic) : null;
                $topicInfo['location'] = ForumUtils::getTopicLocation($tid);
                
                $manageItems = ForumUtils::getPostManagePanel();
                foreach ($manageItems['topic'] as $key => $item) {
                    $item['action'] = WebUtils::createUrl_oldVersion('forum/topicadminview', array('fid' => $post['fid'], 'tid' => $tid, 'pid' => $post['pid'], 'act' => $item['action'], 'type' => 'topic'));
                    $manageItems['topic'][$key] = $item;
                }
                $topicInfo['managePanel'] = $manageItems['topic'];

                $topicInfo['mobileSign'] = ForumUtils::getMobileSign($post['status']);
                
                // if (empty($topic['author']) && !empty($topic['authorid'])){
                //     $topicInfo['reply_status'] = -1;
                // } else if(empty($topic['author']) && empty($topic['authorid'])){
                //     $topicInfo['reply_status'] = 0;
                // } else {
                //     $topicInfo['reply_status'] = 1;
                // }
                $topicInfo['status'] = 1;
                $topicInfo['reply_status'] = 1;

                $topicInfo['flag'] = 0;
                $topicInfo['gender'] = 1;
                $topicInfo['reply_posts_id'] = 0;
            }
        }

        return $topicInfo;
    }

    // 如果内容有复杂类型，要让客户端不能编辑
    private function _isComplexContent($content) {
        $complex = false;
        foreach ($content as $line) {
            // if ($line['type'] != 0 && !($line['type'] == 1 && $line['aid'] == 0)) {
            // 暂时也把有图片附件屏蔽
            if ($line['type'] != 0) {
                $complex = true;
                break;
            }
        }
        return $complex;
    }

    private function _getVoteInfo($tid) {
        $vote = array();

        $voteInfo = ForumUtils::getTopicVoteInfo($tid);

        $vote['deadline'] = $voteInfo['expiration'];
        $vote['is_visible'] = $voteInfo['visiblepoll'] ? 1 : 0;
        $vote['voters'] = (int)$voteInfo['voterscount'];
        $vote['type'] = (int)$voteInfo['maxchoices'];
        $vote['poll_status'] = $voteInfo['status'];
        $vote['poll_id'] = array(0);

        foreach ($voteInfo['options'] as $option) {
            $voteItem['name'] = $option['polloption'];
            $voteItem['poll_item_id'] = (int)$option['polloptionid'];
            $voteItem['total_num'] = (int)$option['votes'];
            $voteItem['percent'] = $option['percent'] . '%';
            $vote['poll_item_list'][] = $voteItem; 
        }

        return $vote;
    }

    // 获取分类信息
    private function _getSortInfo($thread) {
        // from forum_viewthread template
        $sort = array('title' => '', 'summary' => '');

        global $_G;
        $threadsort = $thread['sortid'] && isset($_G['forum']['threadsorts']['types'][$thread['sortid']]) ? 1 : 0;
        if($threadsort) {
            require_once libfile('function/threadsort');
            $threadsortshow = threadsortshow($thread['sortid'], $_G['tid']);
        }
        
        if($threadsort && $threadsortshow) {
            if($threadsortshow['typetemplate']) {
                //echo $threadsortshow['typetemplate'];
                $sort = $this->_getSortByTemplate($threadsortshow);
            } elseif ($threadsortshow['optionlist']) {
                if($threadsortshow['optionlist'] == 'expire') {
                    $sort['summary'] = WebUtils::t("该信息已经过期\n");
                } else {
                    $sort['title'] = $_G['forum']['threadsorts']['types'][$_G['forum_thread']['sortid']];
                    if (is_array($threadsortshow['optionlist'])) {
                        foreach($threadsortshow['optionlist'] as $option) { 
                            if($option['type'] != 'info') {
                                $sort['summary'] .= sprintf("%s :\t", $option['title']);
                                if ($option['value'] || ($option['type'] == 'number' && $option['value'] !== '')) { 
                                    $matches = array();
                                    preg_match('/action=protectsort.+?optionid=(\d+)/', $option['value'], $matches);
                                    if (!empty($matches)) {
                                        $option['value'] = $this->_getProtectSortOptionListValue($_G['tid'], $matches[1]);
                                    } else {
                                        $option['value'] = WebUtils::emptyHtml($option['value']);
                                    }
                                    $sort['summary'] .= $option['value'] . $option['unit'];
                                }
                                $sort['summary'] .= "\n";
                            }
                        }
                    }
                }
            }
        }

        return $sort;
    } 

    // 获取分类信息保护字段的值
    private function _getProtectSortOptionListValue($tid, $optionid) {
        $value = '';
        
        $typeoptionvarvalue = C::t('forum_typeoptionvar')->fetch_all_by_tid_optionid($tid, $optionid);
        $typeoptionvarvalue[0]['expiration'] = $typeoptionvarvalue[0]['expiration'] && $typeoptionvarvalue[0]['expiration'] <= TIMESTAMP ? 1 : 0;
        $option = C::t('forum_typeoption')->fetch($optionid);

        if (($option['expiration'] && !$typeoptionvarvalue[0]['expiration']) || empty($option['expiration'])) {
            $protect = dunserialize($option['protect']);
            include_once libfile('function/threadsort');
            if (protectguard($protect)) {
                if (empty($option['permprompt'])) {
                    $value = lang('forum/misc', 'view_noperm');
                } else {
                    $value = $option['permprompt'];
                }
            } else {
                $value = nl2br($typeoptionvarvalue[0]['value']);
            }
        } else {
            $value = lang('forum/misc', 'has_expired');
        }
        return $value;
    }

    private function _getSortByTemplate($threadsortshow) {
        Mobcent::import(MOBCENT_APP_ROOT . '/components/discuz/template/SortTemplate.php');
        $template = new SortTemplate;
        return $template->getTopicSort($threadsortshow);
    }

    private function _transSortInfo($sort) {
        if ($sort['summary'] != '') {
            $sort = array('infor' => $sort['summary'], 'type' => 0);
        } else {
            $sort = array();
        }
        return $sort;
    }

    private function _getPostInfos($res, $topic, $page, $pageSize, $order, $authorId) {
        $postInfos = array();

        $tid = (int)$topic['tid'];
        $postCount = ForumUtils::getPostCount($tid, array('authorId' => $authorId));

        if (!empty($topic)) {
            global $_G;
            $isOnlyAuthorTopic = ForumUtils::isOnlyAuthorTopic($topic);
            $postList = ForumUtils::getPostList($tid, $page, $pageSize, 
                array(
                    'order' => $order ? 'dateline DESC' : 'dateline ASC', 
                    'authorId' => $authorId
                )
            );
            $position = $order ? 
                $postCount + 1 - $pageSize*($page-1) : 
                $pageSize*($page-1) + 2;
            
            foreach($postList as $post) {
                $pid = (int)$post['pid'];
                $content = ForumUtils::getPostContent($tid, $pid, $post);

                $isOnlyAuthorPost = $isOnlyAuthorTopic && $_G['uid'] != $post['authorid'] && $_G['uid'] != $_G['forum_thread']['authorid'] && !$post['first'] && !$_G['forum']['ismoderator'];

                $postInfo['reply_id'] = ForumUtils::isAnonymousPost($tid, $post) ? 0 : (int)$post['authorid'];
                $postInfo['reply_content'] = $isOnlyAuthorPost ? $this->_filterContent(array(array('content' => WebUtils::t('此帖仅作者可见'), 'type' => 'text'))) : $this->_getPostContent($content);
                $postInfo['reply_type'] = 'normal';
                $this->_isComplexContent($postInfo['reply_content']) && $postInfo['reply_type'] = 'normal_complex';

                $postInfo['reply_name'] = ForumUtils::isAnonymousPost($tid, $post) ? $this->_getAnonymoustext() : $post['author'];
                $postInfo['reply_posts_id'] = $pid;

                // 抢楼帖时采用原楼层
                if (getstatus($topic['status'], 3)) {
                    $postInfo['position'] = $post['position'];    
                } else {
                    $postInfo['position'] = $order ? $position-- : $position++;
                }
                
                $postInfo['posts_date'] = $post['dateline'] . '000';
                
                $postInfo['icon'] = UserUtils::getUserAvatar($postInfo['reply_id']);

                $postInfo['level'] = $this->_getUserLevel($postInfo['reply_id']);
                $postInfo['userTitle'] = UserUtils::getUserTitle($postInfo['reply_id']);

                $postInfo['location'] = ForumUtils::getPostLocation($pid);
                $postInfo['mobileSign'] = ForumUtils::getMobileSign($post['status']);

                // if(empty($post['author']) && isset($post['authorid']) && !empty($post['authorid'])){
                //     $postInfo['reply_status'] = -1;
                // }elseif(empty($post ['author']) && empty($post ['authorid'])){
                //     $postInfo['reply_status'] = 0;
                // }else{
                //     $postInfo['reply_status'] = 1;
                // }
                $postInfo['reply_status'] = 1;
                $postInfo['status'] = 1;
                $postInfo['role_num'] = 1;
                $postInfo['title'] = '';

                $postInfo = array_merge($postInfo, $this->_getPostQuoteInfo($content));

                $manageItems = ForumUtils::getPostManagePanel();
                foreach ($manageItems['post'] as $key => $item) {
                    $item['action'] = WebUtils::createUrl_oldVersion('forum/topicadminview', array('fid' => $post['fid'], 'tid' => $tid, 'pid' => $post['pid'], 'act' => $item['action'], 'type' => 'post'));
                    $manageItems['post'][$key] = $item;
                }
                $postInfo['managePanel'] = $manageItems['post'];

                $postInfos[] = $postInfo;
            }
        }

        $res = WebUtils::getWebApiArrayWithPage_oldVersion($page, $pageSize, $postCount, $res);
        $res['list'] = $postInfos;
        return $res;
    }

    private function _getAnonymoustext() {
        return MobcentDiscuz::getDiscuzCommonSetting('anonymoustext');
    }

    private function _getPostContent($content) {
        return !empty($content['main']) ? $this->_filterContent($content['main']) : array();
    }

    private function _getPostQuoteInfo($content) {
        $postInfo['is_quote'] = !empty($content['quote']) ? 1 : 0;
        $postInfo['quote_pid'] = 0;
        $postInfo['quote_content'] = !empty($content['quote']) ? $content['quote']['who']."\n".$content['quote']['msg']: '';
        $postInfo['quote_user_name'] = '';
        return $postInfo;
    }

    private function _filterContent($content) {
        $typeMaps = array('text' => 0, 'image' => 1, 'video' => 2, 'audio' => 3, 'url' => 4, 'attachment' => 5,);
        $newContent = array();
        foreach ($content as $key => $value) {
            $newContent[$key]['infor'] = $value['content'];
            $newContent[$key]['type'] = $typeMaps[$value['type']];
            if ($value['type'] == 'image') {
                $newContent[$key]['originalInfo'] = $value['content'];
                $newContent[$key]['aid'] = isset($value['extraInfo']['aid']) ? $value['extraInfo']['aid'] : 0;
                $newContent[$key]['infor'] = ImageUtils::getThumbImage($value['content']);
            } else if ($value['type'] == 'url') {
                $newContent[$key]['infor'] = $value['content'];
                $newContent[$key]['url'] = $value['extraInfo']['url'];
            } else if ($value['type'] == 'attachment') {
                $newContent[$key]['infor'] = $value['content'];
                $newContent[$key]['url'] = $value['extraInfo']['url'];  
                $newContent[$key]['desc'] = $value['extraInfo']['desc'];  
            }
        }
        return $newContent;
    }

    private function _getUserLevel($uid) {
        $icon = UserUtils::getUserLevelIcon($uid);
        return $icon['sun'] * 4 + $icon['moon'] * 2 + $icon['star'] * 1;
    }
}