<?php

/**
 * 邀请注册
 *
 * @copyright 2012-2015 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class InviteActivityAction extends MobcentAction {
    public function run($act='init', $accessToken='', $accessSecret='', $device='', $activityId=1) {
        $res = $this->initWebApiArray();
        if ($act == 'init') {
            $res = $this->_inviteActiv($res, $accessToken, $accessSecret, $device, $activityId);
        } elseif($act == 'user') {
            $res = $this->_userReward($res, $activityId, $accessToken, $accessSecret);
        }
        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _inviteActiv($res, $accessToken, $accessSecret, $device, $activityId) {

        // 获取邀请注册活动的配置
        $config = ActivityUtils::getInviteConfig($activityId);
        if (empty($config) || !$config['is_run'] ) {
            return $this->makeErrorInfo($res, 'mobcent_activity_invalid');
        }

        $res['body']['sponsor'] = (string)$config['sponsor'];
        $res['body']['startTime'] = (string)$config['start_time'].'000';
        $res['body']['stopTime'] = (string)$config['stop_time'].'000';
        $res['body']['firstReward'] = (int)$config['first_reward'];
        $res['body']['inviteReward'] = (int)$config['invite_reward'];
        $res['body']['isShowCheck'] = 0;
        $res['body']['exchangeNum'] = '';
        $res['body']['activityRule'] = (string)$config['activity_rule'];
        $res['body']['shareAppUrl'] = (string)$config['share_appurl'];

        if ($accessToken !== '' && $accessSecret !== '') {
            $uid = AppbymeUserAccess::getUserIdByAccess($accessToken, $accessSecret);
            if ($uid) {
                // 当前登录用户参加活动信息|appbyme_activity_invite_user
                $exchangeInfo = AppbymeActivityInviteUser::getExchangeInfo($uid);
                if ($exchangeInfo) {
                    $checkInvite = ActivityUtils::checkInvite($config, $uid, $device);
                    if ($checkInvite['rs']) {
                        $res['body']['isShowCheck'] = 1;
                    }
                    $res['body']['exchangeNum'] = $exchangeInfo['exchange_num'];
                } else {
                    $userInfo = getuserbyuid($uid);
                    $username = $userInfo['username'];
                    $rewardSum = $config['first_reward'];
                    $availableReward = $config['first_reward'];
                    $exchangeNum = $this->getUniqueNum($uid);

                    $insertUser = array(
                        'uid' => $uid,
                        'activity_id' => $activityId,
                        'username' => $username,
                        'reward_sum' => $rewardSum,
                        'available_reward' => $availableReward,
                        'exchange_num' => $exchangeNum,
                        'device' => $device
                    );

                    if (AppbymeActivityInviteUser::insertUser($insertUser)) {
                        $res['body']['isShowCheck'] = 1;
                        $res['body']['exchangeNum'] = (string)$exchangeNum;
                    }

                }
            } 
        }
        
        return $res;
    }

    // 我的奖励
    private function _userReward($res, $activityId, $accessToken, $accessSecret) {
        $uid = AppbymeUserAccess::getUserIdByAccess($accessToken, $accessSecret);
        if (!$uid) {
             return $this->makeErrorInfo($res, 'mobcent_user_error');
        }
        $exchangeInfo = AppbymeActivityInviteUser::getExchangeInfo($uid);
        $config = ActivityUtils::getInviteConfig($activityId);

        $res['body']['exchangeMin'] = (int)$config['exchange_min'];
        $res['body']['exchangeStatus'] = (int)$exchangeInfo['exchange_status'];
        $res['body']['virtualName'] = (string)$config['virtual_name'];
        $res['body']['exchangeRatio'] = (int)$config['exchange_ratio'];
        $res['body']['rewardSum'] = (int)$exchangeInfo['reward_sum'];
        $res['body']['availableReward'] = (int)$exchangeInfo['available_reward'];
        return $res;
    }

    // 生成一个唯一的
    public function getUniqueNum($unique, $maxLen=9) {
        // return date('md') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT).$uid;
        $uniqueLen = strlen($unique);

        if ($uniqueLen < $maxLen) {
            $len = $maxLen - $uniqueLen;
        } else {
            return $unique;
        }

        $randArr = range(0, 9);
        $tmpNum = '';
        for ($i = 0; $i < $len; $i++) {
            $tmpNum .= $randArr[array_rand($randArr)];
        }

        return $tmpNum.$unique;
    }

}

?>