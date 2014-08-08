<?php

/**
 * 活动帖接口
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class TopicActivityAction extends CAction {

    public function run($tid, $act='apply') {
        $res = WebUtils::initWebApiArray_oldVersion();

        $uid = $this->getController()->uid;
        
        // $_REQUEST['json'] = "{'payment':1,'payvalue':100, 'realname': '请求参数11', 'qq': '8', 'message': '请求参数'}";
        $json = isset($_REQUEST['json']) ? $_REQUEST['json'] : '';
        $json = rawurldecode($json);
        $data = WebUtils::jsonDecode($json);

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                if (is_string($value)) {
                    $data[$key] = WebUtils::t($value);
                }
            }

            switch ($act) {
                case 'apply': $res = $this->_applyActivityTopic($res, $tid, $uid, $data); break;
                case 'cancel': $res = $this->_cancelActivityTopic($res, $tid, $uid, $data); break;
                default: $res = $this->_makeErrorInfo($res, 'activity_apply_params_error'); break;
            }
        } else {
            $res = $this->_makeErrorInfo($res, 'activity_apply_params_error');
        }

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _applyActivityTopic($res, $tid, $uid, $data) {
        // from forum_misc.php
        require_once libfile('function/post');
        
        $activity = DzForumActivity::getActivityByTid($tid);
        if($activity['expiration'] && $activity['expiration'] < TIMESTAMP) {
            return $this->_makeErrorInfo($res, 'activity_stop');
        }

        $applyinfo = DzForumActivityApply::getApplyByTidUid($tid, $uid);
        if($applyinfo && $applyinfo['verified'] < DzForumActivityApply::STATUS_VERIFIED_IMPROVE) {
            return $this->_makeErrorInfo($res, 'activity_repeat_apply');
        }

        global $_G;
        $_GET = $data;
        $thread = ForumUtils::getTopicInfo($tid);
        $payvalue = intval($_GET['payvalue']);
        $payment = $_GET['payment'] ? $payvalue : -1;
        $message = cutstr(dhtmlspecialchars($_GET['message']), 200);
        $verified = $thread['authorid'] == $uid ? 1 : 0;

        $ufielddata = '';
        if ($activity['ufield']) {
            $ufielddata = array();
            $version = MobcentDiscuz::getMobcentDiscuzVersion();
            $activity['ufield'] = ($version != MobcentDiscuz::VERSION_X20) ? dunserialize($activity['ufield']) : unserialize($activity['ufield']);
            if (!empty($activity['ufield']['userfield'])) {
                if ($version == MobcentDiscuz::VERSION_X20) {
                    if(!class_exists('discuz_censor'))
                        include libfile('class/censor');
                }
                $censor = discuz_censor::instance();
                loadcache('profilesetting');

                foreach($data as $key => $value) {
                    if (empty($_G['cache']['profilesetting'][$key])) continue;
                    if (is_array($value)) {
                        $value = implode(',', $value);
                    }
                    $value = cutstr(dhtmlspecialchars(trim($value)), 100, '.');
                    // if ($_G['cache']['profilesetting'][$key]['formtype'] == 'file' && !preg_match("/^https?:\/\/(.*)?\.(jpg|png|gif|jpeg|bmp)$/i", $value)) {
                    //     showmessage('activity_imgurl_error');
                    // }
                    if (empty($value) && $key != 'residedist' && $key != 'residecommunity') {
                        return $this->_makeErrorInfo($res, 'activity_exile_field');
                    }
                    $ufielddata['userfield'][$key] = $value;
                }
            }
            if (!empty($activity['ufield']['extfield'])) {
                foreach($activity['ufield']['extfield'] as $fieldid) {
                    $value = cutstr(dhtmlspecialchars(trim($_GET[''.$fieldid])), 50, '.');
                    $ufielddata['extfield'][$fieldid] = $value;
                }
            }
            $ufielddata = !empty($ufielddata) ? serialize($ufielddata) : '';
        }

        if ($_G['setting']['activitycredit'] && $activity['credit'] && empty($applyinfo['verified'])) {
            checklowerlimit(array('extcredits'.$_G['setting']['activitycredit'] => '-'.$activity['credit']));
            updatemembercount($uid, array($_G['setting']['activitycredit'] => '-'.$activity['credit']), true, 'ACC', $tid);
        }

        $data = array(
            'tid' => $tid, 
            'username' => $_G['username'], 
            'uid' => $uid, 
            'message' => $message, 
            'verified' => $verified, 
            'dateline' => $_G['timestamp'], 
            'payment' => $payment, 
            'ufielddata' => $ufielddata
        );
        if ($applyinfo && $applyinfo['verified'] == DzForumActivityApply::STATUS_VERIFIED_IMPROVE) {
            DzForumActivityApply::updateApplyById($data, $applyinfo['applyid']);
        } else {
            DzForumActivityApply::insertApply($data);
        }

        DzForumActivity::updateApplyNumberByTid($tid);

        if ($thread['authorid'] != $uid) {
            notification_add($thread['authorid'], 'activity', 'activity_notice', array(
                'tid' => $tid,
                'subject' => $thread['subject'],
            ));
            $space = array();
            space_merge($space, 'field_home');

            if(!empty($space['privacy']['feed']['newreply'])) {
                $feed['icon'] = 'activity';
                $feed['title_template'] = 'feed_reply_activity_title';
                $feed['title_data'] = array(
                    'subject' => "<a href=\"forum.php?mod=viewthread&tid=$tid\">$thread[subject]</a>",
                    'hash_data' => "tid{$tid}"
                );
                $feed['id'] = $tid;
                $feed['idtype'] = 'tid';
                postfeed($feed);
            }
        }
        $res = $this->_makeErrorInfo($res, 'activity_completion');
        $res['rs'] = 1;
        return $res;
    }

    private function _cancelActivityTopic($res, $tid, $uid, $data) {
        // from forum_misc.php
        DzForumActivityApply::deleteByTidUid($tid, $uid);
        DzForumActivity::updateApplyNumberByTid($tid);
     
        $thread = ForumUtils::getTopicInfo($tid);
        $message = cutstr(dhtmlspecialchars($data['message']), 200);
        if ($thread['authorid'] != $uid) {
            notification_add($thread['authorid'], 'activity', 'activity_cancel', array(
                'tid' => $tid,
                'subject' => $thread['subject'],
                'reason' => $message
            ));
        }
        $res = $this->_makeErrorInfo($res, 'activity_cancel_success');
        $res['rs'] = 1;
        return $res;
    }

    private function _makeErrorInfo($res, $message, $params=array()) {
        return WebUtils::makeErrorInfo_oldVersion($res, $message, $params);
    }
}