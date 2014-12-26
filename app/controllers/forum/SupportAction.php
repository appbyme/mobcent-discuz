<?php

/**
 * 赞接口
 *
 * @author 徐少伟 <xushaowei@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class SupportAction extends MobcentAction 
{
    public function run($tid, $pid = 0, $type = 'topic', $action = 'support')
    {
        $res = $this->initWebApiArray();

        $res = $this->_setSupport($res, $tid, $pid, $type, $action);

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _setSupport($res, $tid, $pid, $type, $action)
    {
        if ($type == 'thread' || $type == 'topic') {
            $res = $this->_setThreadSupport($res, $tid, $action);
        } elseif($type == 'post') {
            $res = $this->_setPostSupport($res, $tid, $pid, $action);
        }
        return $res;
    }

    private function _setThreadSupport($res, $tid, $action)
    {
        global $_G;
        require_once libfile('function/forum');

        dsetcookie('discuz_recommend', '', -1, 0);

        $thread = C::t('forum_thread')->fetch($tid);
        if(!$_G['setting']['recommendthread']['status'] || !$_G['group']['allowrecommend']) {
            return $this->makeErrorInfo($res, 'no_privilege_recommend');
        }

        if($thread['authorid'] == $_G['uid'] && !$_G['setting']['recommendthread']['ownthread']) {
            return $this->makeErrorInfo($res, 'recommend_self_disallow', array('{recommendc}' => $thread['recommends']));
        }
        if(C::t('forum_memberrecommend')->fetch_by_recommenduid_tid($_G['uid'], $tid)) {
            return $this->makeErrorInfo($res, 'recommend_duplicate', array('{recommendc}' => $thread['recommends']));
        }

        $recommendcount = C::t('forum_memberrecommend')->count_by_recommenduid_dateline($_G['uid'], $_G['timestamp']-86400);
        if($_G['setting']['recommendthread']['daycount'] && $recommendcount >= $_G['setting']['recommendthread']['daycount']) {
            return $this->makeErrorInfo($res, 'recommend_outoftimes', array('{recommendc}' => $thread['recommends']));
        }

        $_G['group']['allowrecommend'] = intval($action == 'support' ? $_G['group']['allowrecommend'] : -$_G['group']['allowrecommend']);
        $fieldarr = array();
        if($action == 'support') {
            $heatadd = 'recommend_add=recommend_add+1';
            $fieldarr['recommend_add'] = 1;
        } else {
            $heatadd = 'recommend_sub=recommend_sub+1';
            $fieldarr['recommend_sub'] = 1;
        }

        update_threadpartake($tid);
        $fieldarr['heats'] = 0;
        $fieldarr['recommends'] = $_G['group']['allowrecommend'];
        C::t('forum_thread')->increase($tid, $fieldarr);
        C::t('forum_memberrecommend')->insert(array('tid'=>$tid, 'recommenduid'=>$_G['uid'], 'dateline'=>$_G['timestamp']));

        dsetcookie('recommend', 1, 43200);
        $recommendv = $_G['group']['allowrecommend'] > 0 ? '+'.$_G['group']['allowrecommend'] : $_G['group']['allowrecommend'];

        if($_G['setting']['recommendthread']['daycount']) {
            $daycount = $_G['setting']['recommendthread']['daycount'] - $recommendcount;
            $params = array('noError' => 1, '{recommendv}' => $recommendv, '{recommendc}' => $thread['recommends'], '{daycount}' => $daycount);
            return $this->makeErrorInfo($res, 'recommend_daycount_succeed', $params);
        } else {
            $params = array('noError' => 1, '{recommendv}' => $recommendv, '{recommendc}' => $thread['recommends']);
            return $this->makeErrorInfo($res, 'recommend_succed', $params);
        }
    }

    private function _setPostSupport($res, $tid, $pid, $action)
    {

        global $_G;
        $_GET['tid'] = $tid;
        $_GET['pid'] = $pid;
        $_GET['do'] = $action;

        $post = C::t('forum_post')->fetch('tid:'.$_GET['tid'], $_GET['pid'], false);

        $hotreply = C::t('forum_hotreply_number')->fetch_by_pid($post['pid']);
        if($_G['uid'] == $post['authorid']) {
            return $this->makeErrorInfo($res, 'noreply_yourself_error', array('{recommendv}' => $recommendv, '{recommendc}' => $thread['recommends']), $params);
        }

        if(empty($hotreply)) {
            $hotreply['pid'] = C::t('forum_hotreply_number')->insert(array(
                'pid' => $post['pid'],
                'tid' => $post['tid'],
                'support' => 0,
                'against' => 0,
                'total' => 0,
            ), true);
        } else {
            if(C::t('forum_hotreply_member')->fetch($post['pid'], $_G['uid'])) {
                return $this->makeErrorInfo($res, 'noreply_voted_error', array('{recommendv}' => $recommendv, '{recommendc}' => $thread['recommends']), $params);
            }
        }

        $typeid = $_GET['do'] == 'support' ? 1 : 0;

        C::t('forum_hotreply_number')->update_num($post['pid'], $typeid);
        C::t('forum_hotreply_member')->insert(array(
            'tid' => $post['tid'],
            'pid' => $post['pid'],
            'uid' => $_G['uid'],
            'attitude' => $typeid,
        ));

        $hotreply[$_GET['do']]++;

        $params = array('noError' => 1, '{recommendv}' => $recommendv, '{recommendc}' => $thread['recommends']);
        return $this->makeErrorInfo($res, 'post_poll_succeed', $params);
    }
}

class support extends DiscuzAR
{

    public static function getPostCount($uid) {
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT count(*) as nums 
            FROM %t 
            WHERE uid = %d
            ',
            array('home_album', $uid)
        );
    }
}