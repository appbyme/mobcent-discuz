<?php
require_once './abstractUserVote.php';
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
C::app ()->init ();
require_once libfile ( 'function/discuzcode' );
require_once libfile ( 'class/credit' );
require_once libfile ( 'function/post' );
require_once libfile ( 'function/forum' );
require_once '../model/table/x25/table_common_member.php';
require_once '../model/table/x25/topic.php';

class userVoteImpl_x25 extends abstractUserVote { 
	public function getUserVoteObj() { 
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
		$special = 1; /*tou piao tie, $special=1*/
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
		
		/*renxing: check user to vote*/
		if(empty($_GET['topicId'])){
			$data_post['rs'] = 0;
			$data_post['errcode'] = '04000024';
			return $data_post;
			exit();
		}
		
		$choose_query = DB::query("SELECT * FROM ".DB::table('forum_poll')." where tid=".$_GET['topicId']);
		while($choose_list = DB::fetch($choose_query)) {
			$choose_arr[] = $choose_list;
		}
		$item_arr=explode(',', $_GET['itemId']);
		if($choose_arr[0][maxchoices]==0){
			$choose_arr[0][maxchoices]=1;
		}
		if(count($item_arr)>$choose_arr[0][maxchoices]){
			$data_post['rs'] = 0;
			$data_post['maxchoices'] = $choose_arr[0][maxchoices];
			$data_post['errcode'] = '04000025';
			return $data_post;
			exit();
		}
		
		$isvote_query = DB::query("SELECT count(*) as isvote FROM ".DB::table('forum_pollvoter')." where tid=".$_GET['topicId']." and uid=".$_G['uid']);
		while($isvote_list = DB::fetch($isvote_query)) {
			$isvote_arr[] = $isvote_list;
		}
		if($isvote_arr[0][isvote] != 0){
			$data_post['rs'] = 0;
			$data_post['errcode'] = '04000026';
			return $data_post;
			exit();
		}
		/*end check*/
		
 		/*renxing: user to vote  */
		$items=str_replace(',', '	',$_GET['itemId']);
		DB::query("UPDATE ".DB::table('forum_poll')." SET voters=voters+1 where tid=".$_GET['topicId']);
		DB::query("UPDATE ".DB::table('forum_polloption')." SET votes=votes+1,voterids=CONCAT(voterids,'".$_G['uid']."	') where polloptionid in (".$_GET['itemId'].")"); 
		DB::query("insert into ".DB::table('forum_pollvoter')." (tid,uid,username,options,dateline) values ('".$_GET['topicId']."','".$_G['uid']."','".$_G['username']."','".$items."','".time()."')");

		$polloption_query = DB::query("SELECT polloption as name,polloptionid as pollItemId,votes as totalNum FROM ".DB::table('forum_polloption')." where tid=".$_GET['topicId']);
		while ($polloption_rst = DB::fetch($polloption_query)) {
			$polloption_arr[] = $polloption_rst;
		}
		for($di=0;$di<count($polloption_arr);$di++){
			$polloption_arr[$di][pollItemId]=(Int)$polloption_arr[$di][pollItemId];
			$polloption_arr[$di][totalNum]=(Int)$polloption_arr[$di][totalNum];
		}
		
		$data_post ["rs"] = 1;
		$data_post ["vote_rs"] = $polloption_arr;
		return $data_post;
	}
}

?>