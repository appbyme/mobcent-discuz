<?php

/**
 * 投票接口
 *
 * @author 徐少伟 <xushaowei@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}
// Mobcent::setErrors();
class VoteAction extends MobcentAction
{
    public function run($tid, $options)
    {
        $res = $this->initWebApiArray();

        $res = $this->_setToVote($res, $tid, $options);

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _setToVote($res, $tid, $options)
    {
        global $_G;
        $_G['tid'] = $tid;
        require_once libfile('function/post');
        $options = rawurldecode($options);
        $_GET['pollanswers'] = $options;

        if(!$_G['group']['allowvote']) {
            return $this->makeErrorInfo($res, 'group_nopermission', array('{grouptitle}' => $_G['group']['grouptitle']));
        } elseif(!empty($thread['closed'])) {
            return $this->makeErrorInfo($res, 'thread_poll_closed');
        } elseif(empty($_GET['pollanswers'])) {
            return $this->makeErrorInfo($res, 'thread_poll_invalid');
        }

        $pollarray = C::t('forum_poll')->fetch($_G['tid']);
        $overt = $pollarray['overt'];
            
        if(!$pollarray) {
            return $this->makeErrorInfo($res, 'poll_not_found');
        } elseif($pollarray['expiration'] && $pollarray['expiration'] < TIMESTAMP) {
            return $this->makeErrorInfo($res, 'poll_overdue');
        } elseif($pollarray['maxchoices'] && $pollarray['maxchoices'] < count($_GET['pollanswers'])) {
            return $this->makeErrorInfo($res, 'poll_choose_most', array('{maxchoices}' => $pollarray['maxchoices']));
        }

        $voterids = $_G['uid'] ? $_G['uid'] : $_G['clientip'];
        $polloptionid = array();
        $pollarray = '';
        $query = C::t('forum_polloption')->fetch_all_by_tid($_G['tid']);
        foreach($query as $pollarray) {
            if(strexists("\t".$pollarray['voterids']."\t", "\t".$voterids."\t")) {
                return $this->makeErrorInfo($res, 'thread_poll_voted');
            }
            $polloptionid[] = $pollarray['polloptionid'];
        }
        $polloptionids = '';
        foreach($_GET['pollanswers'] as $key => $id) {
            if(!in_array($id, $polloptionid)) {
                return $this->makeErrorInfo($res, 'parameters_error');
            }
            unset($polloptionid[$key]);
            $polloptionids[] = $id;
        }
        
        // $_GET['pollanswers'] = str_replace(',', '    ', $options);
        $_GET['pollanswers'] = explode(',', $options);
        // DB::query("UPDATE ".DB::table('forum_poll')." SET voters=voters+1 where tid=".$_G['tid']);
        // DB::query("UPDATE ".DB::table('forum_polloption')." SET votes=votes+1,voterids=CONCAT(voterids,'".$_G['uid']."') where polloptionid in (".$_GET['pollanswers'].")"); 
        foreach ($_GET['pollanswers'] as $key => $value) {
            // DzUserVote::updateUserVoteByTid($_G['tid']);
            // DzUserVote::updateUserVotePolloption($_G['uid'], $value);

            C::t('forum_polloption')->update_vote($value, $voterids."\t", 1);
            C::t('forum_thread')->update($_G['tid'], array('lastpost'=>$_G['timestamp']), true);
            C::t('forum_poll')->update_vote($_G['tid']);
            C::t('forum_pollvoter')->insert(array(
                'tid' => $_G['tid'],
                'uid' => $_G['uid'],
                'username' => $_G['username'],
                'options' => $value,
                'dateline' => $_G['timestamp'],
                ));
            updatecreditbyaction('joinpoll');
            $space = array();
            space_merge($space, 'field_home');

            if($overt && !empty($space['privacy']['feed']['newreply'])) {
                $feed['icon'] = 'poll';
                $feed['title_template'] = 'feed_thread_votepoll_title';
                $feed['title_data'] = array(
                    'subject' => "<a href=\"forum.php?mod=viewthread&tid=$_G[tid]\">$thread[subject]</a>",
                    'author' => "<a href=\"home.php?mod=space&uid=$thread[authorid]\">$thread[author]</a>",
                    'hash_data' => "tid{$_G[tid]}"
                );
                $feed['id'] = $_G['tid'];
                $feed['idtype'] = 'tid';
                postfeed($feed);
            }
        }
        // $userVoteInfo = DzUserVote::getUserVoteTotalNum($tid);
        $polloption_query = DB::query("SELECT polloption as name,polloptionid as pollItemId,votes as totalNum FROM ".DB::table('forum_polloption')." where tid=".$tid);
        while ($polloption_rst = DB::fetch($polloption_query)) {
            $polloption_arr[] = $polloption_rst;
        }
        for($di=0;$di<count($polloption_arr);$di++){
            $polloption_arr[$di][pollItemId]=(int)$polloption_arr[$di][pollItemId];
            $polloption_arr[$di][totalNum]=(int)$polloption_arr[$di][totalNum];
        }
        $res['vote_rs'] = $polloption_arr;
        $params['noError'] = 1;
        $res = $this->makeErrorInfo($res, 'thread_poll_succeed', $params);
        return $res;
    }
}

class DzUserVote extends DiscuzAR 
{

    public static function updateUserVoteByTid($tid) 
    {
        return DbUtils::getDzDbUtils(true)->query('
            UPDATE %t
            SET voters = voters+1
            WHERE tid = %d
            ',
            array('forum_poll', $tid)
        );
    }

    public static function updateUserVotePolloption($user, $pollanswers) 
    {
        return DbUtils::getDzDbUtils(true)->query('
            UPDATE %t
            SET votes=votes+1, voterids=CONCAT(voterids, %s)
            WHERE polloptionid in (%n)
            ',
            array('forum_polloption', $user, $pollanswers)
        );
    }

    public static function getUserVoteTotalNum($tid) 
    {
        return DbUtils::getDzDbUtils(true)->queryRow('
            SELECT polloption,polloptionid,votes 
            FROM %t 
            WHERE tid = %d 
            ',
            array('forum_polloption', $tid)
        );
    }
}