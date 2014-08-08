<?php
require_once './abstractPublishPoll.php';
define ( 'IN_MOBCENT', 1 );
require_once '../../source/class/class_core.php';
require_once '../../source/class/table/table_forum_thread.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../../uc_client/client.php';
require_once '../tool/tool.php';
require_once '../Config/public.php';
require_once '../tool/constants.php';
require_once '../model/table_forum_thread.php';
require_once '../helper/helper_notification.php';
require_once '../model/table_surround_user.php';
define('ALLOWGUEST', 1);

// xss debug fixed
$tempMethod = $_SERVER['REQUEST_METHOD'];
$_SERVER['REQUEST_METHOD'] = 'POST';
define('DISABLEXSSCHECK', 1);

C::app ()->init ();

$_SERVER['REQUEST_METHOD'] = $tempMethod;

require_once libfile ( 'function/discuzcode' );
require_once libfile ( 'class/credit' );
require_once libfile ( 'function/post' );
require_once libfile ( 'function/forum' );
require_once '../model/table/x25/table_common_member.php';
require_once '../model/table/x25/topic.php';

class PublishPollImpl_x25 extends abstractPublishPoll {
	public function getPublishPollObj() { 
		function isdate($str,$format="Y-m-d"){
			$strArr = explode("-",$str);
			if(empty($strArr)){
				return false;
			}
			foreach($strArr as $val){
				if(strlen($val)<2){
					$val="0".$val;
				}
				$newArr[]=$val;
			}
			$str =implode("-",$newArr);
			$unixTime=strtotime($str);
			$checkDate= date($format,$unixTime);
			if($checkDate==$str){
				return true;
			}else{
				return false;
			}
		}
		
		$rPostion = $_GET['r'] ? $_GET['r']:0;  
		$longitude =$_GET['longitude'];	
		$latitude =	$_GET['latitude'];	
		$location	=	echo_urldecode($_GET['location']);
		$aid = $_REQUEST ['aid']; 
		$aid_Img=explode(',',$aid);
		$_G ['fid'] = $_GET ['boardId'];
		require_once '../public/mobcentDatabase.php';
		$info = new mobcentGetInfo ();
		$modnewposts = $info ->getBoard($_G ['fid']);
		$readperm = 0;
		$price = 0;
		$typeid = 0;
		$sortid = 0;
		$displayorder = $modnewposts['modnewposts'] > 0?-2:0;
		$digest = 0;
		$special = 1; /*tou piao tie, $special=1 */
		$attachment = 0;
		$moderated = 0;
		$isgroup = 0;
		$replycredit = 0;
		$closed = 0;
		$publishdate = time ();
		
		$accessSecret = $_GET['accessSecret'];
		$accessToken = $_GET['accessToken'];
		$qquser = Common::get_unicode_charset('\u6e38\u5ba2');
		$group = $info->rank_check_allow($accessSecret,$accessToken,$qquser);
		if(!$group['allowvisit'])
		{
			$data_post['rs'] = 0;
			$data_post['errcode'] = '01110001';
			return $data_post;
			exit();
		}
		$arrAccess = $info->sel_accessTopkent($accessSecret,$accessToken);
		$ruid = $_G ['uid'] =$arrAccess['user_id'];
		$space = $info->getUserInfo ( intval ( $ruid ) );
		if(empty($_G ['uid']))
		{
			return $info -> userAccessError();
			exit();
		}
		$author = $space ['username'];
		$_G ['username'] = $lastposter = $author;
		$_G = array_merge ( $_G, $space );
		
		
		$a = array("qq"=>"a","ff"=>"b");
		$b = array("ss"=>"c","dd"=>"d");
		$c = array();
		foreach( $a as $key => $value ) {
			$c[$key] = $value;
		}
		foreach( $b as $key => $value ) {
			$c[$key] = $value;
		}
		
 		/*renxing vote data and check */
		$pollItem=echo_array(urldecode($_GET['pollItem']));
		$pollarray=array();
		foreach($pollItem as $poll){
			$pitems[]=$poll[itemName];
		}
		if(count($pitems)>20)
		{
			$data_post['rs'] = 0;
			$data_post['errcode'] = '04000020';
			return $data_post;
			exit();
		}
		if(count($pitems)<2)
		{
			$data_post['rs'] = 0;
			$data_post['errcode'] = '04000021';
			return $data_post;
			exit();
		}
		if(!(preg_match("/^\d*$/", trim($_GET['type'])))) {
			$data_post['rs'] = 0;
			$data_post['errcode'] = '04000022';
			return $data_post;
			exit();
		}
		if(!(preg_match("/^\d*$/", trim($_GET['deadline'])))) {
			$data_post['rs'] = 0;
			$data_post['errcode'] = '04000023';
			return $data_post;
			exit();
		}

		$pollarray[maxchoices]=empty($_GET['type']) ? 0 : $_GET['type'];
		$pollarray[multiple]=(empty($_GET['type'])||intval($_GET['type'])==1)?0:1;
		$pollarray[options]=$pitems;
		$pollarray[visible] = empty($_GET['isVisible']);
		$pollarray[overt] = !empty($_GET['overt']);
		if(empty($_GET['deadline'])) {
			$pollarray['expiration'] = 0;
		} else {
			$pollarray['expiration'] = TIMESTAMP + 86400 * $_GET['deadline'];
		}
		/*end check */
				
		
		$subject = echo_urldecode ( $_GET ['title'] ) ;	  	 
		
		$message = '';
		$i=0;
		$array_message=echo_array(urldecode($_GET['content']));
		foreach ( $array_message as $k => $v ) {			 
			switch ($v ["type"]) {
				case 0 :
					$message .= $v ["infor"];
					break;
				case 1 :
					if(empty($aid_Img))
					{
						$message .= '[attachimg]' . $aid . '[/attachimg]';
					}
					else
					{
						$message .= '[attachimg]' . $aid_Img[$i] . '[/attachimg]';
						$i=$i+1;
					}
					$attachment = 2;
					break;
				case 3:
					    $message .= "[audio]".$v ["infor"]."[/audio]";
					    break;
			}
		}
		/* 判断是否发匿名与仅该作者可见贴*/
		$isOnlyAuthor = $_GET['isOnlyAuthor']?$_GET['isOnlyAuthor']:0;
		$thread['status'] = 32;
		$isOnlyAuthor == 1 && $thread['status'] = setstatus(2, 1, $thread['status']);
		$isAnonymous = $_GET['isAnonymous']?$_GET['isAnonymous']:0;
		$author = !$isAnonymous ? $_G['username'] : '';
		$newthread = array (
				'fid' => $_G ['fid'],
				'posttableid' => 0,
				'readperm' => $readperm,
				'price' => $price,
				'typeid' => $typeid,
				'sortid' => $sortid,
				'author' => $author,
				'authorid' => $_G ['uid'],
				'subject' => $subject,
				'dateline' => $publishdate,
				'lastpost' => $publishdate,
				'lastposter' => $author,
				'displayorder' => $displayorder,
				'digest' => $digest,
				'special' => $special,
				'attachment' => $attachment,
				'moderated' => $moderated,
				'status' => $thread['status'],
				'isgroup' => $isgroup,
				'replycredit' => $replycredit,
				'closed' => $closed
		);
		$tid = C::t ( 'forum_thread' )->insert ( $newthread, true );
		if($modnewposts['modnewposts'] > 0)
		{
			table_forum_thread_moderate::insert($tid,0,time ());
		}
		
		if (! $tid) {
			echo '{"rs":0}';
			exit ();
		}
		useractionlog ( $_G ['uid'], 'tid' );
		
		C::t ( 'common_member_field_home' )->update ( $_G ['uid'], array (
		'recentnote' => $subject
		) );

		$pinvisible = $modnewposts['modnewposts'] > 0?-2:0;
		$isanonymous = 0;
		$usesig = 1;
		$htmlon = 0;
		$bbcodeoff = - 1;
		$smileyoff = - 1;
		$parseurloff = false;
		$tagstr = null;
		
		$message = htmlspecialchars_decode ( $message );
		$_G ['group'] ['allowat'] = substr_count ( $message, '@' );
		if ($_G ['group'] ['allowat']) {
			$bbcodeoff = 0; 
			$atlist = $atlist_tmp = array ();
			$res = preg_match_all ( "#@([^\r\n]*?)\s#i", $message . ' ', $atlist_tmp );
			$atlist_tmp = array_slice ( array_unique ( $atlist_tmp [1] ), 0, $_G ['group'] ['allowat'] );
			if (! empty ( $atlist_tmp )) {
				if (empty ( $_G ['setting'] ['at_anyone'] )) {
					foreach ( C::t ( 'home_follow' )->fetch_all_by_uid_fusername ( $_G ['uid'], $atlist_tmp ) as $row ) {
						$atlist [$row ['followuid']] = $row ['fusername'];
					}
					if (count ( $atlist ) < $_G ['group'] ['allowat']) {
						$query = C::t ( 'home_friend' )->fetch_all_by_uid_username ( $_G ['uid'], $atlist_tmp );
						foreach ( $query as $row ) {
							$atlist [$row ['fuid']] = $row ['fusername'];
						}
					}
				} else {
					foreach ( C::t ( 'common_member' )->fetch_all_by_username ( $atlist_tmp ) as $row ) {
						$atlist [$row ['uid']] = $row ['username'];
					}
				}
			}
			if ($atlist) {
		
				foreach ( $atlist as $atuid => $atusername ) {
					$atsearch [] = "/@$atusername /i";
					$atreplace [] = "[url=home.php?mod=space&uid=$atuid]@{$atusername}[/url] ";
				}
				$message = preg_replace ( $atsearch, $atreplace, $message . ' ', 1 );
			}
		}
		
		
		/*renxing vote insert*/
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
		/*end  renxing vote insert*/
		
		
		$class_tag = new tag ();
		$tagstr = $class_tag->add_tag ( $_GET ['tags'], $tid, 'tid' );
		$message = preg_replace ( '/\[attachimg\](\d+)\[\/attachimg\]/is', '[attach]\1[/attach]', $message );
		$message = $_GET['platType'] ==1 ? $message."\r\n[url=/mobcent/download/down.php]".Common::get_unicode_charset('\u6765\u81ea\u5b89\u5353\u5ba2\u6237\u7aef').'[/url]':$message."\r\n[url=/mobcent/download/down.php]".Common::get_unicode_charset('\u6765\u81ea\u0069\u0070\u0068\u006f\u006e\u0065\u5ba2\u6237\u7aef')."[/url]";
		$pid = insertpost ( array (
				'fid' => $_G ['fid'],
				'tid' => $tid,
				'first' => '1',
				'author' => $author,
				'authorid' => $_G ['uid'],
				'subject' => $subject,
				'dateline' => time (),
				'message' => $message,
				'useip' => get_client_ip (),
				'invisible' => $pinvisible,
				'anonymous' => $isAnonymous,
				'usesig' => $usesig,
				'htmlon' => $htmlon,
				'bbcodeoff' => 0, 
				'smileyoff' => $smileyoff,
				'parseurloff' => $parseurloff,
				'attachment' => $attachment,
				'tags' => $tagstr,
				'replycredit' => 0,
				'status' => 0
		) );
		
		 
		if ($_G ['group'] ['allowat'] && $atlist) {
			foreach ( $atlist as $atuid => $atusername ) {
				mobcent_helper_notification::notification_add ( $_G['username'], $atuid, 'at', 'at_message',$_G ['uid'], array (
				'from_id' => $tid,
				'from_idtype' => 'at',
				'buyerid' => $_G ['uid'],
				'buyer' => $_G ['username'],
				'tid' => $tid,
				'subject' => $subject,
				'pid' => $pid,
				'message' => messagecutstr ( $message, 150 )
				) );
			}
			set_atlist_cookie ( array_keys ( $atlist ) );
		}
		
		
		if(empty($aid_Img))
		{
			$threadimageaid = $aid;
			 
			if ($aid) {
				$tableid = getattachtableid ( $tid );
				$query = get_forum_attachment_unused($aid);
				while ( $attach = DB::fetch ( $query ) ) {
					$aids = $attach ['aid'];
					$data = $attach;
				}
				$uid = $_G['uid'];
				update_forum_attachment($tid, $tableid,$uid, $pid, $aids);
				$data ['uid'] = 1;
				$data ['tid'] = $tid;
				$data ['pid'] = $pid;
				C::t ( 'forum_attachment_n' )->insert ( $tableid, $data );
			}
		
			$values = array (
					'fid' => $_G ['fid'],
					'tid' => $tid,
					'pid' => $pid,
					'coverimg' => ''
			);
			$param = array ();
			if ($_G ['forum'] ['picstyle']) {
				if (! setthreadcover ( $pid, 0, $threadimageaid )) {
					preg_match_all ( "/(\[img\]|\[img=\d{1,4}[x|\,]\d{1,4}\])\s*([^\[\<\r\n]+?)\s*\[\/img\]/is", $message, $imglist, PREG_SET_ORDER );
					$values ['coverimg'] = "<p id=\"showsetcover\">" . lang ( 'message', 'post_newthread_set_cover' ) . "<span id=\"setcoverwait\"></span></p><script>if($('forward_a')){\$('forward_a').style.display='none';setTimeout(\"$('forward_a').style.display=''\", 5000);};ajaxget('forum.php?mod=ajax&action=setthreadcover&tid=$tid&pid=$pid&fid=$_G[fid]&imgurl={$imglist[0][2]}&newthread=1', 'showsetcover', 'setcoverwait')</script>";
					$param ['clean_msgforward'] = 1;
					$param ['timeout'] = $param ['refreshtime'] = 15;
				}
			}
		
			if ($threadimageaid && empty($imagearr)) {
				if (! $threadimage) {
					$threadimage = C::t ( 'forum_attachment_n' )->fetch ( 'tid:' . $tid, $threadimageaid );
				}
				$threadimage = daddslashes ( $threadimage );
				C::t ( 'forum_threadimage' )->insert ( array (
				'tid' => $tid,
				'attachment' => $threadimage ['attachment'],
				'remote' => $threadimage ['remote']
				) );
			}
			 
		}
		else
		{
			$isInsertForumImage = false;
			foreach($aid_Img as $key=>$val)
			{
				$threadimageaid = $val;
				 	
				if ($val) {
					$tableid = getattachtableid ( $tid );
					$query = DB::query ( "SELECT * FROM %t WHERE aid=%d", array (
							'forum_attachment_unused',
							$val
					) );
					while ( $attach = DB::fetch ( $query ) ) {
						$aids = $attach ['aid'];
						$data = $attach;
					}
					DB::query ( "UPDATE %t SET tid=%d,tableid=%d,uid=%d,pid=%d WHERE aid IN (%n)", array (
					'forum_attachment',
					$tid,
					getattachtableid ( $tid ),
					$_G ['uid'],
					$pid,
					$aids
					) );
					$data ['uid'] = 1;
					$data ['tid'] = $tid;
					$data ['pid'] = $pid;
					C::t ( 'forum_attachment_n' )->insert ( $tableid, $data );
				}
		
				$values = array (
						'fid' => $_G ['fid'],
						'tid' => $tid,
						'pid' => $pid,
						'coverimg' => ''
				);
				$param = array ();
				if ($_G ['forum'] ['picstyle']) {
					if (! setthreadcover ( $pid, 0, $threadimageaid )) {
						preg_match_all ( "/(\[img\]|\[img=\d{1,4}[x|\,]\d{1,4}\])\s*([^\[\<\r\n]+?)\s*\[\/img\]/is", $message, $imglist, PREG_SET_ORDER );
						$values ['coverimg'] = "<p id=\"showsetcover\">" . lang ( 'message', 'post_newthread_set_cover' ) . "<span id=\"setcoverwait\"></span></p><script>if($('forward_a')){\$('forward_a').style.display='none';setTimeout(\"$('forward_a').style.display=''\", 5000);};ajaxget('forum.php?mod=ajax&action=setthreadcover&tid=$tid&pid=$pid&fid=$_G[fid]&imgurl={$imglist[0][2]}&newthread=1', 'showsetcover', 'setcoverwait')</script>";
						$param ['clean_msgforward'] = 1;
						$param ['timeout'] = $param ['refreshtime'] = 15;
					}
				}
		
				if (!$isInsertForumImage && $threadimageaid  && empty($imagearr)) {
					if (! $threadimage) {
						$threadimage = C::t ( 'forum_attachment_n' )->fetch ( 'tid:' . $tid, $threadimageaid );
					}
					$threadimage = daddslashes ( $threadimage );
					C::t ( 'forum_threadimage' )->insert ( array (
					'tid' => $tid,
					'attachment' => $threadimage ['attachment'],
					'remote' => $threadimage ['remote']
					) );
					$isInsertForumImage = true;
				}
				 
			}
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
		 
		if(1){
			$message = !$price && !$readperm ? $message : '';
			if($special == 0) {
				$feed['icon'] = 'thread';
				$feed['title_template'] = 'feed_thread_title';
				$feed['body_template'] = 'feed_thread_message';
				$feed['body_data'] = array(
						'subject' => "<a href=\"forum.php?mod=viewthread&tid=$tid\">$subject</a>",
						'message' => messagecutstr($message, 150)
				);
				if(!empty($_G['forum_attachexist'])) {
					$imgattach = C::t('forum_attachment_n')->fetch_max_image('tid:'.$tid, 'pid', $pid);
					$firstaid = $imgattach['aid'];
					unset($imgattach);
					if($firstaid) {
						$feed['images'] = array(getforumimg($firstaid));
						$feed['image_links'] = array("forum.php?mod=viewthread&do=tradeinfo&tid=$tid&pid=$pid");
					}
				}
			} elseif($special > 0) {
				if($special == 1) {
					$pvs = explode("\t", messagecutstr($polloptionpreview, 150));
					$s = '';
					$i = 1;
					foreach($pvs as $pv) {
						$s .= $i.'. '.$pv.'<br />';
					}
					$s .= '&nbsp;&nbsp;&nbsp;...';
					$feed['icon'] = 'poll';
					$feed['title_template'] = 'feed_thread_poll_title';
					$feed['body_template'] = 'feed_thread_poll_message';
					$feed['body_data'] = array(
							'subject' => "<a href=\"forum.php?mod=viewthread&tid=$tid\">$subject</a>",
							'message' => $s
					);
				} elseif($special == 3) {
					$feed['icon'] = 'reward';
					$feed['title_template'] = 'feed_thread_reward_title';
					$feed['body_template'] = 'feed_thread_reward_message';
					$feed['body_data'] = array(
							'subject'=> "<a href=\"forum.php?mod=viewthread&tid=$tid\">$subject</a>",
							'rewardprice'=> $rewardprice,
							'extcredits' => $_G['setting']['extcredits'][$_G['setting']['creditstransextra'][2]]['title'],
					);
				} elseif($special == 4) {
					$feed['icon'] = 'activity';
					$feed['title_template'] = 'feed_thread_activity_title';
					$feed['body_template'] = 'feed_thread_activity_message';
					$feed['body_data'] = array(
							'subject' => "<a href=\"forum.php?mod=viewthread&tid=$tid\">$subject</a>",
							'starttimefrom' => $_GET['starttimefrom'][$activitytime],
							'activityplace'=> $activity['place'],
							'message' => messagecutstr($message, 150),
					);
					if($_GET['activityaid']) {
						$feed['images'] = array(getforumimg($_GET['activityaid']));
						$feed['image_links'] = array("forum.php?mod=viewthread&do=tradeinfo&tid=$tid&pid=$pid");
					}
				} elseif($special == 5) {
					$feed['icon'] = 'debate';
					$feed['title_template'] = 'feed_thread_debate_title';
					$feed['body_template'] = 'feed_thread_debate_message';
					$feed['body_data'] = array(
							'subject' => "<a href=\"forum.php?mod=viewthread&tid=$tid\">$subject</a>",
							'message' => messagecutstr($message, 150),
							'affirmpoint'=> messagecutstr($affirmpoint, 150),
							'negapoint'=> messagecutstr($negapoint, 150)
					);
				}
			}
		
			$feed['title_data']['hash_data'] = "tid{$tid}";
			$feed['id'] = $tid;
			$feed['idtype'] = 'tid';
			if($feed['icon']) {
				postfeed($feed);
			}
		}
		 
		if($digest) {
			updatepostcredits('+',  $_G['uid'], 'digest', $_G['fid']);
		}
		updatepostcredits('+',  $_G['uid'], 'post', $_G['fid']);
		if($isgroup) {
			C::t('forum_groupuser')->update_counter_for_user($_G['uid'], $_G['fid'], 1);
		}
		 
		if (! $pid) {
			$obj -> rs = SUCCESS;
			echo echo_json($obj);
			exit ();
		}
		 
		$subject = str_replace ( "\t", ' ', $subject );
		$lastpost = "$tid\t" . $subject . "\t$publishdate\t$author";
		
		C::t ( 'forum_forum' )->update ( $_G ['fid'], array (
		'lastpost' => $lastpost
		) );
		C::t ( 'forum_forum' )->update_forum_counter ( $_G ['fid'], 1, 1, 1 );
		DB::query ( "DELETE FROM %t WHERE uid=%d", array (
		'forum_attachment_unused',
		$_G ['uid']
		) );
		
	 
		if(isset($rPostion) && !empty($rPostion))
		{
			surround_user::insert_all_thread_location($longitude,$latitude,$location,$pid);
		}
		
		$data_post ["rs"] = 1;
		$data_post ["content"] = $modnewposts['modnewposts'] > 0?Common::get_unicode_charset('\u65b0\u4e3b\u9898\u9700\u8981\u5ba1\u6838\uff0c\u60a8\u7684\u5e16\u5b50\u901a\u8fc7\u5ba1\u6838\u540e\u624d\u80fd\u663e\u793a'):'';
		return $data_post;
	}
	
}

?>