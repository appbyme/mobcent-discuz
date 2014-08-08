<?php

/**
 * 活动帖html视图接口
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class TopicActivityViewAction extends CAction {

    public function run($tid, $act='apply') {
        $data = TopicUtils::getActivityInfo($tid);
        $data = $data['action']['info'];

        $errorMsg = '';
        if (!empty($_POST)) {
            // discuz 源码会在mobile情况下把POST的数据转码成对应的charset, 
            // 由于这里需要强制使用utf-8,且dz本身并没有修改$_REQUEST变量
            $_POST = array_intersect_key($_REQUEST, $_POST);
            $requestData = WebUtils::jsonEncode($_POST, 'utf-8');
            $res = WebUtils::httpRequestAppAPI('forum/topicactivity', array(
                'tid' => $tid,
                'act' => $act,
                'json' => rawurlencode($requestData)
            ));
            
            if (($res = WebUtils::jsonDecode($res)) != false && $res['head']['errCode'] == MOBCENT_ERROR_NONE) {
                $this->getController()->redirect(WebUtils::createUrl_oldVersion('index/returnmobileview'));
            }
            if ($res != false) {
                $errorMsg = $res['head']['errInfo'];
            }
        }

        // render
        $viewFile = 'topicActivity';
        $this->getController()->renderPartial($viewFile, array(
            'data' => $data,
            'errorMsg' => $errorMsg,
            'formUrl' => WebUtils::createUrl_oldVersion('forum/topicactivityview', array('tid' => $tid, 'act' => $act)),
        ));
    }
}