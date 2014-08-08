<?php

/**
 * 心跳接口
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class HeartAction extends MobcentAction {

    public function run() {
        $res = WebUtils::initWebApiArray_oldVersion();
        
        $uid = $this->getController()->uid;
        
        // get reply info
        $res['body']['replyInfo'] = $this->_getNotifyInfo($uid, 'post');

        // get @me info
        $res['body']['atMeInfo'] = $this->_getNotifyInfo($uid, 'at');

        // get private message that client unreceived
        $res['body']['pmInfos'] = $this->_getPmInfos($uid);

        if (($heartPeriod = WebUtils::getDzPluginAppbymeAppConfig('message_period')) <= 0) {
            $heartPeriod = MINUTE_SECONDS * 2;
        }
        if (($pmPeriod = WebUtils::getDzPluginAppbymeAppConfig('message_pm_period')) <= 0) {
            $pmPeriod = 20;
        }
    
        $res['body']['externInfo']['heartPeriod'] = $heartPeriod . '000';
        $res['body']['externInfo']['pmPeriod'] = $pmPeriod . '000';

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _getNotifyInfo($uid, $type) {
        $data = DbUtils::getDzDbUtils(true)->queryAll('
            SELECT *
            FROM %t
            WHERE uid=%d AND type=%s AND new=%d
            ORDER BY dateline DESC
            ', 
            array('home_notification', $uid, $type, 1)
        );

        $info = array(
            'count' => count($data),
            'time' => !empty($data) ? $data[0]['dateline'] . '000' : "0",
        );

        return $info;
    }

    private function _getPmInfos($uid) {
        $pmInfos = array();

        loaducenter();

        $pmList = uc_pm_list($uid, 1, 10000, 'inbox', 'newpm', 200);
        $pmList = (array)$pmList['data'];
        foreach ($pmList as $pm) {
            // 目前只要两人对话的列表
            if ($pm['members'] > 2 || $pm['pmtype'] != 1) {
                continue;
            }
            $pmInfos[] = array(
                'fromUid' => (int)$pm['touid'],
                'plid' => (int)$pm['plid'],
                'pmid' => (int)$pm['pmid'],
                'time' => $pm['lastdateline'] . '000',
            );
        }

        return $pmInfos;
    }
}
