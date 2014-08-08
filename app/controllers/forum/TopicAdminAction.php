<?php
/**
 *
 * @author kuanghongliang
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}
// Mobcent::setErrors();
class TopicAdminAction extends MobcentAction{
    public function run(){

        // $content = 'hello';
        // $content = rawurlencode('[{"type":0,"infor":"人生得意须尽欢,[挖鼻屎][呵呵]@liang1 "},{"type":1,"infor":"http://localhost/31u"}]');
//         $typeOption = rawurlencode('{"profile":1,"makes":1,"boolen":2,"floor":4,"price":"19999","image":"","address":"1,2"}');
//         $json = '{
//         "head":
//         {
//                 "errCode": 0,
//                 "errInfo":  "",
//         },
//         "body":
//         {
//                  "json":
//                  {
//                            "fid": 2,
//                            "tid":"",
//                            "location": "北京市",
//                            "aid":"",
//                            "content":"123",
//                            "title":"测试贴",
//                            "longitude":"116.302891",
//                            "latitude":"40.055069",
//                            "isOnlyAuthor":0,
//                            "isHidden":0,
//                            "isAnonymous":1,
//                            "isShowPostion":1,
//                            "isQuote":"",
//                            "replyId":"",
//                            "sortId":"",
//                            "typeId":"",
//                            "typeOption":"'.$typeOption.'",
//                  },
//                  "externInfo":
//                  {
//                  },
//         },
// }';
        $json = $_REQUEST['json'];
        $json = rawurldecode($json);
        $info = WebUtils::jsonDecode($json);

        $jsonInfo = !empty($info['body']['json'])?$info['body']['json']:array();

        $act = $_GET['act'];
        $res = $this->_sendPostResult($jsonInfo, $act);
        echo WebUtils::outputWebApi($res, '', false);

        $this->_doPostStatistics($act);
    }

    private function _sendPostResult($jsonInfo,$act){
        $res = $this->initWebApiArray();
        switch ($act){
            case 'new':
                $res['head']['errInfo']=WebUtils::t('发贴成功');
                break;
            case 'reply':
                $res['head']['errInfo']=WebUtils::t('回贴成功');
                break;
            default:
                $res['head']['errInfo']=WebUtils::t('编辑成功');
                break;

        }
        $app = Yii::app()->getController()->mobcentDiscuzApp;
        $app->loadForum($jsonInfo['fid'], $jsonInfo['tid']);

        if (($checkMessage = mobcent_cknewuser()) != '') {
            return $this->makeErrorInfo($res, WebUtils::emptyHtml($checkMessage));
        }

        require_once libfile('class/credit');
        require_once libfile('function/post');

        global $_G;

        if (($_G['forum']['simple'] & 1) || $_G['forum']['redirect']) {
            return $this->makeErrorInfo($res, lang('message', 'forum_disablepost'));
        }

        /*初始化变量*/
        $pid = 0;
        $sortid = 0;
        $typeid = 0;
        $special = 0;
        $readperm=0;
        $_GET['tid'] = $jsonInfo['tid'];
        $_GET['fid'] = $jsonInfo['fid'];
        $_G['tid'] = $jsonInfo['tid'];
        $_GET['typeoption'] = WebUtils::jsonDecode(rawurldecode($jsonInfo['typeOption']));
        $typeInfo = array();
        foreach($_GET['typeoption'] as $k => $v){
            $typeInfo[$k] = WebUtils::t($v);
        }
        $_GET['typeoption'] = $typeInfo;
        $_GET['isanonymous'] =$jsonInfo['isAnonymous'];
        $_GET['hiddenreplies'] =$jsonInfo['isOnlyAuthor'];
        $_GET['usesig'] = 1;
        $_GET['allownoticeauthor'] = 1;
        if($jsonInfo['typeId']){
            $typeid = $jsonInfo['typeId'];
        }

        //copy dz from source/module/forum/forum_post.php
        $postinfo = array('subject' => '');
        $thread = array('readperm' => '', 'pricedisplay' => '', 'hiddenreplies' => '');

        $_G['forum_dtype'] = $_G['forum_checkoption'] = $_G['forum_optionlist'] = $tagarray = $_G['forum_typetemplate'] = array();


        if($jsonInfo['sortId'] && $jsonInfo['sortId']>0) {
            $sortid = $jsonInfo['sortId'];
            require_once libfile('post/threadsorts', 'include');
        }

        /*找出哪项是图片上传项和多项选择项，拼接成所想要的数组类型*/
        $optionId = DB::fetch_all("SELECT optionid FROM ".DB::table('forum_typevar')." WHERE sortid=%d",array($sortid));
        foreach($optionId as $key => $value){
            $type = DB::fetch_first("SELECT identifier,type FROM ".DB::table('forum_typeoption')." WHERE optionid=%d",array($value['optionid']));
            if($type['type'] == 'image'){
                $attachImg = DB::fetch_first("SELECT attachment FROM ".DB::table('forum_attachment_unused')." WHERE aid = %d ",array($_GET['typeoption'][$type['identifier']]));
                $attachImg = $_G['setting']['attachurl'].'forum/'.$attachImg['attachment'];
                $_GET['typeoption'][$type['identifier']] =array('aid'=>$_GET['typeoption'][$type['identifier']],'url'=>$attachImg);
            }
            if($type['type'] == 'checkbox'){
                $_GET['typeoption'][$type['identifier']] = explode(',',$_GET['typeoption'][$type['identifier']]);
            }
        }

        require_once libfile('function/discuzcode');
        if($act == 'edit' || $act == 'reply') {

            $thread = C::t('forum_thread')->fetch($_G['tid']);
            if(!$_G['forum_auditstatuson'] && !($thread['displayorder']>=0 || (in_array($thread['displayorder'], array(-4, -2)) && $thread['authorid']==$_G['uid']))) {
                $thread = array();
            }
            if(!empty($thread)) {

                if($thread['readperm'] && $thread['readperm'] > $_G['group']['readaccess'] && !$_G['forum']['ismoderator'] && $thread['authorid'] != $_G['uid']) {
                    return WebUtils::makeErrorInfo_oldVersion($res, 'thread_nopermission',array('{readperm}'=>$thread['readperm']));
                }

                $_G['fid'] = $thread['fid'];
                $special = $thread['special'];

            } else {
                return WebUtils::makeErrorInfo_oldVersion($res, 'thread_nonexistence');
            }

            if($thread['closed'] == 1 && !$_G['forum']['ismoderator']) {
                return WebUtils::makeErrorInfo_oldVersion($res, 'post_thread_closed');
            }
        }
        if($jsonInfo['isQuote'] && $jsonInfo['replyId']>0){
            $_GET['repquote'] = $jsonInfo['replyId'];
            $language = lang('forum/misc');
            $noticeauthor = $noticetrimstr = '';
            $thaquote = C::t('forum_post')->fetch('tid:'.$_G['tid'], $_GET['repquote']);
            if(!($thaquote && ($thaquote['invisible'] == 0 || $thaquote['authorid'] == $_G['uid'] && $thaquote['invisible'] == -2))) {
                $thaquote = array();
            }
            if($thaquote['tid'] != $_G['tid']) {
                return WebUtils::makeErrorInfo_oldVersion($res, 'reply_quotepost_error');
            }
            if(getstatus($thread['status'], 2) && $thaquote['authorid'] != $_G['uid'] && $_G['uid'] != $thread['authorid'] && $thaquote['first'] != 1 && !$_G['forum']['ismoderator']) {
                return WebUtils::makeErrorInfo_oldVersion($res, 'reply_quotepost_error');
            }

            if(!($thread['price'] && !$thread['special'] && $thaquote['first'])) {
                $quotefid = $thaquote['fid'];
                $message = $thaquote['message'];

                if($_G['setting']['bannedmessages'] && $thaquote['authorid']) {
                    $author = getuserbyuid($thaquote['authorid']);
                    if(!$author['groupid'] || $author['groupid'] == 4 || $author['groupid'] == 5) {
                        $message = $language['post_banned'];
                    } elseif($thaquote['status'] & 1) {
                        $message = $language['post_single_banned'];
                    }
                }

                $time = dgmdate($thaquote['dateline']);
                $message = messagecutstr($message, 100);
                $message = implode("\n", array_slice(explode("\n", $message), 0, 3));

                $thaquote['useip'] = substr($thaquote['useip'], 0, strrpos($thaquote['useip'], '.')).'.x';
                if($thaquote['author'] && $thaquote['anonymous']) {
                    $thaquote['author'] = lang('forum/misc', 'anonymoususer');
                } elseif(!$thaquote['author']) {
                    $thaquote['author'] = lang('forum/misc', 'guestuser').' '.$thaquote['useip'];
                } else {
                    $thaquote['author'] = $thaquote['author'];
                }

                $post_reply_quote = lang('forum/misc', 'post_reply_quote', array('author' => $thaquote['author'], 'time' => $time));
                $noticeauthormsg = dhtmlspecialchars($message);
                if(!defined('IN_MOBILE')) {
                    $message = "[quote][size=2][color=#999999]{$post_reply_quote}[/color] [url=forum.php?mod=redirect&goto=findpost&pid=$_GET[repquote]&ptid={$_G['tid']}][img]static/image/common/back.gif[/img][/url][/size]\n{$message}[/quote]";
                } else {
                    $message = "[quote][color=#999999]{$post_reply_quote}[/color]\n[color=#999999]{$message}[/color][/quote]";
                }
                $quotemessage = discuzcode($message, 0, 0);
                $noticeauthor = dhtmlspecialchars(authcode('q|'.$thaquote['authorid'], 'ENCODE'));
                $noticetrimstr = dhtmlspecialchars($message);
                $_GET['noticetrimstr'] =$noticetrimstr;
                $_GET['noticeauthor'] =$noticeauthor;
            }
        }
        //periodscheck('postbanperiods');

        if($_G['forum']['password'] && $_G['forum']['password'] != $_G['cookie']['fidpw'.$_G['fid']]) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'forum_passwd');
        }

        if(empty($_G['forum']['allowview'])) {
            if(!$_G['forum']['viewperm'] && !$_G['group']['readaccess']) {
                return WebUtils::makeErrorInfo_oldVersion($res, 'group_nopermission',array('{grouptitle}'=>$_G['group']['grouptitle']));
            } elseif($_G['forum']['viewperm'] && !forumperm($_G['forum']['viewperm'])) {
                $msg = mobcent_showmessagenoperm('viewperm', $_G['fid']);
                return WebUtils::makeErrorInfo_oldVersion($res,$msg['message'],$msg['params']);
            }
        } elseif($_G['forum']['allowview'] == -1) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'forum_access_view_disallow');
        }

        $msg = mobcent_formulaperm($_G['forum']['formulaperm']);
        if ($msg['message'] != '') {
            return WebUtils::makeErrorInfo_oldVersion($res, $msg['message'], $msg['params']);
        }
        //formulaperm($_G['forum']['formulaperm']);
        if(!$_G['adminid'] && $_G['setting']['newbiespan'] && (!getuserprofile('lastpost') || TIMESTAMP - getuserprofile('lastpost') < $_G['setting']['newbiespan'] * 60) && TIMESTAMP - $_G['member']['regdate'] < $_G['setting']['newbiespan'] * 60) {
            return WebUtils::makeErrorInfo_oldVersion($res,'post_newbie_span',array('{newbiespan}'=>$_G['setting']['newbiespan']));
        }

        $special = $special > 0 && $special < 7 || $special == 127 ? intval($special) : 0;
        $jsonInfo['title'] = rawurldecode($jsonInfo['title']);
        $subject = isset($jsonInfo['title']) ? dhtmlspecialchars(censor(trim($jsonInfo['title']))) : '';
        $subject = !empty($subject) ? str_replace("\t", ' ', $subject) : $subject;
        $subject = WebUtils::t($subject);
        /*贴子内容处理*/
        $_GET['attachnew'] = array();
        $aid = $jsonInfo['aid'];
        if(isset($aid) && !empty($aid)){
            $aid_Img=explode(',',$aid);
            foreach($aid_Img as $key => $value){
               $_GET['attachnew'][$value] =array('description'=>'');
            }
        }
        $message = '';
        $i=0;
        if($act=='new'){
           $act = 'newthread';
        }
        $jsonInfo['content'] = WebUtils::jsonDecode(rawurldecode($jsonInfo['content']));
        foreach ($jsonInfo['content'] as $k => $v ) {
           switch ($v ["type"]) {
               case 0 :
                   $message .= $v ["infor"]."\r\n";
                   break;
               case 1 :
                   if(empty($aid_Img)) {
                       if ($aid != 0) {
                           $message .= '[attachimg]' . $aid . '[/attachimg]';
                       } else {
                           $message .= '[img]'.$v['infor'].'[/img]';
                       }
                   } else {
                       if ($aid_Img[$i] != 0) {
                           $message .= '[attachimg]' . $aid_Img[$i] . '[/attachimg]';
                       } else {
                           $message .= '[img]'.$v['infor'].'[/img]';
                       }
                       $i=$i+1;
                   }
                   $attachment = 2;
                   $message .= "\r\n";
                   break;
               case 3:
                   $message .= "[audio]".$v ["infor"]."[/audio]";
                   break;
            }
        }

        //表情处理
        $message = $this->smilesReplace($message);
        WebUtils::getDzPluginAppbymeAppConfig('forum_allow_gbk_special') && $message = mb_convert_encoding($message, 'HTML-ENTITIES', 'UTF-8');
        $message = WebUtils::t($message);
        
        $readperm = isset($_GET['readperm']) ? intval($_GET['readperm']) : 0;
        $price = isset($_GET['price']) ? intval($_GET['price']) : 0;

        if(empty($bbcodeoff) && !$_G['group']['allowhidecode'] && !empty($message) && preg_match("/\[hide=?\d*\].*?\[\/hide\]/is", preg_replace("/(\[code\](.+?)\[\/code\])/is", ' ', $message))) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'post_hide_nopermission');
        }

        $modnewthreads = $modnewreplies = 0;
        if(($subject || $message) && empty($_GET['save'])) {
            $extramessage = ($special == 5 ? "\t".$_GET['affirmpoint']."\t".$_GET['negapoint'] : '').
            ($special == 4 ? "\t".$_GET['activityplace']."\t".$_GET['activitycity']."\t".$_GET['activityclass'] : '').
            ($special == 2 ? "\t".$_GET['item_name']."\t".$_GET['item_locus'] : '').
            ($_GET['typeoption'] ? "\t".implode("\t", $_GET['typeoption']) : '').
            ($_GET['polloptions'] || $_GET['polloption'] ? ("\t".implode("\t", $_GET['tpolloption'] == 2 ? explode("\n", $_GET['polloptions']) : $_GET['polloption'])) : '');
            list($modnewthreads, $modnewreplies) = threadmodstatus($subject."\t".$message.$extramessage);
            unset($extramessage);
        }

        $urloffcheck = $usesigcheck = $smileyoffcheck = $codeoffcheck = $htmloncheck = $emailcheck = '';

        $seccodecheck = ($_G['setting']['seccodestatus'] & 4) && (!$_G['setting']['seccodedata']['minposts'] || getuserprofile('posts') < $_G['setting']['seccodedata']['minposts']);
        $secqaacheck = $_G['setting']['secqaa']['status'] & 2 && (!$_G['setting']['secqaa']['minposts'] || getuserprofile('posts') < $_G['setting']['secqaa']['minposts']);

        $_G['group']['allowpostpoll'] = $_G['group']['allowpost'] && $_G['group']['allowpostpoll'] && ($_G['forum']['allowpostspecial'] & 1);
        $_G['group']['allowposttrade'] = $_G['group']['allowpost'] && $_G['group']['allowposttrade'] && ($_G['forum']['allowpostspecial'] & 2);
        $_G['group']['allowpostreward'] = $_G['group']['allowpost'] && $_G['group']['allowpostreward'] && ($_G['forum']['allowpostspecial'] & 4);
        $_G['group']['allowpostactivity'] = $_G['group']['allowpost'] && $_G['group']['allowpostactivity'] && ($_G['forum']['allowpostspecial'] & 8);
        $_G['group']['allowpostdebate'] = $_G['group']['allowpost'] && $_G['group']['allowpostdebate'] && ($_G['forum']['allowpostspecial'] & 16);
        $usesigcheck = $_G['uid'] && $_G['group']['maxsigsize'] ? 'checked="checked"' : '';
        $ordertypecheck = !empty($thread['tid']) && getstatus($thread['status'], 4) ? 'checked="checked"' : '';
        $specialextra = !empty($_GET['specialextra']) ? $_GET['specialextra'] : '';
        $_G['forum']['threadplugin'] = dunserialize($_G['forum']['threadplugin']);
        $_G['group']['allowanonymous'] = $_G['forum']['allowanonymous'] || $_G['group']['allowanonymous'] ? 1 : 0;
        if($specialextra) {
            $special = 127;
        }

        if($act == 'newthread') {
            $policykey = 'post';
        } elseif($act == 'reply') {
            $policykey = 'reply';
        } else {
            $policykey = '';
        }
        if($policykey) {
            $postcredits = $_G['forum'][$policykey.'credits'] ? $_G['forum'][$policykey.'credits'] : $_G['setting']['creditspolicy'][$policykey];
        }
        if($act == 'reply') {
            $allow = $this->check_allow_action($res,'allowreply');
        } else {
            $allow = $this->check_allow_action($res,'allowpost');
        }
        if($allow){
            return $allow;
        }
        if(!empty($jsonInfo['location'])){
            $jsonInfo['location'] = WebUtils::t(rawurldecode($jsonInfo['location']));
        }
        $extract = array('modnewthreads'=>$modnewthreads,
                         'modnewreplies' =>$modnewreplies,
                         'thread' => $thread,
                         'res' => $res,
                         'special' => $special,
                         'subject' => $subject,
                         'message' => $message,
                         'jsonInfo' => $jsonInfo,
                         'sortid'  => $sortid,
                         'typeid' => $typeid);
        switch($act){
            case 'newthread':
                $result = $this->sendPost($extract);
                break;
            case 'reply':
                $result = $this->replyPost($extract);
                break;
            case 'edit':
                $result = $this->editPost($extract);
                break;
        }
        if($result['errcode'] != WebUtils::t('发贴成功')){
            return $result;
        }
        $res = array_merge($result,$res);
        return $res;

    }

    private function check_allow_action($res,$action = 'allowpost') {
        global $_G;
        if(isset($_G['forum'][$action]) && $_G['forum'][$action] == -1) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'forum_access_disallow');
        }
    }

    /*表情替换图片方法*/
    private function smilesReplace($message){
        global $_G;
        $baseUrl = $_G['siteurl'] . '/mobcent/';
        $smiles = Yii::app()->params['mobcent']['phiz'];
        /* if(UC_DBCHARSET == 'gbk'){
            $smiles = WebUtils::arrayCoding($smiles,"utf-8","gbk");
        } */
        preg_match_all('/\[.*?\]/',$message,$res);
        foreach($res[0] as $k => $v){
            foreach($smiles as $key => $val){
                if($v == $key){
                    $message = str_replace($v,"[img]".$baseUrl.'/app/data/phiz/default/'.$val."[/img]",$message);
                }
            }
        }
        return $message;
    }

    /*编辑贴子接口方法*/
    private function editPost(){
        require_once libfile('post/editpost', 'include');
    }

    /*回复贴子方法*/
    private function replyPost($extract){
        global $_G;
        extract($extract);

        // 获取主题和帖子要插入的状态信息
        $topicStatus = ForumUtils::getPostSendStatus('topic', $_GET['platType']);
        $postStatus = ForumUtils::getPostSendStatus('post', $_GET['platType']);

        //$navtitle .= ' - '.$thread['subject'].' - '.$_G['forum']['name'];
        //copy from dz source/include/post/post_newreply.php
        require_once libfile('function/forumlist');

        $isfirstpost = 0;
        $showthreadsorts = 0;
        $quotemessage = '';
        if(!$_G['uid'] && !((!$_G['forum']['replyperm'] && $_G['group']['allowreply']) || ($_G['forum']['replyperm'] && forumperm($_G['forum']['replyperm'])))) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'replyperm_login_nopermission',array('{login}'=>1));
        } elseif(empty($_G['forum']['allowreply'])) {
            if(!$_G['forum']['replyperm'] && !$_G['group']['allowreply']) {
                return WebUtils::makeErrorInfo_oldVersion($res, 'replyperm_none_nopermission',array('{login}'=>1));
            } elseif($_G['forum']['replyperm'] && !forumperm($_G['forum']['replyperm'])) {
                $msg = mobcent_showmessagenoperm('replyperm', $_G['forum']['fid']);
                return WebUtils::makeErrorInfo_oldVersion($res,$msg['message'],$msg['params']);
            }
        } elseif($_G['forum']['allowreply'] == -1) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'post_forum_newreply_nopermission');
        }

        if(!$_G['uid'] && ($_G['setting']['need_avatar'] || $_G['setting']['need_email'] || $_G['setting']['need_friendnum'])) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'replyperm_login_nopermission',array('{login}'=>1));
        }

        if(empty($thread)) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'thread_nonexistence');
        } elseif($thread['price'] > 0 && $thread['special'] == 0 && !$_G['uid']) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'group_nopermission',array('{grouptitle}'=>$_G['group']['grouptitle']));
        }

        checklowerlimit('reply', 0, 1, $_G['forum']['fid']);

        if($_G['setting']['commentnumber'] && !empty($_GET['comment'])) {
            if(!submitcheck('commentsubmit', 0, $seccodecheck, $secqaacheck)) {
                return WebUtils::makeErrorInfo_oldVersion($res, 'forum_passwd');
                showmessage('submitcheck_error', NULL);
            }
            $post = C::t('forum_post')->fetch('tid:'.$_G['tid'], $_GET['pid']);
            if(!$post) {
                return WebUtils::makeErrorInfo_oldVersion($res, 'post_nonexistence');
            }
            if($thread['closed'] && !$_G['forum']['ismoderator'] && !$thread['isgroup']) {
                return WebUtils::makeErrorInfo_oldVersion($res, 'post_thread_closed');
            } elseif(!$thread['isgroup'] && $post_autoclose = checkautoclose($thread)) {
                return WebUtils::makeErrorInfo_oldVersion($res, $post_autoclose,array('{autoclose}'=>$_G['forum']['autoclose']));
            } elseif(checkflood()) {
                return WebUtils::makeErrorInfo_oldVersion($res, 'post_flood_ctrl',array('{floodctrl}'=>$_G['setting']['floodctrl']));
            } elseif(checkmaxperhour('pid')) {
                return WebUtils::makeErrorInfo_oldVersion($res, 'post_flood_ctrl_posts_per_hour',array('posts_per_hour'=>$_G['group']['maxpostsperhour']));
            }
            $commentscore = '';
            if(!empty($_GET['commentitem']) && !empty($_G['uid']) && $post['authorid'] != $_G['uid']) {
                foreach($_GET['commentitem'] as $itemk => $itemv) {
                    if($itemv !== '') {
                        $commentscore .= strip_tags(trim($itemk)).': <i>'.intval($itemv).'</i> ';
                    }
                }
            }
            $comment = cutstr(($commentscore ? $commentscore.'<br />' : '').censor(trim(dhtmlspecialchars($_GET['message'])), '***'), 200, ' ');
            if(!$comment) {
                return WebUtils::makeErrorInfo_oldVersion($res, 'post_sm_isnull');
            }
            C::t('forum_postcomment')->insert(array(
            'tid' => $post['tid'],
            'pid' => $post['pid'],
            'author' => $_G['username'],
            'authorid' => $_G['uid'],
            'dateline' => TIMESTAMP,
            'comment' => $comment,
            'score' => $commentscore ? 1 : 0,
            'useip' => $_G['clientip'],
            ));
            C::t('forum_post')->update('tid:'.$_G['tid'], $_GET['pid'], array('comment' => 1));
            if(!empty($_G['uid'])){
                Mobcent::import(MOBCENT_APP_ROOT . '/components/discuz/source/function/function_post.php');
                mobcent_updatepostcredits('+', $_G['uid'], 'reply', $_G['fid']);
            }
            if(!empty($_G['uid']) && $_G['uid'] != $post['authorid']) {
                notification_add($post['authorid'], 'pcomment', 'comment_add', array(
                'tid' => $_G['tid'],
                'pid' => $_GET['pid'],
                'subject' => $thread['subject'],
                'from_id' => $_G['tid'],
                'from_idtype' => 'pcomment',
                'commentmsg' => cutstr(str_replace(array('[b]', '[/b]', '[/color]'), '', preg_replace("/\[color=([#\w]+?)\]/i", "", $comment)), 200)
                ));
            }
            update_threadpartake($post['tid']);
            $pcid = C::t('forum_postcomment')->fetch_standpoint_by_pid($_GET['pid']);
            $pcid = $pcid['id'];
            if(!empty($_G['uid']) && $_GET['commentitem']) {
                $totalcomment = array();
                foreach(C::t('forum_postcomment')->fetch_all_by_pid_score($_GET['pid'], 1) as $comment) {
                    $comment['comment'] = addslashes($comment['comment']);
                    if(strexists($comment['comment'], '<br />')) {
                        if(preg_match_all("/([^:]+?):\s<i>(\d+)<\/i>/", $comment['comment'], $a)) {
                            foreach($a[1] as $k => $itemk) {
                                $totalcomment[trim($itemk)][] = $a[2][$k];
                            }
                        }
                    }
                }
                $totalv = '';
                foreach($totalcomment as $itemk => $itemv) {
                    $totalv .= strip_tags(trim($itemk)).': <i>'.(floatval(sprintf('%1.1f', array_sum($itemv) / count($itemv)))).'</i> ';
                }

                if($pcid) {
                    C::t('forum_postcomment')->update($pcid, array('comment' => $totalv, 'dateline' => TIMESTAMP + 1));
                } else {
                    C::t('forum_postcomment')->insert(array(
                    'tid' => $post['tid'],
                    'pid' => $post['pid'],
                    'author' => '',
                    'authorid' => '-1',
                    'dateline' => TIMESTAMP + 1,
                    'comment' => $totalv
                    ));
                }
            }
            C::t('forum_postcache')->delete($post['pid']);
            return WebUtils::makeErrorInfo_oldVersion($res, 'comment_add_succeed');
            //showmessage('comment_add_succeed', "forum.php?mod=viewthread&tid=$post[tid]&pid=$post[pid]&page=$_GET[page]&extra=$extra#pid$post[pid]", array('tid' => $post['tid'], 'pid' => $post['pid']));
        }

        if($special == 127) {
            $postinfo = C::t('forum_post')->fetch_threadpost_by_tid_invisible($_G['tid']);
            $sppos = strrpos($postinfo['message'], chr(0).chr(0).chr(0));
            $specialextra = substr($postinfo['message'], $sppos + 3);
        }
        if(getstatus($thread['status'], 3)) {
            $rushinfo = C::t('forum_threadrush')->fetch($_G['tid']);
            if($rushinfo['creditlimit'] != -996) {
                $checkcreditsvalue = $_G['setting']['creditstransextra'][11] ? getuserprofile('extcredits'.$_G['setting']['creditstransextra'][11]) : $_G['member']['credits'];
                if($checkcreditsvalue < $rushinfo['creditlimit']) {
                    $creditlimit_title = $_G['setting']['creditstransextra'][11] ? $_G['setting']['extcredits'][$_G['setting']['creditstransextra'][11]]['title'] : lang('forum/misc', 'credit_total');
                    return WebUtils::makeErrorInfo_oldVersion($res, 'forum_passwd');
                    showmessage('post_rushreply_creditlimit', '', array('creditlimit_title' => $creditlimit_title, 'creditlimit' => $rushinfo['creditlimit']));
                }
            }

        }

        if($thread['closed'] && !$_G['forum']['ismoderator'] && !$thread['isgroup']) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'post_thread_closed');
        } elseif(!$thread['isgroup'] && $post_autoclose = checkautoclose($thread)) {
            return WebUtils::makeErrorInfo_oldVersion($res, $post_autoclose,array('{autoclose}'=>$_G['forum']['autoclose']));
        } if(trim($subject) == '' && trim($message) == '' && $thread['special'] != 2) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'post_sm_isnull');
        } elseif($post_invalid = checkpost($subject, $message, $special == 2 && $_G['group']['allowposttrade'])) {
            return WebUtils::makeErrorInfo_oldVersion($res, $post_invalid,array('{minpostsize}'=>$_G['setting']['minpostsize']),array('{maxpostsize}'=>$_G['setting']['maxpostsize']));
            //showmessage($post_invalid, '', array('minpostsize' => $_G['setting']['minpostsize'], 'maxpostsize' => $_G['setting']['maxpostsize']));
        } elseif(checkflood()) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'post_flood_ctrl',array('{floodctrl}'=>$_G['setting']['floodctrl']));
            //showmessage('post_flood_ctrl', '', array('floodctrl' => $_G['setting']['floodctrl']));
        } elseif(checkmaxperhour('pid')) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'post_flood_ctrl_posts_per_hour',array('{posts_per_hour}'=>$_G['group']['maxpostsperhour']));
            //showmessage('post_flood_ctrl_posts_per_hour', '', array('posts_per_hour' => $_G['group']['maxpostsperhour']));
        }

        $attentionon = empty($_GET['attention_add']) ? 0 : 1;
        $attentionoff = empty($attention_remove) ? 0 : 1;
        $heatthreadset = update_threadpartake($_G['tid'], true);
        if($_G['group']['allowat']) {
            $atlist = $atlist_tmp = $ateduids = array();
            preg_match_all("/@([^\r\n]*?)\s/i", $message.' ', $atlist_tmp);
            $atlist_tmp = array_slice(array_unique($atlist_tmp[1]), 0, $_G['group']['allowat']);
            $atnum = $maxselect = 0;
            foreach(C::t('home_notification')->fetch_all_by_authorid_fromid($_G['uid'], $_G['tid'], 'at') as $row) {
                $atnum ++;
                $ateduids[$row[uid]] = $row['uid'];
            }
            $maxselect = $_G['group']['allowat'] - $atnum;
            if($maxselect > 0 && !empty($atlist_tmp)) {
                if(empty($_G['setting']['at_anyone'])) {
                    foreach(C::t('home_follow')->fetch_all_by_uid_fusername($_G['uid'], $atlist_tmp) as $row) {
                        if(!in_array($row['followuid'], $ateduids)) {
                            $atlist[$row[followuid]] = $row['fusername'];
                        }
                        if(count($atlist) == $maxselect) {
                            break;
                        }
                    }
                    if(count($atlist) < $maxselect) {
                        $query = C::t('home_friend')->fetch_all_by_uid_username($_G['uid'], $atlist_tmp);
                        foreach($query as $row) {
                            if(!in_array($row['followuid'], $ateduids)) {
                                $atlist[$row[fuid]] = $row['fusername'];
                            }
                        }
                    }
                } else {
                    foreach(C::t('common_member')->fetch_all_by_username($atlist_tmp) as $row) {
                        if(!in_array($row['uid'], $ateduids)) {
                            $atlist[$row[uid]] = $row['username'];
                        }
                        if(count($atlist) == $maxselect) {
                            break;
                        }
                    }
                }
            }
            if($atlist) {
                foreach($atlist as $atuid => $atusername) {
                    $atsearch[] = "/@".str_replace('/', '\/', preg_quote($atusername))." /i";
                    $atreplace[] = "[url=home.php?mod=space&uid=$atuid]@{$atusername}[/url] ";
                }
                $message = preg_replace($atsearch, $atreplace, $message.' ', 1);
            }
        }
        $bbcodeoff = checkbbcodes($message, !empty($_GET['bbcodeoff']));
        $smileyoff = checksmilies($message, !empty($_GET['smileyoff']));
        $parseurloff = !empty($_GET['parseurloff']);
        $htmlon = $_G['group']['allowhtml'] && !empty($_GET['htmlon']) ? 1 : 0;
        $usesig = !empty($_GET['usesig']) && $_G['group']['maxsigsize'] ? 1 : 0;

        $isanonymous = $_G['group']['allowanonymous'] && !empty($_GET['isanonymous'])? 1 : 0;
        $author = empty($isanonymous) ? $_G['username'] : '';

        if($thread['displayorder'] == -4) {
            $modnewreplies = 0;
        }
        $pinvisible = $modnewreplies ? -2 : ($thread['displayorder'] == -4 ? -3 : 0);
        $message = preg_replace('/\[attachimg\](\d+)\[\/attachimg\]/is', '[attach]\1[/attach]', $message);
        $postcomment = in_array(2, $_G['setting']['allowpostcomment']) && $_G['group']['allowcommentreply'] && !$pinvisible && !empty($_GET['reppid']) && ($nauthorid != $_G['uid'] || $_G['setting']['commentpostself']) ? messagecutstr($message, 200, ' ') : '';

        if(!empty($_GET['noticetrimstr'])) {
            $message = $_GET['noticetrimstr']."\n\n".$message;
            $bbcodeoff = false;
        }

        $pid = insertpost(array(
                'fid' => $_G['fid'],
                'tid' => $_G['tid'],
                'first' => '0',
                'author' => $_G['username'],
                'authorid' => $_G['uid'],
                'subject' => $subject,
                'dateline' => $_G['timestamp'],
                'message' => $message,
                'useip' => $_G['clientip'],
                'invisible' => $pinvisible,
                'anonymous' => $isanonymous,
                'usesig' => $usesig,
                'htmlon' => $htmlon,
                'bbcodeoff' => $bbcodeoff,
                'smileyoff' => $smileyoff,
                'parseurloff' => $parseurloff,
                'attachment' => '0',
                'status' => (defined('IN_MOBILE') ? 8 : 0)|$postStatus,
        ));
        if($_G['group']['allowat'] && $atlist) {
            foreach($atlist as $atuid => $atusername) {
                notification_add($atuid, 'at', 'at_message', array('from_id' => $_G['tid'], 'from_idtype' => 'at', 'buyerid' => $_G['uid'], 'buyer' => $_G['username'], 'tid' => $_G['tid'], 'subject' => $thread['subject'], 'pid' => $pid, 'message' => messagecutstr($message, 150)));
            }
            set_atlist_cookie(array_keys($atlist));
        }
        $updatethreaddata = $heatthreadset ? $heatthreadset : array();
        $postionid = C::t('forum_post')->fetch_maxposition_by_tid($thread['posttableid'], $_G['tid']);
        $updatethreaddata[] = DB::field('maxposition', $postionid);
        if(getstatus($thread['status'], 3) && $postionid) {
            $rushstopfloor = $rushinfo['stopfloor'];
            if($rushstopfloor > 0 && $thread['closed'] == 0 && $postionid >= $rushstopfloor) {
                $updatethreaddata[] = 'closed=1';
            }
        }
        useractionlog($_G['uid'], 'pid');

        $nauthorid = 0;
        if(!empty($_GET['noticeauthor']) && !$isanonymous && !$modnewreplies) {
            list($ac, $nauthorid) = explode('|', authcode($_GET['noticeauthor'], 'DECODE'));
            if($nauthorid != $_G['uid']) {
                if($ac == 'q') {
                    notification_add($nauthorid, 'post', 'reppost_noticeauthor', array(
                    'tid' => $thread['tid'],
                    'subject' => $thread['subject'],
                    'fid' => $_G['fid'],
                    'pid' => $pid,
                    'from_id' => $pid,
                    'from_idtype' => 'quote',
                    ));
                } elseif($ac == 'r') {
                    notification_add($nauthorid, 'post', 'reppost_noticeauthor', array(
                    'tid' => $thread['tid'],
                    'subject' => $thread['subject'],
                    'fid' => $_G['fid'],
                    'pid' => $pid,
                    'from_id' => $thread['tid'],
                    'from_idtype' => 'post',
                    ));
                }
            }

            if($postcomment) {
                $rpid = intval($_GET['reppid']);
                if($rpost = C::t('forum_post')->fetch('tid:'.$thread['tid'], $rpid)) {
                    if(!$rpost['first']) {
                        C::t('forum_postcomment')->insert(array(
                        'tid' => $thread['tid'],
                        'pid' => $rpid,
                        'rpid' => $pid,
                        'author' => $_G['username'],
                        'authorid' => $_G['uid'],
                        'dateline' => TIMESTAMP,
                        'comment' => $postcomment,
                        'score' => 0,
                        'useip' => $_G['clientip'],
                        ));
                        C::t('forum_post')->update('tid:'.$thread['tid'], $rpid, array('comment' => 1));
                        C::t('forum_postcache')->delete($rpid);
                    }
                }
                unset($postcomment);
            }
        }

        if($thread['authorid'] != $_G['uid'] && getstatus($thread['status'], 6) && empty($_GET['noticeauthor']) && !$isanonymous && !$modnewreplies) {
            $thapost = C::t('forum_post')->fetch_threadpost_by_tid_invisible($_G['tid'], 0);
            notification_add($thapost['authorid'], 'post', 'reppost_noticeauthor', array(
            'tid' => $thread['tid'],
            'subject' => $thread['subject'],
            'fid' => $_G['fid'],
            'pid' => $pid,
            'from_id' => $thread['tid'],
            'from_idtype' => 'post',
            ));
        }
        $feedid = 0;
        if(helper_access::check_module('follow') && !empty($_GET['adddynamic']) && !$isanonymous) {
            require_once libfile('function/discuzcode');
            require_once libfile('function/followcode');
            $feedcontent = C::t('forum_threadpreview')->count_by_tid($thread['tid']);
            $firstpost = C::t('forum_post')->fetch_threadpost_by_tid_invisible($thread['tid']);

            if(empty($feedcontent)) {
                $feedcontent = array(
                        'tid' => $thread['tid'],
                        'content' => followcode($firstpost['message'], $thread['tid'], $pid, 1000),
                );
                C::t('forum_threadpreview')->insert($feedcontent);
                C::t('forum_thread')->update_status_by_tid($thread['tid'], '512');
            } else {
                C::t('forum_threadpreview')->update_relay_by_tid($thread['tid'], 1);
            }

            $notemsg = cutstr(followcode($message, $thread['tid'], $pid, 0, false), 140);
            $followfeed = array(
                    'uid' => $_G['uid'],
                    'username' => $_G['username'],
                    'tid' => $thread['tid'],
                    'note' => $notemsg,
                    'dateline' => TIMESTAMP
            );
            $feedid = C::t('home_follow_feed')->insert($followfeed, true);
            C::t('common_member_count')->increase($_G['uid'], array('feeds'=>1));

        }

        if($thread['replycredit'] > 0 && !$modnewreplies && $thread['authorid'] != $_G['uid'] && $_G['uid']) {

            $replycredit_rule = C::t('forum_replycredit')->fetch($_G['tid']);
            if(!empty($replycredit_rule['times'])) {
                $have_replycredit = C::t('common_credit_log')->count_by_uid_operation_relatedid($_G['uid'], 'RCA', $_G['tid']);
                if($replycredit_rule['membertimes'] - $have_replycredit > 0 && $thread['replycredit'] - $replycredit_rule['extcredits'] >= 0) {
                    $replycredit_rule['extcreditstype'] = $replycredit_rule['extcreditstype'] ? $replycredit_rule['extcreditstype'] : $_G['setting']['creditstransextra'][10];
                    if($replycredit_rule['random'] > 0) {
                        $rand = rand(1, 100);
                        $rand_replycredit = $rand <= $replycredit_rule['random'] ? true : false ;
                    } else {
                        $rand_replycredit = true;
                    }
                    if($rand_replycredit) {
                        updatemembercount($_G['uid'], array($replycredit_rule['extcreditstype'] => $replycredit_rule['extcredits']), 1, 'RCA', $_G[tid]);
                        C::t('forum_post')->update('tid:'.$_G['tid'], $pid, array('replycredit' => $replycredit_rule['extcredits']));
                        $updatethreaddata[] = DB::field('replycredit', $thread['replycredit'] - $replycredit_rule['extcredits']);
                    }
                }
            }
        }

        ($_G['group']['allowpostattach'] || $_G['group']['allowpostimage']) && ($_GET['attachnew'] || $special == 2 && $_GET['tradeaid']) && updateattach($thread['displayorder'] == -4 || $modnewreplies, $_G['tid'], $pid, $_GET['attachnew']);

        $replymessage = 'post_reply_succeed';
        if($special == 2 && $_G['group']['allowposttrade'] && $thread['authorid'] == $_G['uid'] && !empty($_GET['trade']) && !empty($_GET['item_name'])) {

            require_once libfile('function/trade');
            trade_create(array(
            'tid' => $_G['tid'],
            'pid' => $pid,
            'aid' => $_GET['tradeaid'],
            'item_expiration' => $_GET['item_expiration'],
            'thread' => $thread,
            'discuz_uid' => $_G['uid'],
            'author' => $author,
            'seller' => empty($_GET['paymethod']) && $_GET['seller'] ? dhtmlspecialchars(trim($_GET['seller'])) : '',
            'item_name' => $_GET['item_name'],
            'item_price' => $_GET['item_price'],
            'item_number' => $_GET['item_number'],
            'item_quality' => $_GET['item_quality'],
            'item_locus' => $_GET['item_locus'],
            'transport' => $_GET['transport'],
            'postage_mail' => $_GET['postage_mail'],
            'postage_express' => $_GET['postage_express'],
            'postage_ems' => $_GET['postage_ems'],
            'item_type' => $_GET['item_type'],
            'item_costprice' => $_GET['item_costprice'],
            'item_credit' => $_GET['item_credit'],
            'item_costcredit' => $_GET['item_costcredit']
            ));

            $replymessage = 'trade_add_succeed';
            if(!empty($_GET['tradeaid'])) {
                convertunusedattach($_GET['tradeaid'], $_G['tid'], $pid);
            }

        }


        $_G['forum']['threadcaches'] && deletethreadcaches($_G['tid']);

        include_once libfile('function/stat');
        updatestat($thread['isgroup'] ? 'grouppost' : 'post');

        $param = array('fid' => $_G['fid'], 'tid' => $_G['tid'], 'pid' => $pid, 'from' => $_GET['from'], 'sechash' => !empty($_GET['sechash']) ? $_GET['sechash'] : '');
        if($feedid) {
            $param['feedid'] = $feedid;
        }
        dsetcookie('clearUserdata', 'forum');

        if($modnewreplies) {
            updatemoderate('pid', $pid);
            unset($param['pid']);
            if($updatethreaddata) {
                C::t('forum_thread')->update($_G['tid'], $updatethreaddata, false, false, 0, true);
            }
            C::t('forum_forum')->update_forum_counter($_G['fid'], 0, 0, 1, 1);
            $url = empty($_POST['portal_referer']) ? ("forum.php?mod=viewthread&tid={$thread[tid]}") :  $_POST['portal_referer'];
            manage_addnotify('verifypost');
           // if(!isset($inspacecpshare)) {
                return WebUtils::makeErrorInfo_oldVersion($res, 'post_reply_mod_succeed', array('noError' => 1));
                //showmessage('post_reply_mod_succeed', $url, $param);
           // }
        } else {

            $fieldarr = array(
                    'lastposter' => array($author),
                    'replies' => 1
            );
            if($thread['lastpost'] < $_G['timestamp']) {
                $fieldarr['lastpost'] = array($_G['timestamp']);
            }
            $row = C::t('forum_threadaddviews')->fetch($_G['tid']);
            if(!empty($row)) {
                C::t('forum_threadaddviews')->update($_G['tid'], array('addviews' => 0));
                $fieldarr['views'] = $row['addviews'];
            }
            $updatethreaddata = array_merge($updatethreaddata, C::t('forum_thread')->increase($_G['tid'], $fieldarr, false, 0, true));
            if($thread['displayorder'] != -4) {
                Mobcent::import(MOBCENT_APP_ROOT . '/components/discuz/source/function/function_post.php');
                mobcent_updatepostcredits('+', $_G['uid'], 'reply', $_G['fid']);
                if($_G['forum']['status'] == 3) {
                    if($_G['forum']['closed'] > 1) {
                        C::t('forum_thread')->increase($_G['forum']['closed'], $fieldarr, true);
                    }
                    C::t('forum_groupuser')->update_counter_for_user($_G['uid'], $_G['fid'], 0, 1);
                    C::t('forum_forumfield')->update($_G['fid'], array('lastupdate' => TIMESTAMP));
                    require_once libfile('function/grouplog');
                    updategroupcreditlog($_G['fid'], $_G['uid']);
                }

                $lastpost = "$thread[tid]\t$thread[subject]\t$_G[timestamp]\t$author";
                C::t('forum_forum')->update($_G['fid'], array('lastpost' => $lastpost));
                C::t('forum_forum')->update_forum_counter($_G['fid'], 0, 1, 1);
                if($_G['forum']['type'] == 'sub') {
                    C::t('forum_forum')->update($_G['forum']['fup'], array('lastpost' => $lastpost));
                }
            }

            $page = getstatus($thread['status'], 4) ? 1 : @ceil(($thread['special'] ? $thread['replies'] + 1 : $thread['replies'] + 2) / $_G['ppp']);

            if($updatethreaddata) {
                C::t('forum_thread')->update($_G['tid'], $updatethreaddata, false, false, 0, true);
            }
           /*  if(!isset($inspacecpshare)) {
                // showmessage($replymessage, $url, $param);
            } */
        }
        if($jsonInfo['isShowPostion'] && $jsonInfo['location']){
            $data = DB::query('INSERT INTO  %t VALUES(poi_id,%f,%f,%d,%d,%s)',array('home_surrounding_user',$jsonInfo['longitude'],$jsonInfo['latitude'],$pid,2,$jsonInfo['location']));
        }
         /* //客户端回复帖子积分入库
            $temp = DB::fetch_first('SELECT extcredits3 FROM '.DB::table('common_credit_rule').' WHERE rid =%d',array(2));
            $extcredits3 = DB::fetch_first('SELECT extcredits3 FROM '.DB::table('common_member_count').' WHERE uid = %d',array($_G['uid']));
            $temp = $extcredits3['extcredits3'] + $temp['extcredits3'];
            DB::query('UPDATE '.DB::table('common_member_count').' set extcredits3 = %d WHERE uid = %d',array($temp,$_G['uid']));
         */
            return array('rs'=>1,'errcode'=>WebUtils::t('发贴成功'));
    }

    /*发普通贴和分类贴方法*/
    private function sendPost($extract){
        global $_G;
        extract($extract);
        // 获取主题和帖子要插入的状态信息
        $topicStatus = ForumUtils::getPostSendStatus('topic', $_GET['platType']);
        $postStatus = ForumUtils::getPostSendStatus('post', $_GET['platType']);

        //copy from dz source/include/post/post_newthread.php
        if(empty($_G['forum']['fid']) || $_G['forum']['type'] == 'group') {
            return WebUtils::makeErrorInfo_oldVersion($res, 'forum_nonexistence');
        }

        if(($special == 1 && !$_G['group']['allowpostpoll']) || ($special == 2 && !$_G['group']['allowposttrade']) || ($special == 3 && !$_G['group']['allowpostreward']) || ($special == 4 && !$_G['group']['allowpostactivity']) || ($special == 5 && !$_G['group']['allowpostdebate'])) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'group_nopermission',array('{grouptitle}'=>$_G['group']['grouptitle']));
        }

        if(!$_G['uid'] && !((!$_G['forum']['postperm'] && $_G['group']['allowpost']) || ($_G['forum']['postperm'] && forumperm($_G['forum']['postperm'])))) {
            if(!defined('IN_MOBILE')) {
                return WebUtils::makeErrorInfo_oldVersion($res, 'postperm_login_nopermission',array('{login}'=>1));
            } else {
                return WebUtils::makeErrorInfo_oldVersion($res, 'postperm_login_nopermission_mobile',array('{login}'=>1));
            }
        } elseif(empty($_G['forum']['allowpost'])) {
            if(!$_G['forum']['postperm'] && !$_G['group']['allowpost']) {
                return WebUtils::makeErrorInfo_oldVersion($res, 'postperm_none_nopermission');
            } elseif($_G['forum']['postperm'] && !forumperm($_G['forum']['postperm'])) {
                $msg = mobcent_showmessagenoperm('postperm', $_G['fid'],$_G['forum']['formulaperm']);
                return WebUtils::makeErrorInfo_oldVersion($res,$msg['message'],$msg['params']);
            }
        } elseif($_G['forum']['allowpost'] == -1) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'post_forum_newthread_nopermission');
        }

        if(!$_G['uid'] && ($_G['setting']['need_avatar'] || $_G['setting']['need_email'] || $_G['setting']['need_friendnum'])) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'postperm_login_nopermission');
        }

        if(trim($subject) == '') {
            return WebUtils::makeErrorInfo_oldVersion($res, 'post_sm_isnull');
        }

        if(!$sortid && !$special && trim($message) == '') {
            return WebUtils::makeErrorInfo_oldVersion($res, 'post_sm_isnull');
        }
        if($post_invalid = checkpost($subject, $message, ($special || $sortid))) {
            return WebUtils::makeErrorInfo_oldVersion($res, $post_invalid,array('{minpostsize}'=>$_G['setting']['minpostsize']),array('{maxpostsize}'=>$_G['setting']['maxpostsize']));
            //showmessage($post_invalid, '', array('minpostsize' => $_G['setting']['minpostsize'], 'maxpostsize' => $_G['setting']['maxpostsize']));
        }

        if(checkflood()) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'post_flood_ctrl',array('{floodctrl}'=>$_G['setting']['floodctrl']));
        } elseif(checkmaxperhour('tid')) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'thread_flood_ctrl_threads_per_hour',array('{threads_per_hour}'=>$_G['group']['maxthreadsperhour']));
        }
        $_GET['save'] = $_G['uid'] ? $_GET['save'] : 0;

        if ($_G['group']['allowsetpublishdate'] && $_GET['cronpublish'] && $_GET['cronpublishdate']) {
            $publishdate = strtotime($_GET['cronpublishdate']);
            if ($publishdate > $_G['timestamp']) {
                $_GET['save'] = 1;
            } else {
                $publishdate = $_G['timestamp'];
            }
        } else {
            $publishdate = $_G['timestamp'];
        }
        $typeid = isset($typeid) && isset($_G['forum']['threadtypes']['types'][$typeid]) && (empty($_G['forum']['threadtypes']['moderators'][$typeid]) || $_G['forum']['ismoderator']) ? $typeid : 0;
        $displayorder = $modnewthreads ? -2 : (($_G['forum']['ismoderator'] && $_G['group']['allowstickthread'] && !empty($_GET['sticktopic'])) ? 1 : (empty($_GET['save']) ? 0 : -4));
        if($displayorder == -2) {
            C::t('forum_forum')->update($_G['fid'], array('modworks' => '1'));
        } elseif($displayorder == -4) {
            $_GET['addfeed'] = 0;
        }
        $digest = $_G['forum']['ismoderator'] && $_G['group']['allowdigestthread'] && !empty($_GET['addtodigest']) ? 1 : 0;
        $readperm = $_G['group']['allowsetreadperm'] ? $readperm : 0;
        $isanonymous = $_G['group']['allowanonymous'] && $_GET['isanonymous'] ? 1 : 0;
        $price = intval($price);
        $price = $_G['group']['maxprice'] && !$special ? ($price <= $_G['group']['maxprice'] ? $price : $_G['group']['maxprice']) : 0;

        //强制主题类别判断
        if(!$typeid && $_G['forum']['threadtypes']['required'] && !$special) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'post_type_isnull');
        }

        //强制主题分类判断
        if(!$sortid && $_G['forum']['threadsorts']['required'] && !$special) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'post_sort_isnull');
        }

        //主题售价 客户端暂不支持
        if($price > 0 && floor($price * (1 - $_G['setting']['creditstax'])) == 0) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'forum_passwd');
            showmessage('post_net_price_iszero');
        }

        //投票贴相关
        if($special == 1) {

            $polloption = $_GET['tpolloption'] == 2 ? explode("\n", $_GET['polloptions']) : $_GET['polloption'];
            $pollarray = array();
            foreach($polloption as $key => $value) {
                $polloption[$key] = censor($polloption[$key]);
                if(trim($value) === '') {
                    unset($polloption[$key]);
                }
            }

            if(count($polloption) > $_G['setting']['maxpolloptions']) {
                return WebUtils::makeErrorInfo_oldVersion($res, 'forum_passwd');
                showmessage('post_poll_option_toomany', '', array('maxpolloptions' => $_G['setting']['maxpolloptions']));
            } elseif(count($polloption) < 2) {
                return WebUtils::makeErrorInfo_oldVersion($res, 'forum_passwd');
                showmessage('post_poll_inputmore');
            }

            $curpolloption = count($polloption);
            $pollarray['maxchoices'] = empty($_GET['maxchoices']) ? 0 : ($_GET['maxchoices'] > $curpolloption ? $curpolloption : $_GET['maxchoices']);
            $pollarray['multiple'] = empty($_GET['maxchoices']) || $_GET['maxchoices'] == 1 ? 0 : 1;
            $pollarray['options'] = $polloption;
            $pollarray['visible'] = empty($_GET['visibilitypoll']);
            $pollarray['overt'] = !empty($_GET['overt']);

            if(preg_match("/^\d*$/", trim($_GET['expiration']))) {
                if(empty($_GET['expiration'])) {
                    $pollarray['expiration'] = 0;
                } else {
                    $pollarray['expiration'] = TIMESTAMP + 86400 * $_GET['expiration'];
                }
            } else {
                return WebUtils::makeErrorInfo_oldVersion($res, 'forum_passwd');
                showmessage('poll_maxchoices_expiration_invalid');
            }

        }

        // 分类信息有效期
        $_GET['typeexpiration'] = $_GET['typeoption']['typeexpiration'];

        $sortid = $special && $_G['forum']['threadsorts']['types'][$sortid] ? 0 : $sortid;
        $typeexpiration = intval($_GET['typeexpiration']);

        if($_G['forum']['threadsorts']['expiration'][$typeid] && !$typeexpiration) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'threadtype_expiration_invalid');
        }

        $_G['forum_optiondata'] = array();
        if($_G['forum']['threadsorts']['types'][$sortid] && !$_G['forum']['allowspecialonly']) {
            Mobcent::import(MOBCENT_APP_ROOT . '/components/discuz/source/function/function_threadsort.php');
            $_G['forum_optiondata'] = mobcent_threadsort_validator($_GET['typeoption'], $pid);
            if ($_G['forum_optiondata']['message'] != '') {
                return WebUtils::makeErrorInfo_oldVersion($res, $_G['forum_optiondata']['message'], $_G['forum_optiondata']['params']);
            }
        }

        $author = !$isanonymous ? $_G['username'] : '';

        $moderated = $digest || $displayorder > 0 ? 1 : 0;

        $thread['status'] = 0;

        $_GET['ordertype'] && $thread['status'] = setstatus(4, 1, $thread['status']);

        $_GET['hiddenreplies'] && $thread['status'] = setstatus(2, 1, $thread['status']);

        /*             if($_G['group']['allowpostrushreply'] && $_GET['rushreply']) {
         $_GET['rushreplyfrom'] = strtotime($_GET['rushreplyfrom']);
        $_GET['rushreplyto'] = strtotime($_GET['rushreplyto']);
        $_GET['rewardfloor'] = trim($_GET['rewardfloor']);
        $_GET['stopfloor'] = intval($_GET['stopfloor']);
        $_GET['creditlimit'] = $_GET['creditlimit'] == '' ? '-996' : intval($_GET['creditlimit']);
        if($_GET['rushreplyfrom'] > $_GET['rushreplyto'] && !empty($_GET['rushreplyto'])) {
        return WebUtils::makeErrorInfo_oldVersion($res, 'forum_passwd');
        showmessage('post_rushreply_timewrong');
        }
        if(($_GET['rushreplyfrom'] > $_G['timestamp']) || (!empty($_GET['rushreplyto']) && $_GET['rushreplyto'] < $_G['timestamp']) || ($_GET['stopfloor'] == 1) ) {
        $closed = true;
        }
        if(!empty($_GET['rewardfloor']) && !empty($_GET['stopfloor'])) {
        $floors = explode(',', $_GET['rewardfloor']);
        if(!empty($floors) && is_array($floors)) {
        foreach($floors AS $key => $floor) {
        if(strpos($floor, '*') === false) {
        if(intval($floor) == 0) {
        unset($floors[$key]);
        } elseif($floor > $_GET['stopfloor']) {
        unset($floors[$key]);
        }
        }
        }
        $_GET['rewardfloor'] = implode(',', $floors);
        }
        }
        $thread['status'] = setstatus(3, 1, $thread['status']);
        $thread['status'] = setstatus(1, 1, $thread['status']);
        } */

        $_GET['allownoticeauthor'] && $thread['status'] = setstatus(6, 1, $thread['status']);
        $isgroup = $_G['forum']['status'] == 3 ? 1 : 0;

        /*  if($_G['group']['allowreplycredit']) {
         $_GET['replycredit_extcredits'] = intval($_GET['replycredit_extcredits']);
        $_GET['replycredit_times'] = intval($_GET['replycredit_times']);
        $_GET['replycredit_membertimes'] = intval($_GET['replycredit_membertimes']);
        $_GET['replycredit_random'] = intval($_GET['replycredit_random']);

        $_GET['replycredit_random'] = $_GET['replycredit_random'] < 0 || $_GET['replycredit_random'] > 99 ? 0 : $_GET['replycredit_random'] ;
        $replycredit = $replycredit_real = 0;
        if($_GET['replycredit_extcredits'] > 0 && $_GET['replycredit_times'] > 0) {
        $replycredit_real = ceil(($_GET['replycredit_extcredits'] * $_GET['replycredit_times']) + ($_GET['replycredit_extcredits'] * $_GET['replycredit_times'] *  $_G['setting']['creditstax']));
        if($replycredit_real > getuserprofile('extcredits'.$_G['setting']['creditstransextra'][10])) {
        return WebUtils::makeErrorInfo_oldVersion($res, 'forum_passwd');
        showmessage('replycredit_morethan_self');
        } else {
        $replycredit = ceil($_GET['replycredit_extcredits'] * $_GET['replycredit_times']);
        }
        }
        } */


        $newthread = array(
                'fid' => $_G['fid'],
                'posttableid' => 0,
                'readperm' => $readperm,
                'price' => $price,
                'typeid' => $typeid,
                'sortid' => $sortid,
                'author' => $author,
                'authorid' => $_G['uid'],
                'subject' => $subject,
                'dateline' => $publishdate,
                'lastpost' => $publishdate,
                'lastposter' => $author,
                'displayorder' => $displayorder,
                'digest' => $digest,
                'special' => $special,
                'attachment' => 0,
                'moderated' => $moderated,
                'status' => $thread['status']|$topicStatus,
                'isgroup' => $isgroup,
                'replycredit' => $replycredit,
                'closed' => $closed ? 1 : 0
        );
        $tid = C::t('forum_thread')->insert($newthread, true);
        useractionlog($_G['uid'], 'tid');

        if(!getuserprofile('threads') && $_G['setting']['newbie']) {
            C::t('forum_thread')->update($tid, array('icon' => $_G['setting']['newbie']));
        }
        if ($publishdate != $_G['timestamp']) {
            loadcache('cronpublish');
            $cron_publish_ids = dunserialize($_G['cache']['cronpublish']);
            $cron_publish_ids[$tid] = $tid;
            $cron_publish_ids = serialize($cron_publish_ids);
            savecache('cronpublish', $cron_publish_ids);
        }


        if(!$isanonymous) {
            C::t('common_member_field_home')->update($_G['uid'], array('recentnote'=>$subject));
        }

        if($special == 3 && $_G['group']['allowpostreward']) {
            updatemembercount($_G['uid'], array($_G['setting']['creditstransextra'][2] => -$realprice), 1, 'RTC', $tid);
        }

        if($moderated) {
            updatemodlog($tid, ($displayorder > 0 ? 'STK' : 'DIG'));
            updatemodworks(($displayorder > 0 ? 'STK' : 'DIG'), 1);
        }

        /* if($special == 1) {

        foreach($pollarray['options'] as $polloptvalue) {
        $polloptvalue = dhtmlspecialchars(trim($polloptvalue));
        C::t('forum_polloption')->insert(array('tid' => $tid, 'polloption' => $polloptvalue));
        }
        $polloptionpreview = '';
        $query = C::t('forum_polloption')->fetch_all_by_tid($tid, 1, 2);
        foreach($query as $option) {
        $polloptvalue = preg_replace("/\[url=(https?){1}:\/\/([^\[\"']+?)\](.+?)\[\/url\]/i", "<a href=\"\\1://\\2\" target=\"_blank\">\\3</a>", $option['polloption']);
        $polloptionpreview .= $polloptvalue."\t";
        }

        $polloptionpreview = daddslashes($polloptionpreview);

        $data = array('tid' => $tid, 'multiple' => $pollarray['multiple'], 'visible' => $pollarray['visible'], 'maxchoices' => $pollarray['maxchoices'], 'expiration' => $pollarray['expiration'], 'overt' => $pollarray['overt'], 'pollpreview' => $polloptionpreview);
        C::t('forum_poll')->insert($data);
        } */

        if($_G['forum']['threadsorts']['types'][$sortid] && !empty($_G['forum_optiondata']) && is_array($_G['forum_optiondata'])) {
            $filedname = $valuelist = $separator = '';
            foreach($_G['forum_optiondata'] as $optionid => $value) {
                if($value) {
                    $filedname .= $separator.$_G['forum_optionlist'][$optionid]['identifier'];
                    $valuelist .= $separator."'".daddslashes($value)."'";
                    $separator = ' ,';
                }

                if($_G['forum_optionlist'][$optionid]['type'] == 'image') {
                    $identifier = $_G['forum_optionlist'][$optionid]['identifier'];
                    $sortaids[] = intval($_GET['typeoption'][$identifier]['aid']);
                }

                C::t('forum_typeoptionvar')->insert(array(
                'sortid' => $sortid,
                'tid' => $tid,
                'fid' => $_G['fid'],
                'optionid' => $optionid,
                'value' => censor($value),
                'expiration' => ($typeexpiration ? $publishdate + $typeexpiration : 0),
                ));
            }

            if($filedname && $valuelist) {
                C::t('forum_optionvalue')->insert($sortid, "($filedname, tid, fid) VALUES ($valuelist, '$tid', '$_G[fid]')");
            }
        }
        if($_G['group']['allowat']) {
            $atlist = $atlist_tmp = array();
            preg_match_all("/@([^\r\n]*?)\s/i", $message.' ', $atlist_tmp);
            $atlist_tmp = array_slice(array_unique($atlist_tmp[1]), 0, $_G['group']['allowat']);
            if(!empty($atlist_tmp)) {
                if(empty($_G['setting']['at_anyone'])) {
                    foreach(C::t('home_follow')->fetch_all_by_uid_fusername($_G['uid'], $atlist_tmp) as $row) {
                    $atlist[$row['followuid']] = $row['fusername'];
                    }
                    if(count($atlist) < $_G['group']['allowat']) {
                        $query = C::t('home_friend')->fetch_all_by_uid_username($_G['uid'], $atlist_tmp);
                        foreach($query as $row) {
                        $atlist[$row['fuid']] = $row['fusername'];
                        }
                    }
                } else {
                    foreach(C::t('common_member')->fetch_all_by_username($atlist_tmp) as $row) {
                        $atlist[$row['uid']] = $row['username'];
                    }
                 }
            }
            if($atlist) {
                foreach($atlist as $atuid => $atusername) {
                    $atsearch[] = "/@".str_replace('/', '\/', preg_quote($atusername))." /i";
                    $atreplace[] = "[url=home.php?mod=space&uid=$atuid]@{$atusername}[/url] ";
                }
                $message = preg_replace($atsearch, $atreplace, $message.' ', 1);
            }
        }
        $bbcodeoff = checkbbcodes($message, !empty($_GET['bbcodeoff']));
        $smileyoff = checksmilies($message, !empty($_GET['smileyoff']));
        $parseurloff = !empty($_GET['parseurloff']);
        $htmlon = $_G['group']['allowhtml'] && !empty($_GET['htmlon']) ? 1 : 0;
        $usesig = !empty($_GET['usesig']) && $_G['group']['maxsigsize'] ? 1 : 0;
        $class_tag = new tag();
        $tagstr = $class_tag->add_tag($_GET['tags'], $tid, 'tid');

        /* if($_G['group']['allowreplycredit']) {
         if($replycredit > 0 && $replycredit_real > 0) {
        updatemembercount($_G['uid'], array('extcredits'.$_G['setting']['creditstransextra'][10] => -$replycredit_real), 1, 'RCT', $tid);
        $insertdata = array(
                'tid' => $tid,
                'extcredits' => $_GET['replycredit_extcredits'],
                'extcreditstype' => $_G['setting']['creditstransextra'][10],
                'times' => $_GET['replycredit_times'],
                'membertimes' => $_GET['replycredit_membertimes'],
                'random' => $_GET['replycredit_random']
        );
        C::t('forum_replycredit')->insert($insertdata);
        }
        } */

        if($_G['group']['allowpostrushreply'] && $_GET['rushreply']) {
            $rushdata = array('tid' => $tid, 'stopfloor' => $_GET['stopfloor'], 'starttimefrom' => $_GET['rushreplyfrom'], 'starttimeto' => $_GET['rushreplyto'], 'rewardfloor' => $_GET['rewardfloor'], 'creditlimit' => $_GET['creditlimit']);
            C::t('forum_threadrush')->insert($rushdata);
        }

        $pinvisible = $modnewthreads ? -2 : (empty($_GET['save']) ? 0 : -3);
        $message = preg_replace('/\[attachimg\](\d+)\[\/attachimg\]/is', '[attach]\1[/attach]', $message);
        $pid = insertpost(array(
                'fid' => $_G['fid'],
                'tid' => $tid,
                'first' => '1',
                'author' => $_G['username'],
                'authorid' => $_G['uid'],
                'subject' => $subject,
                'dateline' => $publishdate,
                'message' => $message,
                'useip' => $_G['clientip'],
                'invisible' => $pinvisible,
                'anonymous' => $isanonymous,
                'usesig' => $usesig,
                'htmlon' => $htmlon,
                'bbcodeoff' => $bbcodeoff,
                'smileyoff' => $smileyoff,
                'parseurloff' => $parseurloff,
                'attachment' => '0',
                'tags' => $tagstr,
                'replycredit' => 0,
                'status' => (defined('IN_MOBILE') ? 8 : 0) | $postStatus   
        ));
        if($_G['group']['allowat'] && $atlist) {
            foreach($atlist as $atuid => $atusername) {
                notification_add($atuid, 'at', 'at_message', array('from_id' => $tid, 'from_idtype' => 'at', 'buyerid' => $_G['uid'], 'buyer' => $_G['username'], 'tid' => $tid, 'subject' => $subject, 'pid' => $pid, 'message' => messagecutstr($message, 150)));
            }
            set_atlist_cookie(array_keys($atlist));
        }
        $threadimageaid = 0;
        $threadimage = array();
        if($special == 4 && $_GET['activityaid']) {
            $threadimageaid = $_GET['activityaid'];
            convertunusedattach($_GET['activityaid'], $tid, $pid);
        }

        if($_G['forum']['threadsorts']['types'][$sortid] && !empty($_G['forum_optiondata']) && is_array($_G['forum_optiondata']) && $sortaids) {
            foreach($sortaids as $sortaid) {
                convertunusedattach($sortaid, $tid, $pid);
            }
        }

        if(($_G['group']['allowpostattach'] || $_G['group']['allowpostimage']) && ($_GET['attachnew'] || $sortid || !empty($_GET['activityaid']))) {
            updateattach($displayorder == -4 || $modnewthreads, $tid, $pid, $_GET['attachnew']);
            if(!$threadimageaid) {
                $threadimage = C::t('forum_attachment_n')->fetch_max_image('tid:'.$tid, 'tid', $tid);
                $threadimageaid = $threadimage['aid'];
            }
        }

        $values = array('fid' => $_G['fid'], 'tid' => $tid, 'pid' => $pid, 'coverimg' => '', 'sechash' => !empty($_GET['sechash']) ? $_GET['sechash'] : '');
        $param = array();
        Mobcent::import(MOBCENT_APP_ROOT . '/components/discuz/source/function/function_post.php');
        if($_G['forum']['picstyle']) {
            if(!mobcent_setthreadcover($pid, 0, $threadimageaid)) {
                preg_match_all("/(\[img\]|\[img=\d{1,4}[x|\,]\d{1,4}\])\s*([^\[\<\r\n]+?)\s*\[\/img\]/is", $message, $imglist, PREG_SET_ORDER);
                $values['coverimg'] = "<p id=\"showsetcover\">".lang('message', 'post_newthread_set_cover')."<span id=\"setcoverwait\"></span></p><script>if($('forward_a')){\$('forward_a').style.display='none';setTimeout(\"$('forward_a').style.display=''\", 5000);};ajaxget('forum.php?mod=ajax&action=setthreadcover&tid=$tid&pid=$pid&fid=$_G[fid]&imgurl={$imglist[0][2]}&newthread=1', 'showsetcover', 'setcoverwait')</script>";
                $param['clean_msgforward'] = 1;
                $param['timeout'] = $param['refreshtime'] = 15;
            }
        }

        if($threadimageaid) {
            if(!$threadimage) {
                $threadimage = C::t('forum_attachment_n')->fetch('tid:'.$tid, $threadimageaid);
            }
            $threadimage = daddslashes($threadimage);
            C::t('forum_threadimage')->insert(array(
            'tid' => $tid,
            'attachment' => $threadimage['attachment'],
            'remote' => $threadimage['remote'],
            ));
        }

        $statarr = array(0 => 'thread', 1 => 'poll', 2 => 'trade', 3 => 'reward', 4 => 'activity', 5 => 'debate', 127 => 'thread');
        include_once libfile('function/stat');
        updatestat($isgroup ? 'groupthread' : $statarr[$special]);

        if($modnewthreads) {
            updatemoderate('tid', $tid);
            C::t('forum_forum')->update_forum_counter($_G['fid'], 0, 0, 1);
            manage_addnotify('verifythread');
            return WebUtils::makeErrorInfo_oldVersion($res, 'post_newthread_mod_succeed', array('noError' => 1));
            //showmessage('post_newthread_mod_succeed', $returnurl, $values, $param);
        } else {
            if($displayorder >= 0 && helper_access::check_module('follow') && !empty($_GET['adddynamic']) && !$isanonymous) {
                require_once libfile('function/discuzcode');
                require_once libfile('function/followcode');
                $feedcontent = array(
                        'tid' => $tid,
                        'content' => followcode($message, $tid, $pid, 1000),
                );
                C::t('forum_threadpreview')->insert($feedcontent);
                C::t('forum_thread')->update_status_by_tid($tid, '512');
                $followfeed = array(
                        'uid' => $_G['uid'],
                        'username' => $_G['username'],
                        'tid' => $tid,
                        'note' => '',
                        'dateline' => TIMESTAMP
                );
                $values['feedid'] = C::t('home_follow_feed')->insert($followfeed, true);
                C::t('common_member_count')->increase($_G['uid'], array('feeds'=>1));

            }

            $feed = array(
                    'icon' => '',
                    'title_template' => '',
                    'title_data' => array(),
                    'body_template' => '',
                    'body_data' => array(),
                    'title_data'=>array(),
                    'images'=>array()
            );
            if($displayorder != -4) {
                if($digest) {
                    updatepostcredits('+',  $_G['uid'], 'digest', $_G['fid']);
                }
                //updatepostcredits('+',  $_G['uid'], 'post', $_G['fid']);
                Mobcent::import(MOBCENT_APP_ROOT . '/components/discuz/source/function/function_post.php');
                //Yii::import('application.components.discuz.source.function.function_post', true);
                mobcent_updatepostcredits('+',  $_G['uid'], 'post', $_G['fid']);
                if($isgroup) {
                    C::t('forum_groupuser')->update_counter_for_user($_G['uid'], $_G['fid'], 1);
                }
                $subject = str_replace("\t", ' ', $subject);
                $lastpost = "$tid\t".$subject."\t$_G[timestamp]\t$author";
                C::t('forum_forum')->update($_G['fid'], array('lastpost' => $lastpost));
                C::t('forum_forum')->update_forum_counter($_G['fid'], 1, 1, 1);
                if($_G['forum']['type'] == 'sub') {
                    C::t('forum_forum')->update($_G['forum']['fup'], array('lastpost' => $lastpost));
                }
            }
            if($_G['forum']['status'] == 3) {
                C::t('forum_forumfield')->update($_G['fid'], array('lastupdate' => TIMESTAMP));
                require_once libfile('function/grouplog');
                updategroupcreditlog($_G['fid'], $_G['uid']);
            }
            /*如果显示地理位置，入库到表里*/
            if($jsonInfo['isShowPostion'] && $jsonInfo['location']){
                $data = DB::query('INSERT INTO  %t VALUES(null,%f,%f,%d,%d,%s)',array('home_surrounding_user',$jsonInfo['longitude'],$jsonInfo['latitude'],$tid,3,$jsonInfo['location']));
            }
           /*  //客户端发表主题积分入库
                $temp = DB::fetch_first('SELECT extcredits3  FROM '.DB::table('common_credit_rule').' WHERE rid =%d ',array(1));
                $extcredits3 = DB::fetch_first('SELECT extcredits3 FROM '.DB::table('common_member_count').' WHERE uid = %d',array($_G['uid']));
                $temp = $extcredits3['extcredits3'] + $temp['extcredits3'];
                DB::query('UPDATE '.DB::table('common_member_count').' set extcredits3 = %d WHERE uid = %d',array($temp,$_G['uid']));
             */
             //showmessage('post_newthread_succeed', $returnurl, $values, $param);
            return array('rs'=>1,'errcode'=>WebUtils::t('发贴成功'));
        }

    }

    // 统计发贴 回贴数
    private function _doPostStatistics($act) {
        $forumKey = isset($_GET['forumKey']) ? $_GET['forumKey'] : '';
        $platType = isset($_GET['platType']) ? $_GET['platType'] : APP_TYPE_ANDROID;
        $isReplyPost = $act == 'new' ? 0 : 1;
        $url = "http://sdk.mobcent.com/imsdk/post/postLog.do?forumKey=".$forumKey."&platType=".$platType."&isReplyPost=".$isReplyPost;

        WebUtils::httpRequest($url, 5);
    }

}