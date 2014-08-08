<?php

/**
 * 评论管理接口
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class CommentAdminAction extends MobcentAction {

    public function run($json) {
        $res = WebUtils::initWebApiArray_oldVersion();

        // $json = "{'action': 'reply', 'idType': 'aid', 'id': 1, 'content': [{'type': 0, 'infor': '呵呵\r\nhaha%25E5%2591%25B5%25E5%2591%25B5%25E2%2580%259C%25E2%2580%259D%2522%2522',}], 'quoteCommentId': 12, }";
        $json = rawurldecode($json);
        $json = WebUtils::jsonDecode($json);
        
        !isset($json['action']) && $json['action'] = 'reply';
        !isset($json['idType']) && $json['idType'] = 'aid';
        switch ($json['action']) {
            case 'reply': $res = $this->_commentReply($res, $json); break;
            default: $res = WebUtils::makeErrorInfo_oldVersion($res, 'mobcent_error_params'); break;
        }

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _commentReply($res, $data) {
        global $_G;

        require DISCUZ_ROOT.'./source/function/function_home.php';
        
        require_once libfile('function/portalcp');

        // 在DISCUZ_ROOT/source/include/portalcp/portalcp_comment.php基础上二次开发
        if (!checkperm('allowcommentarticle')) {
            return $this->makeErrorInfo($res, 'group_nopermission', array('{grouptitle}' => $_G['group']['grouptitle']), array('login' => 1));
        }

        switch ($data['idType']) {
            case 'aid': $_POST['aid'] = $data['id']; break;
            case 'tid': $_POST['topicid'] = $data['id']; break;
            default: return $this->makeErrorInfo($res, 'mobcent_error_params');
        }
    
        $id = 0;
        $idtype = '';
        if(!empty($_POST['aid'])) {
            $id = intval($_POST['aid']);
            $idtype = 'aid';
        } elseif(!empty($_POST['topicid'])) {
            $id = intval($_POST['topicid']);
            $idtype = 'topicid';
        }

        // 获取评论内容
        $_POST['message'] = $commentText = '';
        foreach ($data['content'] as $line) {            
            $line['type'] = $this->_transCommentType($line['type']);

            // 引用评论
            if (isset($data['quoteCommentId']) && $data['quoteCommentId'] > 0) {
                $quoteComment = DzPortalComment::getCommentById($data['quoteCommentId']);
                if (!empty($quoteComment)) {
                    $commentText .= $this->_getCommentMessage($quoteComment);
                }
            }

            if ($line['type'] == 'text') {
                $line['infor'] = rawurldecode($line['infor']);
                $commentText .= WebUtils::t($line['infor']);
            }
        }
        $_POST['message'] = $commentText;

        $message = $_POST['message'];

        require_once libfile('function/spacecp');

        if (($checkMessage = mobcent_cknewuser()) != '') {
            return $this->makeErrorInfo($res, WebUtils::emptyHtml($checkMessage));
        }

        $waittime = interval_check('post');
        if ($waittime > 0) {
            return $this->makeErrorInfo($res, 'operating_too_fast', array('{waittime}' => $waittime), array('return' => true));
        }

        $retmessage = addportalarticlecomment($id, $message, $idtype);
        return $this->makeErrorInfo($res, $retmessage, array('noError' => ($retmessage == 'do_success' ? 1: 0)));
    }

    // 转换评论内容类型
    private function _transCommentType($type) {
        $typeMaps = array('text' => 0, 'image' => 1, 'video' => 2, 'audio' => 3, 'url' => 4, 'attachment' => 5,);
        $typeMaps = array_flip($typeMaps);
        if (!array_key_exists($type, $typeMaps)) {
            return false;
        }
        return $typeMaps[$type];
    }

    // 获取引用评论内容
    private function _getCommentMessage($quoteComment) {
        $quoteComment['message'] = WebUtils::emptyReturnLine($quoteComment['message'], ' ');
        $quoteComment['message'] = preg_replace('/<blockquote>.*?<\/blockquote>/s', '', $quoteComment['message']);
        $quoteComment['message'] = preg_replace('/<div class="quote">.*?<\/div>/s', '', $quoteComment['message']);
        $quoteComment['message'] = WebUtils::emptyHtml($quoteComment['message']);
        $quoteComment['message'] = $quoteComment['username'] . ': ' . $quoteComment['message'];
        $quoteComment['message'] = '[quote]' . $quoteComment['message'] . "[/quote]\r\n";
        return $quoteComment['message'];
    }
}