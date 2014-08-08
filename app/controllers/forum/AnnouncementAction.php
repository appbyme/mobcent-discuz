<?php

/**
 * 公告详情接口
 *
 * @author 徐少伟 <xushaowei@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class AnnouncementAction extends MobcentAction {

    public function run($id=0) {

        $res = $res = $this->initWebApiArray();
        $res = $this->_getAnnouncementInfo($res, $id);

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _getAnnouncementInfo($res, $id) {
        global $_G;
        require_once libfile('function/discuzcode');

        $announce = DzForumAnnouncement::getAnnouncementByUid($id);

        if(!count($announce)) {
            $res = $this->makeErrorInfo($res, 'announcement_nonexistence');
        } else {
            $tempAnnounce = array();
            $tempAnnounce['author'] = $announce['author'];

            $tmp = explode('.', dgmdate($announce['starttime'], 'Y.m'));
            $months[$tmp[0].$tmp[1]] = $tmp;
            if(!empty($_GET['m']) && $_GET['m'] != dgmdate($announce['starttime'], 'Ym')) {
                continue;
            }

            $tempAnnounce['starttime'] = dgmdate($announce['starttime'], 'd');
            $tempAnnounce['endtime'] = $announce['endtime'] ? dgmdate($announce['endtime'], 'd') : '';
            $tempAnnounce['title'] = WebUtils::emptyHtml($announce['subject']);

            $uid = DzCommonMember::getUidByUsername($announce['author']);
            $tempAnnounce['icon'] = UserUtils::getUserAvatar($uid);

            $announceMessage = $announce['type'] == 1 ? "{$announce[message]}" : $announceMessage;
            $announceMessage = nl2br(discuzcode($announce['message'], 0, 0, 1, 1, 1, 1, 1));
            
            $announceType = array();
            $announceType['infor'] = WebUtils::emptyHtml($announceMessage);
            $announce['type'] == 1 ? $announceType['type'] = 'url' : $announceType['type'] = 'text';
            
            $tempAnnounce['content'] = $announceType;
            $res['body']['list'] = $tempAnnounce;
        }
        return $res;
    }
}
?>