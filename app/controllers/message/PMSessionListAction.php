<?php

/**
 * 私信总会话列表接口
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class PMSessionListAction extends MobcentAction {

    public function run($json) {
        $res = $this->initWebApiArray();

        $json = rawurldecode($json);
        $json = WebUtils::jsonDecode($json);
        
        !isset($json['page']) && $json['page'] = 1;
        !isset($json['pageSize']) && $json['pageSize'] = 10;
        
        $res = $this->_getResult($res, $json);

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _getResult($res, $data) {
        $page = $data['page'];
        $pageSize = $data['pageSize'];
        
        $page == 0 && $page = 1 && $pageSize = 10000;

        $pmList = $this->_getPMList($page, $pageSize);

        $res['body']['list'] = $pmList['list'];
        $res = WebUtils::getWebApiArrayWithPage($res, $page, $pageSize, $pmList['count']);

        return $res;
    }

    private function _getPMList($page, $pageSize) {
        $pmList = array('list' => array(), 'count' => 0);

        global $_G;
        // 在DISCUZ_ROOT/source/include/space/space_pm.php基础上二次开发

        loaducenter();

        $filter = 'privatepm';
        $perpage = $pageSize;
        $count = 0;
        $list = array();
        
        if($filter == 'privatepm' || $filter == 'newpm') {
            $result = uc_pm_list($_G['uid'], $page, $perpage, 'inbox', $filter, 200);

            $count = $result['count'];
            $list = $result['data'];
        }

        if($_G['member']['newpm']) {
            if($newpm && $_G['setting']['cloud_status']) {
                $msgService = Cloud::loadClass('Cloud_Service_Client_Message');
                $msgService->setMsgFlag($_G['uid'], $_G['timestamp']);
            }
            C::t('common_member')->update($_G['uid'], array('newpm' => 0));
            uc_pm_ignore($_G['uid']);
        }

        $tempPMList = array();
        foreach ($list as $pm) {
            // 目前只要两人对话的列表
            if ($pm['members'] > 2 || $pm['pmtype'] != 1) {
                $count--;
                continue;
            }
            $tempPm = array();
            $tempPm['plid'] = (int)$pm['plid'];
            $tempPm['pmid'] = (int)$pm['pmid'];
            $tempPm['lastUserId'] = (int)$pm['lastauthorid'];
            $tempPm['lastUserName'] = (string)$pm['lastauthor'];
            $tempPm['lastSummary'] = (string)$pm['lastsummary'];
            $tempPm['lastDateline'] = $pm['lastdateline'].'000';
            $tempPm['toUserId'] = (int)$pm['touid'];
            $tempPm['toUserAvatar'] = UserUtils::getUserAvatar($pm['touid']);
            $tempPm['toUserName'] = (string)$pm['tousername'];
            $tempPm['toUserIsBlack'] = UserUtils::isBlacklist($_G['uid'], $pm['touid']) ? 1 : 0;
            $tempPm['isNew'] = $pm['new'] ? 1 : 0;

            $tempPMList[] = $tempPm;
        }
        $pmList['list'] = $tempPMList;
        $pmList['count'] = $count;

        return $pmList;
    }
}