<?php

/**
 * 提醒接口
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class NotifyListAction extends CAction {

	public function run($type = 'post', $page = 1, $pageSize = 20) {
        $res = WebUtils::initWebApiArray_oldVersion();

		$uid = $this->getController()->uid;

		$notifyInfo = $this->_getNotifyInfo($uid, $type, $page, $pageSize);
		$list = $notifyInfo['list'];
		$count = $notifyInfo['count'];
		
        $res = array_merge($res, WebUtils::getWebApiArrayWithPage_oldVersion(
            $page, $pageSize, $count));
		$res['list'] = $list;
        
		// $transaction = Yii::app()->dbDz->beginTransaction();
		// try {
            echo WebUtils::outputWebApi($res, '', false);

			$this->_updateReadStatus($uid, $type);
			
		// 	$transaction->commit();
		// } catch(Exception $e) {
		// 	var_dump($e);
		//     $transaction->rollback();
		// }

		Yii::app()->end();
	}

	private function _getNotifyInfo($uid, $type, $page, $pageSize) {
		$info = array(
			'count' => 0,
			'list' => array(),
		);

		$count = DzHomeNotification::getCountByUid($uid, $type);
        $notifyData = DzHomeNotification::getAllNotifyByUid($uid, $type, $page, $pageSize);
        foreach ($notifyData as $data) {
        	$matches = array();
        	preg_match_all('/&ptid=(\d+?)&pid=(\d+?)"/i', $data['note'], $matches);
        	$ptid = $matches[1][0];
            $pid = $matches[2][0];
        	$postInfo = $this->_getPostInfo($ptid, $pid);
        	if (!empty($postInfo)) {
	        	$info['list'][] = $postInfo;
        	} else {
        		--$count;
        	}
        }
		$info['count'] = $count;

		return $info;
	}

	private function _getPostInfo($tid, $pid) {
		$info = array();

        $post = ForumUtils::getPostInfo($tid, $pid);
        if (!empty($post)) {
            $forumName = ForumUtils::getForumName($post['fid']);
        	$threadPost = ForumUtils::getTopicPostInfo($tid);

            $topicContent = ForumUtils::getTopicContent($tid, $threadPost);
            $postContent = ForumUtils::getPostContent($tid, $pid, $post);

            $content = $this->_getContent($postContent, $topicContent);

        	$info['board_name'] = $forumName;
            $info['board_id'] = (int)$post['fid'];
            
        	$info['topic_id'] = (int)$tid;
        	$info['topic_subject'] = $threadPost['subject'];
        	$info['topic_content'] = $content['topic'];
        	$info['topic_url'] = '';

        	$info['reply_content'] = $content['reply'];
        	$info['reply_url'] = '';
        	$info['reply_remind_id'] = (int)$pid;
        	$info['reply_nick_name'] = $post['author'];
        	$info['user_id'] = (int)$post['authorid'];
        	$info['icon'] = UserUtils::getUserAvatar($post['authorid']);
        	$info['is_read'] = 1;
        	$info['replied_date'] = $post['dateline'] . '000';
        }

        return $info;
	}

    /**
     * copy from Discuz
     */
	private function _updateReadStatus($uid, $type) {
        // call_user_func(array($this, MobcentDiscuz::getFuncNameWithVersion(__FUNCTION__)), $uid, $type);
	    DzHomeNotification::updateReadStatus($uid);
        DbUtils::getDzDbUtils(true)->query('
            UPDATE %t
            SET newprompt=0
            WHERE uid=%d
            ', 
            array('common_member', $uid)
        );
    }

    private function _getContent($postContent, $topicContent) {
        $content = array('topic' => '', 'reply' => '');
        if (!empty($postContent['main'])) {
            $content['reply'] = $this->_transContent($postContent['main']);
        }
        if (!empty($postContent['quote'])) {
            $content['topic'] = WebUtils::subString($postContent['quote']['msg'], 0, Yii::app()->params['mobcent']['forum']['post']['summaryLength']);
        } else {
            $content['topic'] = $this->_transContent($topicContent['main']);
        }

        return $content;
    }

    private function _transContent($content) {
        $msg = '';
        if (!empty($content)) {
            foreach ($content as $line) {
                if ($line['type'] == 'text') {
                    $msg .= $line['content'] . "\r\n";
                }
            }
            $msg = preg_replace('/\[mobcent_phiz=.+?\]/', '', $msg);
            $length = Yii::app()->params['mobcent']['forum']['post']['summaryLength'];
            $msg = WebUtils::subString($msg, 0, $length);
        }
        return $msg;  
    }
}