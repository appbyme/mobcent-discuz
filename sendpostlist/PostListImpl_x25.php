<?php
define('IN_MOBCENT',1);
require_once './abstractPostList.php';
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_forum.php';
require_once '../model/table/x25/table_forum_typeoption.php';
require_once '../tool/tool.php';
define('ALLOWGUEST', 1);
C::app ()->init();
require_once '../public/mobcentDatabase.php';

class PostListImpl_x25 extends abstractPostList {
	public function getPostListObj() {
		global $_G;
		$info = new mobcentGetInfo();
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
		$uid = $_G ['uid'] =$arrAccess['user_id'];
		$qquser = Common::get_unicode_charset('\u6e38\u5ba2');
		if(empty($accessSecret) || empty($accessToken))
		{
			$group = $info-> sel_QQuser($qquser);
		
		}else if(empty($_G ['uid']))
		{
			return $info -> userAccessError();
			exit();
		}else {
			$group = $info-> sel_group_by_uid($uid);
			
		}
		if(!$_G['forum']['viewperm'] && !$group['readaccess'])
		{
			$data_post['rs'] = 0;
			$data_post['errcode'] = '01110001';
			return $data_post;
			exit();
		}
		$space = $info->getUserInfo(intval($uid));
		$_G=array_merge($_G,$space);
		$_G['fid'] = intval($_GET['boardId']);
		$forum = $info->getForumSub($_G['fid']);
		$_G['forum']=array_merge($_G['forum'],$forum);



		/*renxing fenlei*/
		$fenlei_id = intval($_GET['classificationTop_id']);
		$classified_list=C::t('forum_typeoption')->fetch_info_by_classid($fenlei_id,'asc');

		/*-----------check bi tian-----------*/
		$mytypevar=C::t('forum_typevar')->fetch_all_by_sortid($fenlei_id,'asc');
		foreach($mytypevar as $tpvs){
			$typevar[]=$tpvs;
		}		
		foreach($classified_list as $dd=>$ff){		
			$classified_test[]=array_merge($ff,$typevar[$dd]);
		}  
		foreach($typevar as $trs){
			unset($trs[sortid]);
			unset($trs[available]);
			unset($trs[search]);
			$rx_var[]=$trs;
		}
		for($sx=0;$sx<count($classified_test);$sx++){
			if(intval($classified_test[$sx][available])==0){
				unset($classified_test[$sx]);
			}
		}	
		
		$classified_arr=array();
		foreach($classified_test as $cs_test){
			unset($cs_test[sortid]);
			unset($cs_test[optionid]);
			unset($cs_test[available]);
			unset($cs_test[search]);
			unset($cs_test[displayorder]);
			unset($cs_test[subjectshow]);
			$classified_arr[]=$cs_test;
		}		
		/*-----------end check----------*/
		 
		
		$renxing=array();		
		for($i=0;$i<count($classified_arr);$i++){			
			$tps=$classified_arr[$i][classifiedType];
			$classified_arr[$i][classifiedRules]=unserialize($classified_arr[$i][classifiedRules]);
			
			if(isset($classified_arr[$i][classifiedRules][maxlength]) && is_numeric(($classified_arr[$i][classifiedRules][maxlength]))){
				$classified_arr[$i][classifiedRules][maxlength]=intval((int)$classified_arr[$i][classifiedRules][maxlength]/3);
			}
			if($tps=="calendar"){
				$classified_arr[$i][classifiedRules][defaultvalue]=date("Y-m-d",time());
				$classified_arr[$i][classifiedRules][isdate]=1;
			}
			if($tps=="number" || $tps=="range"){
				$classified_arr[$i][classifiedRules][isnumber]=1;
			}
			
			if($tps=="calendar" || $tps=="email" || $tps=="url" || $tps=="number" || $tps=="range"){
				$classified_arr[$i][classifiedType]="text";
			}
			switch($classified_arr[$i][classifiedType]){
				case "text":
					$classified_arr[$i][classifiedType]=1;
					break;
				case "radio":
					$classified_arr[$i][classifiedType]=2;
					break;
				case "checkbox":
					$classified_arr[$i][classifiedType]=3;
					break;
				case "select":
					$classified_arr[$i][classifiedType]=4;
					break;
				case "textarea":
					$classified_arr[$i][classifiedType]=5;
					break;
				case "image":
					$classified_arr[$i][classifiedType]=6;
					break;
				default:
					$classified_arr[$i][classifiedType]=0;
					break;
			}
			
			$choice_arr=explode("\r\n",$classified_arr[$i][classifiedRules][choices]);			
     		if($classified_arr[$i][classifiedRules][choices]!=""){
			
			foreach($choice_arr as $charr){
				$choice_val=explode("=",$charr);
				if($choice_val[0]==intval($choice_val[0])){
					$aaa['name']=$choice_val[1];
					$aaa['value']=$choice_val[0];
					$cd[]=$aaa;				 
					foreach($renxing as $rx){
						$msd=count($rx);
					}
				}
			}			
			$classified_arr[$i][classifiedRules][choices]=array_slice($cd,$msd); 
			$renxing[]=$cd;
		}
			
		if($classified_arr[$i][classifiedRules]==false){
			$classified_arr[$i][classifiedRules]=array();
		}
	} 
		

		// 对信息有效期的判断
		$sortInfo = DB::fetch_first('
			SELECT expiration
			FROM %t
			WHERE typeid=%d
			', 
			array('forum_threadtype', $fenlei_id)
		);
		$validStatus = $sortInfo['expiration'];

		if ($validStatus) {
			$array = array(
			    "classifiedTopId" =>"1",
			    "classifiedTitle" => Common::get_unicode_charset('\u4FE1\u606F\u6709\u6548\u671F'),
			    "classifiedName" => "typeexpiration",
			    "classifiedType" => 4,
			    "classifiedRules" => array(
			        "choices" => array(
			        	array(
			        		'name' => Common::get_unicode_charset('\u0033\u5929'),
			        		'value' => '259200'
			        	),
			        	array(
			        		'name' => Common::get_unicode_charset('\u0035\u5929'),
			        		'value' => '432000'
			        	),
			        	array(
			        		'name' => Common::get_unicode_charset('\u0037\u5929'),
			        		'value' => '604800'
			        	),
			        	array(
			        		'name' => Common::get_unicode_charset('\u0031\u4E2A\u6708'),
			        		'value' => '2592000'
			        	),
			        	array(
			        		'name' => Common::get_unicode_charset('\u0033\u4E2A\u6708'),
			        		'value' => '7776000'
			        	),
			        	array(
			        		'name' => Common::get_unicode_charset('\u534A\u5E74'),
			        		'value' => '15552000'
			        	),
			        	array(
			        		'name' => Common::get_unicode_charset('\u0031\u5E74'),
			        		'value' => '31536000'
			        	),			        				        				        	
			        ),
			        "inputsize" => ""
			    ),
			    "required" => "1",
			    "unchangeable" => "0"				
			);
			array_unshift($classified_arr, $array);
		}

		/*end  renxing  fenlei*/			 
		$data_post["classificationTopId"]=$fenlei_id;
		$data_post ["classified"] = $classified_arr;
		$data_post ["rs"] = 1;
		
		return $data_post;
	}
}

?>
