<?php

/**
 * 评论列表接口
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class CommentListAction extends MobcentAction {

    public function run($json) {
        $res = WebUtils::initWebApiArray_oldVersion();

        // $json = "{'id': 1, 'idType': 'aid', 'page': 1, 'pageSize': 10, }";
        $json = rawurldecode($json);
        $json = WebUtils::jsonDecode($json);

        $res = $this->_checkComment($res, $json);
        if (!WebUtils::checkError($res)) {
            $comments = $this->getCommentList($json);
            $res['body']['list'] = $comments['list'];
            $res = array_merge($res, WebUtils::getWebApiArrayWithPage($res, $json['page'], $json['pageSize'], $comments['count']));
        }

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _checkComment($res, $data) {
        $_GET['id'] = $data['id'];
        $_GET['idType'] = $data['idType'];
        
        // 在DISCUZ_ROOT/source/module/portal/portal_comment.php基础上二次开发
        $id = empty($_GET['id']) ? 0 : intval($_GET['id']);
        $idtype = in_array($_GET['idtype'], array('aid', 'topicid')) ? $_GET['idtype'] : 'aid';
        if(empty($id)) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'comment_no_'.$idtype.'_id');
        }
        if($idtype == 'aid') {
            $csubject = C::t('portal_article_title')->fetch($id);
            if($csubject) {
                $csubject = array_merge($csubject, C::t('portal_article_count')->fetch($id));
            }
            // $url = fetch_article_url($csubject);
        } elseif($idtype == 'topicid') {
            $csubject = C::t('portal_topic')->fetch($id);
            // $url = fetch_topic_url($csubject);
        }

        if(empty($csubject)) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'comment_'.$idtype.'_no_exist');
        } elseif(empty($csubject['allowcomment'])) {
            return WebUtils::makeErrorInfo_oldVersion($res, $idtype.'_comment_is_forbidden');
        }

        return $res;
    }

    protected function getCommentList($data) {
        $res = array('list' => array(), 'count' => 0);

        $list = array();
        $comments = DzPortalComment::getComments($data['id'], $data['idType'], $data['page'], $data['pageSize']);
        foreach ($comments as $comment) {
            $tmpComment = array();
            $tmpComment['managePanel'] = array(
                array('type' => 'quote', 'action' => '', 'title' => WebUtils::t('引用'))
            );
            $tmpComment['id'] = (int)$comment['cid'];
            $tmpComment['uid'] = (int)$comment['uid'];
            $tmpComment['username'] = $comment['username'];
            $tmpComment['avatar'] = UserUtils::getUserAvatar($comment['uid']);
            $tmpComment['time'] = date('Y-m-d H:i', $comment['dateline']);
            $tmpComment['content'] = $this->transCommentMessage($comment['message']);

            $list[] = $tmpComment;
        }
        $res['list'] = $list;
        $res['count'] = DzPortalComment::getCount($data['id'], $data['idType']);

        return $res;
    }

    // 把数据库里面的message字段转换成客户端需要的格式
    protected function transCommentMessage($message) {
        $contents = array();

        $message = WebUtils::emptyReturnLine($message);
        $message = explode('</blockquote></div>', $message);
        foreach ($message as $line) {
            $matches = array();
            preg_match('/(.*?)<div class="quote"><blockquote>(.*)/', $line, $matches);
            if (!empty($matches)) {
                $matches[1] != '' && $contents[] = $this->_makeContent('text', $matches[1]);
                $contents[] = $this->_makeContent('quoteText', $matches[2]);
            } else {
                $line != '' && $contents[] = $this->_makeContent('text', $line);
            }
        }

        return $contents;
    }

    private function _transCommentType($type) {
        $typeMaps = array('text' => 0, 'image' => 1, 'video' => 2, 'audio' => 3, 'url' => 4, 'attachment' => 5, 'quoteText' => 10);
        // $typeMaps = array_flip($typeMaps);
        if (!array_key_exists($type, $typeMaps)) {
            return false;
        }
        return $typeMaps[$type];
    }

    private function _makeContent($type, $infor) {
        return array('type' => $this->_transCommentType($type), 'infor' => $this->_emptyHtml($infor));
    }

    private function _emptyHtml($message) {
        $message = str_replace('<br />', "\r\n", $message);
        $message = WebUtils::emptyHtml($message);
        return $message;
    }
}