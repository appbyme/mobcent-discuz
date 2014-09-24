<?php

/**
 * 帖子评分接口
 *
 * @author HanPengyu
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}
// Mobcent::setErrors();
class TopicRateAction extends MobcentAction
{
    public function run($tid, $pid)
    {
        $res = $this->initWebApiArray();
        // if ($type == 'check') {
        //     $res = $this->_checkRate($res, $tid, $pid);
        //     WebUtils::outputWebApi($res);
        // } elseif ($type == 'view') {
        //     $this->_viewRate($tid, $pid);
        // }
        $this->_viewRate($res, $tid, $pid);
    }

    private function _checkRate($res, $tid, $pid)
    {   
        global $_G;
        require_once libfile('function/misc');
        $_GET['tid'] = $_G['tid'] = $tid;
        $_GET['pid'] = $pid;

        if(!$_G['group']['raterange']) {
            // 抱歉，您所在的用户组({grouptitle})无法进行此操作
            return $this->makeErrorInfo($res, lang('message', 'group_nopermission', array('grouptitle' => $_G['group']['grouptitle'])));
        } elseif($_G['setting']['modratelimit'] && $_G['adminid'] == 3 && !$_G['forum']['ismoderator']) {
            // 抱歉，作为版主您只能在自己的管辖范围内评分
            return $this->makeErrorInfo($res, lang('message', 'thread_rate_moderator_invalid'));
        }

        if(($_G['group']['resasonpm'] == 2 || $_G['group']['reasonpm'] == 3) || !empty($_GET['sendreasonpm'])) {
            $forumname = strip_tags($_G['forum']['name']);
            $sendreasonpm = 1;
        } else {
            $sendreasonpm = 0;
        }

        $thread = C::t('forum_thread')->fetch($_G['tid']);
        $post = C::t('forum_post')->fetch('tid:'.$_G['tid'], $_GET['pid']);

        if($post['invisible'] != 0 || $post['authorid'] == 0) {
            $post = array();
        }

        if(!$post || $post['tid'] != $thread['tid'] || !$post['authorid']) {
            // 帖子不存在或不能被推送
            return $this->makeErrorInfo($res, lang('message', 'rate_post_error'));
        } elseif(!$_G['forum']['ismoderator'] && $_G['setting']['karmaratelimit'] && TIMESTAMP - $post['dateline'] > $_G['setting']['karmaratelimit'] * 3600) {
            // 抱歉，您不能对发表于 {karmaratelimit} 小时前的帖子进行评分
            return $this->makeErrorInfo($res, lang('message', 'thread_rate_timelimit', array('karmaratelimit' => $_G['setting']['karmaratelimit'])));
        } elseif($post['authorid'] == $_G['uid'] || $post['tid'] != $_G['tid']) {
            // 抱歉，您不能给自己发表的帖子评分
            return $this->makeErrorInfo($res, lang('message', 'thread_rate_member_invalid'));
            // $errorMsg = lang('message', 'thread_rate_member_invalid');
        } elseif($post['anonymous']) {
            // 抱歉，您不能对匿名帖评分
            return $this->makeErrorInfo($res, lang('message', 'thread_rate_anonymous'));
        } elseif($post['status'] & 1) {
            // 抱歉，您不能对屏蔽帖评分
            return $this->makeErrorInfo($res, lang('message', 'thread_rate_banned'));
        }

        $allowrate = TRUE;
        if(!$_G['setting']['dupkarmarate']) {
            if(C::t('forum_ratelog')->count_by_uid_pid($_G['uid'], $_GET['pid'])) {
                // 抱歉，您不能对同一个帖子重复评分
                return $this->makeErrorInfo($res, lang('message', 'thread_rate_duplicate'));
            }
        }

        return $res;
    }

    private function _viewRate($res, $tid, $pid)
    {   
        $res = $this->_checkRate($res, $tid, $pid);
        $status = WebUtils::checkError($res);
        $location = WebUtils::createUrl_oldVersion('index/returnmobileview');
        if ($status) {
            $str = <<<HTML
            <script>
                alert("{$res['head']['errInfo']}");
                location.href = "{$location}";
            </script>
HTML;
        echo $str;
        exit;
        }

        global $_G;
        require_once libfile('function/misc');
        require_once libfile('function/forum');

        //  今日剩余积分
        $maxratetoday = $this->getratingleft($_G['group']['raterange']);
        $post = C::t('forum_post')->fetch('tid:'.$tid, $pid);
        $thread = C::t('forum_thread')->fetch($tid);
        if (!empty($_POST)) {
            $reason = checkreasonpm();
            $rate = $ratetimes = 0;
            $creditsarray = $sub_self_credit = array();
            getuserprofile('extcredits1');

            foreach($_G['group']['raterange'] as $id => $rating) {
                $score = intval($_GET['score'.$id]);
                if(isset($_G['setting']['extcredits'][$id]) && !empty($score)) {
                    if($rating['isself'] && (intval($_G['member']['extcredits'.$id]) - $score < 0)) {
                        // 抱歉，您的{extcreditstitle}不足，无法评分
                        $errorMsg = lang('message', 'thread_rate_range_self_invalid', array('extcreditstitle' => $_G['setting']['extcredits'][$id]['title']));
                        $this->renderTemplates($tid, $pid, $errorMsg);
                        exit;
                    }
                    if(abs($score) <= $maxratetoday[$id]) {
                        if($score > $rating['max'] || $score < $rating['min']) {
                            // 请输入正确的分值
                            $errorMsg = lang('message', 'thread_rate_range_invalid');
                            $this->renderTemplates($tid, $pid, $errorMsg);
                            exit;
                        } else {
                            $creditsarray[$id] = $score;
                            if($rating['isself']) {
                                $sub_self_credit[$id] = -abs($score);
                            }
                            $rate += $score;
                            $ratetimes += ceil(max(abs($rating['min']), abs($rating['max'])) / 5);
                        }
                    } else {
                        // 抱歉，24 小时评分数超过限制
                        $errorMsg = lang('message', 'thread_rate_ctrl');
                        $this->renderTemplates($tid, $pid, $errorMsg);
                        exit;
                    }
                }
            }          

            if(!$creditsarray) {
                // 请输入正确的分值
                $errorMsg = lang('message', 'thread_rate_range_invalid');
                $this->renderTemplates($tid, $pid, $errorMsg);
                exit;
            }
            
            updatemembercount($post['authorid'], $creditsarray, 1, 'PRC', $_GET['pid']);
            
            if(!empty($sub_self_credit)) {
                updatemembercount($_G['uid'], $sub_self_credit, 1, 'RSC', $_GET['pid']);
            }
            C::t('forum_post')->increase_rate_by_pid('tid:'.$_G['tid'], $_GET['pid'], $rate, $ratetimes);
            if($post['first']) {
                $threadrate = intval(@($post['rate'] + $rate) / abs($post['rate'] + $rate));
                C::t('forum_thread')->update($_G['tid'], array('rate'=>$threadrate));
            }
            
            require_once libfile('function/discuzcode');
            $sqlvalues = $comma = '';
            $sqlreason = censor(trim($_GET['reason']));
            $sqlreason = cutstr(dhtmlspecialchars($sqlreason), 40, '.');
            foreach($creditsarray as $id => $addcredits) {
                $insertarr = array(
                    'pid' => $_GET['pid'],
                    'uid' => $_G['uid'],
                    'username' => $_G['username'],
                    'extcredits' => $id,
                    'dateline' => $_G['timestamp'],
                    'score' => $addcredits,
                    'reason' => $sqlreason
                );
                C::t('forum_ratelog')->insert($insertarr);
            }
            include_once libfile('function/post');
            $_G['forum']['threadcaches'] && @deletethreadcaches($_G['tid']);
            
            $reason = dhtmlspecialchars(censor(trim($reason)));
            
            // 对是否通知作者做的一些初始工作
            if(($_G['group']['resasonpm'] == 2 || $_G['group']['reasonpm'] == 3) || !empty($_GET['sendreasonpm'])) {
                $forumname = strip_tags($_G['forum']['name']);
                $sendreasonpm = 1;
            } else {
                $sendreasonpm = 0;
            }
            // *****
            if($sendreasonpm) {
                $ratescore = $slash = '';
                foreach($creditsarray as $id => $addcredits) {
                    $ratescore .= $slash.$_G['setting']['extcredits'][$id]['title'].' '.($addcredits > 0 ? '+'.$addcredits : $addcredits).' '.$_G['setting']['extcredits'][$id]['unit'];
                    $slash = ' / ';
                }
                sendreasonpm($post, 'rate_reason', array(
                    'tid' => $thread['tid'],
                    'pid' => $_GET['pid'],
                    'subject' => $thread['subject'],
                    'ratescore' => $ratescore,
                    'reason' => $reason,
                    'from_id' => 0,
                    'from_idtype' => 'rate'
                ));
            }
            $logs = array();
            foreach($creditsarray as $id => $addcredits) {
                $logs[] = dhtmlspecialchars("$_G[timestamp]\t{$_G[member][username]}\t$_G[adminid]\t$post[author]\t$id\t$addcredits\t$_G[tid]\t$thread[subject]\t$reason");
            }
            update_threadpartake($post['tid']);
            C::t('forum_postcache')->delete($_GET['pid']);
            writelog('ratelog', $logs);

            // 评分成功
            $this->getController()->redirect(WebUtils::createUrl_oldVersion('index/returnmobileview'));

        }

        $this->renderTemplates($tid, $pid);
    }

    // 调用模版
    private  function renderTemplates($tid, $pid, $errorMsg='') 
    {
        global $_G;
        require_once libfile('function/misc');

        //  今日剩余积分
        $maxratetoday = $this->getratingleft($_G['group']['raterange']);

        // 评分栏目列表
        $ratelist = $this->getratelist($_G['group']['raterange']);
        $ratelist = WebUtils::emptyHtml($ratelist);

        // 评分理由
        $selectreason =  explode("\n", modreasonselect(0, 'userreasons'));
        $selectreason = str_replace('</li>', '', $selectreason);
        $selectreason = explode('<li>', $selectreason[0]);

        $this->getController()->renderPartial('topicRate', array(
            'formUrl' => WebUtils::createUrl_oldVersion('forum/topicrate',
                array(
                    'tid' => $tid,
                    'pid' => $pid,
                    // 'type' =>'check',
                    // 'hacker_uid' => $_G['uid']
                )
            ),
            'errorMsg' => $errorMsg,
            'ratelist' => $ratelist,
            'maxratetoday' => $maxratetoday,
            'selectreason' => $selectreason
        ));         
    }

    // 剩余积分
    private function getratingleft($raterange)
    {
        global $_G;
        $maxratetoday = array();

        foreach($raterange as $id => $rating) {
            $maxratetoday[$id] = $rating['mrpd'];
        }

        foreach(C::t('forum_ratelog')->fetch_all_sum_score($_G['uid'], $_G['timestamp']-86400) as $rate) {
            $maxratetoday[$rate['extcredits']] = $raterange[$rate['extcredits']]['mrpd'] - $rate['todayrate'];
        }
        return $maxratetoday;
    }

    // 评分栏目列表
    private function getratelist($raterange)
    {
        global $_G;
        $maxratetoday = $this->getratingleft($raterange);

        $ratelist = array();
        foreach($raterange as $id => $rating) {
            if(isset($_G['setting']['extcredits'][$id])) {
                $ratelist[$id] = '';
                $rating['max'] = $rating['max'] < $maxratetoday[$id] ? $rating['max'] : $maxratetoday[$id];
                $rating['min'] = -$rating['min'] < $maxratetoday[$id] ? $rating['min'] : -$maxratetoday[$id];
                $offset = abs(ceil(($rating['max'] - $rating['min']) / 10));
                if($rating['max'] > $rating['min']) {
                    for($vote = $rating['max']; $vote >= $rating['min']; $vote -= $offset) {
                        $ratelist[$id] .= $vote ? '<li>'.($vote > 0 ? '+'.$vote : $vote).'</li>' : '';
                    }
                }
            }
        }
        return $ratelist;
    }
}