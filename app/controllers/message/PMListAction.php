<?php

/**
 * 私信列表接口
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class PMListAction extends MobcentAction {

    public function run($pmlist) {
        $res = $this->initWebApiArray();
        
        $uid = $this->getController()->uid;

        // $pmlist ='{"body": {"pmInfos": [{"fromUid": 4, "startTime": "0", "stopTime": "0", "cacheCount": 0, "pmLimit": 10, }], "externInfo": {"onlyFromUid":0} } }';
        
        $res['body']['userInfo'] = $this->_getUserInfo($uid);
        $res['body']['pmList'] = $this->_getPMList($uid, $pmlist);

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _getUserInfo($uid) {
        $userInfo = array('uid' => 0, 'name' => '', 'avatar' => '');

        $user = UserUtils::getUserInfo($uid);
        if (!empty($user)) {
            $userInfo['uid'] = (int)$uid;
            $userInfo['name'] = $user['username'];
            $userInfo['avatar'] = UserUtils::getUserAvatar($uid, 'small');
        }

        return $userInfo;
    }

    private function _getPMList($uid, $pmlistJson) {
        $pmList = array();

        $pmInfos = WebUtils::jsonDecode($pmlistJson);
        if (!empty($pmInfos)) {
            $externInfo = $pmInfos['body']['externInfo'];
            $isFilter = (isset($externInfo['onlyFromUid']) && $externInfo['onlyFromUid']);
            $pmInfos = $pmInfos['body']['pmInfos'];
            foreach ($pmInfos as $info) {
                $startTime = $info['startTime'] != 0 ? substr($info['startTime'], 0, -3) : 0;
                $stopTime = $info['stopTime'] != 0 ? substr($info['stopTime'], 0, -3) : 0;
                $cacheCount = (int)$info['cacheCount'];
                $pmLimit = $info['pmLimit'] > 0 ? (int)$info['pmLimit'] : 15;
                $userInfo = $this->_getUserInfo($info['fromUid']);
                $msgList = $this->_getPMMsgList(
                    $uid, $info['fromUid'], 
                    $startTime, $stopTime, 
                    $cacheCount, $pmLimit, $isFilter
                );
                
                $pmInfo['fromUid'] = (int)$userInfo['uid'];
                $pmInfo['name'] = $userInfo['name'];
                $pmInfo['avatar'] = $userInfo['avatar'];
                $pmInfo['msgList'] = (array)$msgList['list'];
                $pmInfo['plid'] = (int)$msgList['plid'];
                $pmInfo['hasPrev'] = $msgList['hasPrev'] ? 1 : 0;

                $pmList[] = $pmInfo;
            }
        }

        return $pmList;
    }

    // 获取私信列表
    private function _getPMMsgList($uid, $fromUid, $startTime=0, $stopTime=0, $cacheCount=0, $pmLimit=10, $isFilter=false) {
        $msgList = array();
        $hasPrev = false;
        $plid = 0;

        loaducenter();
        
        $tempMsgList = array();
        if ($stopTime == 0) { // 获取新的消息
            if ($startTime == 0) { // 获取新的消息
                $count = (int)uc_pm_view_num($uid, $fromUid, 0);
                $tempMsgList = (array)uc_pm_view($uid, 0, $fromUid, 5, 1, $pmLimit, 0, 0);
                $count > count($tempMsgList) && $hasPrev = true;
            } else {
                $tempMsgList = (array)uc_pm_view($uid, 0, $fromUid, 5, 1, 50, 0, 0);
                $lastIndex = count($tempMsgList) - 1;
                if ($lastIndex >= 0) {
                    $offset = 0;
                    for ($i = $lastIndex; $i >= 0; $i--) {
                        if ($tempMsgList[$i]['dateline'] <= $startTime) {
                            $offset = $i;
                            $offset++;
                            break;
                        }
                    }
                    $tempMsgList = array_slice($tempMsgList, $offset);
                }
            }         
        } else if ($stopTime) { // 获取历史的消息
            $count = (int)uc_pm_view_num($uid, $fromUid, 0);
            $lastPage = (int)(($count-1)/$pmLimit) + 1;
            $page = (int)floor($cacheCount/$pmLimit) + 1;
            $tempList = (array)uc_pm_view($uid, 0, $fromUid, 5, $page, $pmLimit);
            foreach ($tempList as $pm) {
                if ($pm['dateline'] < $stopTime) {
                    $tempMsgList[] = $pm;
                }
            }
            $page < $lastPage && $hasPrev = true;
        }

        foreach ($tempMsgList as $msg) {
            $msgInfo = array();
            $plid = $msg['plid'];
            if (!$isFilter || $msg['authorid'] != $uid) {
                $tempMsg = $this->_transMessage($msg['message']);
                $msgInfo['sender'] = (int)$msg['authorid'];
                $msgInfo['mid'] = (int)$msg['pmid'];
                $msgInfo['content'] = (string)$tempMsg['content'];
                $msgInfo['type'] = $tempMsg['type'];
                $msgInfo['time'] = $msg['dateline'] . '000';
                $msgList[] = $msgInfo;
            }
        }

        return array('list' => $msgList, 'hasPrev' => $hasPrev, 'plid' => $plid);
    }

    private function _transMessage($msgString) {
        $msg = array('type' => 'text', 'content' => '');

        $matches = array();
        preg_match_all('/<img.*?src="(.*?)".*?\/>/s', $msgString, $matches, PREG_SET_ORDER);
        if (!empty($matches)) {
            foreach ($matches as $match) {
                $match[1] = WebUtils::getHttpFileName($match[1]);
                if (strpos($match[0], 'static/image/smiley') !== false ||
                    strpos($match[0], 'mobcent/app/data/phiz') !== false) {
                    $msgString = str_replace($match[0], sprintf('[mobcent_phiz=%s]', $match[1]), $msgString);
                } else {
                    $msg['type'] = 'image';
                    $msgString = ImageUtils::getThumbImage($match[1]);
                    break;
                }
            }
        }

        $matches = array();
        preg_match_all('/<a href="(.*?)".*?>(.*?)<\/a>/s', $msgString, $matches, PREG_SET_ORDER);
        if (!empty($matches)) {
            foreach ($matches as $match) {
                $match[1] = WebUtils::getHttpFileName($match[1]);
                if (strpos($match[0], UploadUtils::getUploadAudioBaseUrlPath()) !== false) {
                    $msg['type'] = 'audio';
                    $msgString = $match[1];
                    break;
                } else {
                    // $msgString = str_replace($match[0], sprintf('[mobcent_url=%s]%s[/mobcent_url]', $match[1], $match[2]), $msgString);
                    $msgString = str_replace($match[0], sprintf(' %s %s ', $match[2], $match[1]), $msgString);
                }
            }
        }

        $msg['content'] = WebUtils::emptyHtml($msgString);
        return $msg;
    }
}