<?php

/**
 * 签到接口
 *
 * @author HanPengyu
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}
class SignAction extends CAction{
    public function run() {
        $res = WebUtils::initWebApiArray_oldVersion();
        $res = $this->_startSign($res);
        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _startSign($res) {
        global $_G;
        loadcache('pluginlanguage_script');
        $lang = $_G['cache']['pluginlanguage_script']['dsu_paulsign'];  //获取语言包
        $var = $_G['cache']['plugin']['dsu_paulsign'];  // 获取插件的配置信息
        if (!$var['ifopen']) {
            if ($var['plug_clsmsg'] == '') {
                $var['plug_clsmsg'] = WebUtils::t('签到插件没有开启');
            }
            return WebUtils::makeErrorInfo_oldVersion($res, MOBCENT_ERROR_DEFAULT.":{$var['plug_clsmsg']}");
        }
        $htime = dgmdate($_G['timestamp'], 'H',$var['tos']);    // 获取当前的时间,小时
        $groups = unserialize($var['groups']);  // 获取允许签到的用户组

        // 本月签到的处理
        $qddb = DB::fetch_first("SELECT time FROM ".DB::table('dsu_paulsign')." ORDER BY time DESC limit 0,1");
        $lastmonth = dgmdate($qddb['time'], 'm',$var['tos']);
        $nowmonth = dgmdate($_G['timestamp'], 'm',$var['tos']);
        if ($nowmonth != $lastmonth) {
            DB::query("UPDATE ".DB::table('dsu_paulsign')." SET mdays=0 WHERE uid");
        }

        // 获取当前用户发贴总数
        $post = DB::fetch_first("SELECT posts FROM ".DB::table('common_member_count')." WHERE uid='$_G[uid]'");
        $read_ban = explode(",",$var['ban']);   //查看黑名单用户

        // 用户签到的信息
        $qiandaodb = DB::fetch_first("SELECT * FROM ".DB::table('dsu_paulsign')." WHERE uid='$_G[uid]'");
        $tdtime = gmmktime(0,0,0,dgmdate($_G['timestamp'], 'n',$var['tos']),dgmdate($_G['timestamp'], 'j',$var['tos']),dgmdate($_G['timestamp'], 'Y',$var['tos'])) - $var['tos']*3600;
        
        // 心情初始化
        // $emots = unserialize($_G['setting']['paulsign_emot']);

        $_GET['qdxq'] = 'kx';
        $credit = mt_rand($var['mincredit'], $var['maxcredit']); // 奖励积分值的设置

        // 客户端奖励倍数
        $appbymePlug = WebUtils::getDzPluginAppbymeAppConfig('dzsyscache_sign_extcredit_base');
        $appbymePlug = $appbymePlug !== false ? $appbymePlug : 100;
        $credit = $appbymePlug * 0.01 * $credit;
        $jlxgroups = unserialize($var['jlxgroups']);    //奖励增倍的指定用户组选择
        
        //前N额外奖励项自定义
        $njlmain =str_replace(array("\r\n", "\n", "\r"), '/hhf/', $var['jlmain']);
        $extreward = explode("/hhf/", $njlmain);
        $extreward_num = count($extreward);
        $stats = DB::fetch_first("SELECT * FROM ".DB::table('dsu_paulsignset')." WHERE id='1'");

        // 判断是否开启时间段限制
        if ($var['timeopen']) {
            if ($htime < $var['stime']) {
                return WebUtils::makeErrorInfo_oldVersion($res, MOBCENT_ERROR_DEFAULT.":{$lang['ts_timeearly1']}{$var[stime]}{$lang['ts_timeearly2']}");
            } elseif ($htime > $var['ftime']) { 
                return WebUtils::makeErrorInfo_oldVersion($res, MOBCENT_ERROR_DEFAULT.":{$lang['ts_timeov']}");
            }            
        }

        // 判断允许签到的用户组
        if (!in_array($_G['groupid'], $groups)) {
            return WebUtils::makeErrorInfo_oldVersion($res, MOBCENT_ERROR_DEFAULT.":{$lang['ts_notallow']}");
        }
        // 判断当前用户的发帖总数是否允许签到 
        if ($var['mintdpost'] > $post['posts']) {
            return WebUtils::makeErrorInfo_oldVersion($res, MOBCENT_ERROR_DEFAULT.":{$lang['ts_minpost1']}{$var[mintdpost]}{$lang['ts_minpost2']}");
        }

        //判断当前用户是否在黑名单中 
        if (in_array($_G['uid'], $read_ban)) {
            return WebUtils::makeErrorInfo_oldVersion($res, MOBCENT_ERROR_DEFAULT.":{$lang['ts_black']}");
        }

        // 当前用户是否签到的判断
        if ($qiandaodb['time'] > $tdtime) {
            return WebUtils::makeErrorInfo_oldVersion($res, MOBCENT_ERROR_DEFAULT.":{$lang['ts_yq']}");
        }

        // 心情的处理
        // if (!array_key_exists($_GET['qdxq'], $emots)) {
        //     return WebUtils::makeErrorInfo_oldVersion($res, MOBCENT_ERROR_DEFAULT.":{$lang['ts_xqnr']}");
        // }

        // 今日想说的话
        $todaysay = WebUtils::getDzPluginAppbymeAppConfig('mobile_sign_text');
        empty($todaysay) && $todaysay = $lang['wttodaysay'];
        
        //判断签到进程锁
        if ($var['lockopen']){
            while(discuz_process::islocked('dsu_paulsign', 5)){
                usleep(100000);
            }
        }
        // jlx : 指定用户组获取奖励是一般的几倍
        if (in_array($_G['groupid'], $jlxgroups) && $var['jlx'] !== '0') {
            $credit = $credit * $var['jlx'];
        }

        // 连续签到指数开关
        if (($tdtime - $qiandaodb['time']) < 86400 && $var['lastedop'] && $qiandaodb['lasted'] !== '0'){
            $randlastednum = mt_rand($var['lastednuml'],$var['lastednumh']);
            $randlastednum = sprintf("%03d", $randlastednum);
            $randlastednum = '0.'.$randlastednum;
            $randlastednum = $randlastednum * $qiandaodb['lasted'];
            $credit = round($credit*(1+$randlastednum));
        }
        $num = DB::result_first("SELECT COUNT(*) FROM ".DB::table('dsu_paulsign')." WHERE time >= {$tdtime} ");

        if (!$qiandaodb['uid']) {
            DB::query("INSERT INTO ".DB::table('dsu_paulsign')." (uid,time) VALUES ('$_G[uid]',$_G[timestamp])");
        }
        // 连续签到指数
        if (($tdtime - $qiandaodb['time']) < 86400 && $var['lastedop']){
            DB::query("UPDATE ".DB::table('dsu_paulsign')." SET days=days+1,mdays=mdays+1,time='$_G[timestamp]',qdxq='$_GET[qdxq]',todaysay='$todaysay',reward=reward+{$credit},lastreward='$credit',lasted=lasted+1 WHERE uid='$_G[uid]'");
        } else {
            DB::query("UPDATE ".DB::table('dsu_paulsign')." SET days=days+1,mdays=mdays+1,time='$_G[timestamp]',qdxq='$_GET[qdxq]',todaysay='$todaysay',reward=reward+{$credit},lastreward='$credit',lasted='1' WHERE uid='$_G[uid]'");
        }

        // 添加积分操作
        updatemembercount($_G['uid'], array($var['nrcredit'] => $credit));
        require_once libfile('function/post');
        require_once libfile('function/forum');

        // 是否同步到记录
        if ($var['sync_say'] && $_GET['qdmode'] =='1') {

        }

        // 是否同步到广播大厅
        if ($var['sync_follow'] && $_GET['qdmode']=='1' && $_G['setting']['followforumid']) {

        }

        // 每日最想说是否同步到签名
        if ($var['sync_sign'] && $_G['group']['maxsigsize']) {
            $signhtml = cutstr(strip_tags($todaysay.$lang['fromsign']), $_G['group']['maxsigsize']);
            DB::update('common_member_field_forum', array('sightml'=>$signhtml), "uid='$_G[uid]'");
        }

        // 对前N额外奖励项
        if ($num <= ($extreward_num - 1) ) {
            list($exacr,$exacz) = explode("|", $extreward[$num]);
            $psc = $num+1;
            if($exacr && $exacz) updatemembercount($_G['uid'], array($exacr => $exacz));
        }

        // 获取主题和帖子要插入的状态信息
        $topicStatus = $this->getClientApp('topic', $_GET['platType']);
        $postStatus = $this->getClientApp('post', $_GET['platType']);

        // 对签到自动回复的类型进行处理
        // 2:指定贴自动回复
        if($var['qdtype'] == '2') {
            $thread = DB::fetch_first("SELECT * FROM ".DB::table('forum_thread')." WHERE tid='$var[tidnumber]'");
            $hft = dgmdate($_G['timestamp'], 'Y-m-d H:i',$var['tos']);
            if ($num >=0 && ($num <= ($extreward_num - 1)) && $exacr && $exacz) {
                $message = "[quote][size=2][color=gray][color=teal] [/color][color=gray]{$lang[tsn_01]}[/color] [color=darkorange]{$hft}[/color] {$lang[tsn_02]}[color=red]{$lang[tsn_03]}[/color][color=darkorange]{$lang[tsn_04]}{$psc}{$lang[tsn_05]}[/color]{$lang[tsn_06]} [/color][color=gray]{$_G[setting][extcredits][$var[nrcredit]][title]} [/color][color=darkorange]{$credit}[/color][color=gray]{$_G[setting][extcredits][$var[nrcredit]][unit]}[/color][color=gray]{$lang[tsn_17]}[/color] [color=gray]{$_G[setting][extcredits][$exacr][title]} [/color][color=darkorange]{$exacz}[/color][color=gray]{$_G[setting][extcredits][$exacr][unit]}.{$another_vip}[/color][/color][/size][/quote][size=3][color=dimgray]{$lang[tsn_07]}[color=red]{$todaysay}[/color]{$lang[tsn_08]}[/color][/size]";
            } else {
                $message = "[quote][size=2][color=gray][color=teal] [/color][color=gray]{$lang[tsn_01]}[/color] [color=darkorange]{$hft}[/color] {$lang[tsn_09]}{$lang[tsn_06]} [/color][color=gray]{$_G[setting][extcredits][$var[nrcredit]][title]} [/color][color=darkorange]{$credit} [/color][color=gray]{$_G[setting][extcredits][$var[nrcredit]][unit]}.{$another_vip}[/color][/size][/quote][size=3][color=dimgray]{$lang[tsn_07]}[color=red]{$todaysay}[/color]{$lang[tsn_08]}[/color][/size]";
            }
            $pid = insertpost(array('fid' => $thread['fid'],'tid' => $var['tidnumber'],'first' => '0','author' => $_G['username'],'authorid' => $_G['uid'],'subject' => '','dateline' => $_G['timestamp'],'message' => $message,'useip' => $_G['clientip'],'invisible' => '0','anonymous' => '0','usesig' => '0','htmlon' => '0','bbcodeoff' => '0','smileyoff' => '0','parseurloff' => '0','attachment' => '0','status' =>$postStatus));
            DB::query("UPDATE ".DB::table('forum_thread')." SET lastposter='$_G[username]', lastpost='$_G[timestamp]', replies=replies+1 WHERE tid='$var[tidnumber]' AND fid='$thread[fid]'", 'UNBUFFERED');
            updatepostcredits('+', $_G['uid'], 'reply', $thread['fid']);
            $lastpost = "$thread[tid]\t".addslashes($thread['subject'])."\t$_G[timestamp]\t$_G[username]";
            DB::query("UPDATE ".DB::table('forum_forum')." SET lastpost='$lastpost', posts=posts+1, todayposts=todayposts+1 WHERE fid='$thread[fid]'", 'UNBUFFERED');
            $tidnumber = $var['tidnumber'];

        // 指定板块每天一主题
        } elseif ($var['qdtype'] == '3') {
            if ($num=='0' || $stats['qdtidnumber'] == '0') {
                $subject=str_replace(array('{m}','{d}','{y}','{bbname}','{author}'),array(dgmdate($_G['timestamp'], 'n',$var['tos']),dgmdate($_G['timestamp'], 'j',$var['tos']),dgmdate($_G['timestamp'], 'Y',$var['tos']),$_G['setting']['bbname'],$_G['username']),$var['title_thread']);
                $hft = dgmdate($_G['timestamp'], 'Y-m-d H:i',$var['tos']);
                if ($exacr && $exacz) {
                    $message = "[quote][size=2][color=dimgray]{$lang[tsn_10]}[/color][url={$_G[siteurl]}plugin.php?id=dsu_paulsign:sign][color=darkorange]{$lang[tsn_11]}[/color][/url][color=dimgray]{$lang[tsn_12]}[/color][/size][/quote][quote][size=2][color=gray][color=teal] [/color][color=gray]{$lang[tsn_01]}[/color] [color=darkorange]{$hft}[/color] {$lang[tsn_02]}[color=red]{$lang[tsn_03]}[/color][color=darkorange]{$lang[tsn_04]}{$lang[tsn_13]}{$lang[tsn_05]}[/color]{$lang[tsn_06]} [/color][color=gray]{$_G[setting][extcredits][$var[nrcredit]][title]} [/color][color=darkorange]{$credit}[/color][color=gray]{$_G[setting][extcredits][$var[nrcredit]][unit]}[/color][color=gray]{$lang[tsn_17]}[/color] [color=gray]{$_G[setting][extcredits][$exacr][title]} [/color][color=darkorange]{$exacz}[/color][color=gray]{$_G[setting][extcredits][$exacr][unit]}.{$another_vip}[/color][/color][/size][/quote][size=3][color=dimgray]{$lang[tsn_07]}[color=red]{$todaysay}[/color]{$lang[tsn_08]}[/color][/size]";
                } else {
                    $message = "[quote][size=2][color=dimgray]{$lang[tsn_10]}[/color][url={$_G[siteurl]}plugin.php?id=dsu_paulsign:sign][color=darkorange]{$lang[tsn_11]}[/color][/url][color=dimgray]{$lang[tsn_12]}[/color][/size][/quote][quote][size=2][color=gray][color=teal] [/color][color=gray]{$lang[tsn_01]}[/color] [color=darkorange]{$hft}[/color] {$lang[tsn_02]}[color=red]{$lang[tsn_03]}[/color][color=darkorange]{$lang[tsn_04]}{$lang[tsn_13]}{$lang[tsn_05]}[/color]{$lang[tsn_06]} [/color][color=gray]{$_G[setting][extcredits][$var[nrcredit]][title]} [/color][color=darkorange]{$credit}[/color][color=gray]{$_G[setting][extcredits][$var[nrcredit]][unit]}.{$another_vip}[/color][/color][/size][/quote][size=3][color=dimgray]{$lang[tsn_07]}[color=red]{$todaysay}[/color]{$lang[tsn_08]}[/color][/size]";
                }

                DB::query("INSERT INTO ".DB::table('forum_thread')." (fid, posttableid, readperm, price, typeid, sortid, author, authorid, subject, dateline, lastpost, lastposter, displayorder, digest, special, attachment, moderated, highlight, closed, status, isgroup) VALUES ('$var[fidnumber]', '0', '0', '0', '$var[qdtypeid]', '0', '$_G[username]', '$_G[uid]', '$subject', '$_G[timestamp]', '$_G[timestamp]', '$_G[username]', '0', '0', '0', '0', '1', '1', '1', '$topicStatus', '0')");
                $tid = DB::insert_id();
                DB::query("UPDATE ".DB::table('dsu_paulsignset')." SET qdtidnumber = '$tid' WHERE id='1'");
                $pid = insertpost(array('fid' => $var['fidnumber'],'tid' => $tid,'first' => '1','author' => $_G['username'],'authorid' => $_G['uid'],'subject' => $subject,'dateline' => $_G['timestamp'],'message' => $message,'useip' => $_G['clientip'],'invisible' => '0','anonymous' => '0','usesig' => '0','htmlon' => '0','bbcodeoff' => '0','smileyoff' => '0','parseurloff' => '0','attachment' => '0','status' =>$postStatus));
                $expiration = $_G['timestamp'] + 86400;
                DB::query("INSERT INTO ".DB::table('forum_thread')."mod (tid, uid, username, dateline, action, expiration, status) VALUES ('$tid', '$_G[uid]', '$_G[username]', '$_G[timestamp]', 'EHL', '$expiration', '1')");
                DB::query("INSERT INTO ".DB::table('forum_thread')."mod (tid, uid, username, dateline, action, expiration, status) VALUES ('$tid', '$_G[uid]', '$_G[username]', '$_G[timestamp]', 'CLS', '0', '1')");
                updatepostcredits('+', $_G['uid'], 'post', $var['fidnumber']);
                $lastpost = "$tid\t".addslashes($subject)."\t$_G[timestamp]\t$_G[username]";
                DB::query("UPDATE ".DB::table('forum_forum')." SET lastpost='$lastpost', threads=threads+1, posts=posts+1, todayposts=todayposts+1 WHERE fid='$var[fidnumber]'", 'UNBUFFERED');
                $tidnumber = $tid;
            } else {
                $tidnumber = $stats['qdtidnumber'];
                $thread = DB::fetch_first("SELECT subject FROM ".DB::table('forum_thread')." WHERE tid='$tidnumber'");
                $hft = dgmdate($_G['timestamp'], 'Y-m-d H:i',$var['tos']);
                if ($num >=1 && ($num <= ($extreward_num - 1)) && $exacr && $exacz) {
                    $message = "[quote][size=2][color=gray][color=teal] [/color][color=gray]{$lang[tsn_01]}[/color] [color=darkorange]{$hft}[/color] {$lang[tsn_02]}[color=red]{$lang[tsn_03]}[/color][color=darkorange]{$lang[tsn_04]}{$psc}{$lang[tsn_05]}[/color]{$lang[tsn_06]} [/color][color=gray]{$_G[setting][extcredits][$var[nrcredit]][title]} [/color][color=darkorange]{$credit}[/color][color=gray]{$_G[setting][extcredits][$var[nrcredit]][unit]}[/color][color=gray]{$lang[tsn_17]}[/color] [color=gray]{$_G[setting][extcredits][$exacr][title]} [/color][color=darkorange]{$exacz}[/color][color=gray]{$_G[setting][extcredits][$exacr][unit]}[/color][/color][/size][/quote][size=3][color=dimgray]{$lang[tsn_07]}[color=red]{$todaysay}[/color]{$lang[tsn_08]}[/color][/size]";
                } else {
                    $message = "[quote][size=2][color=gray][color=teal] [/color][color=gray]{$lang[tsn_01]}[/color] [color=darkorange]{$hft}[/color] {$lang[tsn_09]}{$lang[tsn_06]} [/color][color=gray]{$_G[setting][extcredits][$var[nrcredit]][title]} [/color][color=darkorange]{$credit} [/color][color=gray]{$_G[setting][extcredits][$var[nrcredit]][unit]}[/color][/size][/quote][size=3][color=dimgray]{$lang[tsn_07]}[color=red]{$todaysay}[/color]{$lang[tsn_08]}[/color][/size]";
                }

                // $message = $this->getClientApp($message, $_GET['platType']);

                $pid = insertpost(array('fid' => $var['fidnumber'],'tid' => $tidnumber,'first' => '0','author' => $_G['username'],'authorid' => $_G['uid'],'subject' => '','dateline' => $_G['timestamp'],'message' => $message,'useip' => $_G['clientip'],'invisible' => '0','anonymous' => '0','usesig' => '0','htmlon' => '0','bbcodeoff' => '0','smileyoff' => '0','parseurloff' => '0','attachment' => '0','status' => $postStatus));
                DB::query("UPDATE ".DB::table('forum_thread')." SET lastposter='$_G[username]', lastpost='$_G[timestamp]', replies=replies+1 WHERE tid='$tidnumber' AND fid='$var[fidnumber]'", 'UNBUFFERED');
                updatepostcredits('+', $_G['uid'], 'reply', $var['fidnumber']);
                $lastpost = "$tidnumber\t".addslashes($thread['subject'])."\t$_G[timestamp]\t$_G[username]";
                DB::query("UPDATE ".DB::table('forum_forum')." SET lastpost='$lastpost', posts=posts+1, todayposts=todayposts+1 WHERE fid='$var[fidnumber]'", 'UNBUFFERED');
            }
        }
        if (memory('check')) memory('set', 'dsu_pualsign_'.$_G['uid'], $_G['timestamp'], 86400);
        if ($num ==0) {
            if ($stats['todayq'] > $stats['highestq']) DB::query("UPDATE ".DB::table('dsu_paulsignset')." SET highestq='$stats[todayq]' WHERE id='1'");
            DB::query("UPDATE ".DB::table('dsu_paulsignset')." SET yesterdayq='$stats[todayq]',todayq=1 WHERE id='1'");
            DB::query("UPDATE ".DB::table('dsu_paulsignemot')." SET count=0");
        } else {
            DB::query("UPDATE ".DB::table('dsu_paulsignset')." SET todayq=todayq+1 WHERE id='1'");
        }    
        DB::query("UPDATE ".DB::table('dsu_paulsignemot')." SET count=count+1 WHERE qdxq='$_GET[qdxq]'");
        $lasted = DB::result_first("SELECT lasted FROM ".DB::table('dsu_paulsign')." WHERE uid='$_G[uid]'");
        if ($exacr && $exacz) {
            $message = "{$lang[tsn_14]}{$lang[tsn_03]}{$lang[tsn_04]}{$psc}{$lang[tsn_15]}{$lang[classn_12]}{$lasted}{$lang[classn_02]}{$lang[tsn_06]}{$_G[setting][extcredits][$var[nrcredit]][title]}{$credit}{$_G[setting][extcredits][$var[nrcredit]][unit]}{$lang[tsn_16]}{$_G[setting][extcredits][$exacr][title]}{$exacz}{$_G[setting][extcredits][$exacr][unit]}";
            return WebUtils::makeErrorInfo_oldVersion($res, $message, $params=array('noError'=>1));
        } else {
            $psc = $num + 1;
            $message = "{$lang[tsn_14]}{$lang[tsn_03]}{$lang[tsn_04]} {$psc} {$lang[tsn_15]}{$lang[classn_12]} {$lasted} {$lang[classn_02]}{$lang[tsn_06]}{$_G[setting][extcredits][$var[nrcredit]][title]} {$credit} {$_G[setting][extcredits][$var[nrcredit]][unit]}";
            return WebUtils::makeErrorInfo_oldVersion($res, $message, $params=array('noError'=>1));
        }
    }

    // 获取客户端小尾巴
     public function getClientApp($type, $platType) {

        // 判断客户端是否开启了小尾巴功能
        $status = 0;
        if (WebUtils::getDzPluginAppbymeAppConfig('mobile_allow_sign_with_sign')) {
            $status = ForumUtils::getPostSendStatus($type, $platType);
        }
        return $status;   
    }

}