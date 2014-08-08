<?php

/**
 * 举报接口
 *
 * @author 徐少伟 <xushaowei@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class ReportAction extends MobcentAction {

    public function run($id, $message='', $idtype='thread') {

        $res = $res = $this->initWebApiArray();
        $res = $this->_userReportType($res, $idtype, $id, $message);

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _userReportType($res, $rtype, $rid, $message) {
        global $_G;

        if ($rtype == 'post') {
            $tid = UserReport::getTidByPid($rid);
        }
        $fid = intval($_GET['fid']);
        $uid = intval($_GET['uid']);
        $message = WebUtils::t(rawurldecode($message));
        $default_url = array(
            'user' => 'home.php?mod=space&uid=',
            'post' => 'forum.php?mod=redirect&goto=findpost&ptid='.$tid.'&pid=',
            'thread' => 'forum.php?mod=viewthread&tid=',
            'group' => 'forum.php?mod=group&fid=',
            'album' => 'home.php?mod=space&do=album&uid='.$uid.'&id=',
            'blog' => 'home.php?mod=space&do=blog&uid='.$uid.'&id=',
            'pic' => 'home.php?mod=space&do=album&uid='.$uid.'&picid='
        );
        $url = '';
        if($rid && !empty($default_url[$rtype])) {
            $url = $default_url[$rtype].intval($rid);
        } else {
            $url = addslashes(dhtmlspecialchars(base64_decode($_GET['url'])));
            $url = preg_match("/^http[s]?:\/\/[^\[\"']+$/i", trim($url)) ? trim($url) : '';
        }
        if(empty($url)) {
            $res = $this->makeErrorInfo($res, 'report_parameters_invalid');
        } else {
            $urlkey = md5($url);
            $message = censor(cutstr(dhtmlspecialchars(trim($message)), 200, ''));
            $message = $_G['username'].'&nbsp;:&nbsp;'.rtrim($message, "\\");
            if($reportid = C::t('common_report')->fetch_by_urlkey($urlkey)) {
                C::t('common_report')->update_num($reportid, $message);
            } else {
                $data = array('url' => $url, 'urlkey' => $urlkey, 'uid' => $_G['uid'], 'username' => $_G['username'], 'message' => $message, 'dateline' => TIMESTAMP);
                if($fid) {
                    $data['fid'] = $fid;
                }
                C::t('common_report')->insert($data);
                $report_receive = unserialize($_G['setting']['report_receive']);
                $moderators = array();
                if($report_receive['adminuser']) {
                    foreach($report_receive['adminuser'] as $touid) {
                        notification_add($touid, 'report', 'new_report', array('from_id' => 1, 'from_idtype' => 'newreport'), 1);
                    }
                }
                if($fid && $rtype == 'post') {
                    foreach(C::t('forum_moderator')->fetch_all_by_fid($fid, false) as $row) {
                        $moderators[] = $row['uid'];
                    }
                    if($report_receive['supmoderator']) {
                        $moderators = array_unique(array_merge($moderators, $report_receive['supmoderator']));
                    }
                    foreach($moderators as $touid) {
                        $touid != $_G['uid'] && !in_array($touid, $report_receive) && notification_add($touid, 'report', 'new_post_report', array('fid' => $fid, 'from_id' => 1, 'from_idtype' => 'newreport'), 1);
                    }
                }
            }
            $params['noError'] = 1;
            $res = $this->makeErrorInfo($res, 'report_succeed', $params);
        }
        return $res;
    }
}

class UserReport extends DiscuzAR {

    public static function getTidByPid($pid) {
        return DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT tid
            FROM %t
            WHERE pid = %d
            ',
            array('forum_post', $pid)
        );
    }
}
?>