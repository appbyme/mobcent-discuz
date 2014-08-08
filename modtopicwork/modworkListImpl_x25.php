<?php
require_once '../../source/class/class_core.php';
require_once '../../source/function/function_home.php';
require_once '../../config/config_ucenter.php';
require_once '../../uc_client/client.php';
require_once '../tool/tool.php';
require_once ('./abstractModworkList.php');
require_once '../public/mobcentDatabase.php';
require_once '../model/table/x25/table_common_member.php';
require_once libfile ( 'function/forum' );
require_once libfile('function/delete');
require_once libfile('function/post');
require_once libfile('function/threadsort');
define('ALLOWGUEST', 1);

// xss debug fixed
$tempMethod = $_SERVER['REQUEST_METHOD'];
$_SERVER['REQUEST_METHOD'] = 'POST';
define('DISABLEXSSCHECK', 1);

C::app ()->init ();

$_SERVER['REQUEST_METHOD'] = $tempMethod;

require_once '../model/table_forum_thread.php';
require_once '../model/table/x25/topic.php';
class modworkListImpl_x25 extends abstractModworkList {
	function getModworkListObj() {
		$info = new mobcentGetInfo();
		$accessSecret = $_GET['accessSecret'];
		$accessToken = $_GET['accessToken'];
		$arrAccess = $info->sel_accessTopkent($accessSecret,$accessToken);
		$uid = $_G['uid'] = $arrAccess['user_id'];
		$tid = $_GET ['topicId'];
		$modType = $_GET['type'];
		//$_GET['content']='%255B%257B%2522type%2522%253A0%252C%2522infor%2522%253A%2522cvccvvvvvv%2522%257D%255D';
		
		switch($modType)
		{
			case 1:
				try{
					$thread = C::t('forum_thread')->fetch($tid);
					if($thread['authorid'] != $_G['uid']){
						$data['rs'] = 0;
						$data['errcode'] ="01040214";
						return $data;
						exit;
					}
					$modaction = 'DLP';
					$modpostsnum =1;
					$tids =array($tid);
					deletethread($tids, true, true, true);
					manage_addnotify('verifyrecyclepost', $modpostsnum);
					$modpostsnum = 0;
					$posts =1;
					$today = dgmdate(TIMESTAMP, 'Y-m-d');
					if($modaction && $posts) {
						$affect_rows = C::t('forum_modwork')->increase_count_posts_by_uid_modaction_dateline(1, $posts, $_G['uid'], $modaction, $today);
						if(!$affect_rows) {
							C::t('forum_modwork')->insert(array(
							'uid' => $_G['uid'],
							'modaction' => $modaction,
							'dateline' => $today,
							'count' => 1,
							'posts' => $posts,
							));
						}
					}
					$data['rs'] = 1;
					return $data;
				}catch (Exception $e)
				{
					$data['rs'] = 0;
					$data['errcode'] = "01040214";
					return $data;
				}
				
				break;
			case 5:
				try{
					$pid = $_GET['postsId'];
					$thread = C::t('forum_thread')->fetch($tid);
					if($thread['authorid'] != $_G['uid']){
						$data['rs'] = 0;
						$data['errcode'] ="01040216";
						return $data;
						exit;
					}
					if($thread['closed'] == 1) {
						$data['rs'] = 0;
						$data['errcode'] ="01040216";
						return $data;
						exit;
					}
					
					$array_message=echo_array(urldecode(($_GET['content']))); 
					//print_r($get_content['content']);exit;
					$_G['forum_optiondata'] = threadsort_validator($array_message, $pid);
				
					$aid = $_REQUEST ['aid'];
					$aid_Img=explode(',',$aid);
					$aid_key=array_flip($aid_Img);
					$aid_key =array_unique($aid_key);
					
					$updateAid =$_REQUEST ['updateAid'];
					$updateAid_Img=explode(',',$updateAid);
					
					$attachment_Img = C::t('forum_attachment')->fetch_all_by_id('tid',$tid);
					$attachment_Img = array_keys($attachment_Img);
					foreach($attachment_Img as $optionid => $value) {
						if(!in_array($value,$updateAid_Img))
						{
									$value = censor($value);
									$identifier = $_G['forum_optionlist'][$optionid]['identifier'];
									$newsortaid = intval($value);
									$attach = C::t('forum_attachment_n')->fetch('tid:'.$tid, $value);
									C::t('forum_attachment')->delete($value);
									C::t('forum_attachment_n')->delete('tid:'.$tid, $value);
									dunlink($attach);
									$threadimageaid = $newsortaid;
									convertunusedattach($newsortaid, $tid, $pid);
						}
						
					}
					$subject = echo_urldecode ( $_GET ['title'] ) ;
					
					$message = '';
					$i=0;
					
					foreach ( $array_message as $k => $v ) {
						switch ($v ["type"]) {
							case 0 :
								$message .= $v ["infor"];
								break;
							case 1 :
			                    if(empty($aid_Img))
			                    {
			                        if ($aid != 0) {
			                            $message .= '[attachimg]' . $aid . '[/attachimg]';
			                        } else {
			                            $message .= '[img]'.$v['infor'].'[/img]';
			                        }
			                    }
			                    else
			                    {
			                        if ($aid_Img[$i] != 0) {
			                            $message .= '[attachimg]' . $aid_Img[$i] . '[/attachimg]';
			                        } else {
			                            $message .= '[img]'.$v['infor'].'[/img]';
			                        }
			                        $i=$i+1;
			                    }
								$attachment = 2;
								break;
						}
					}
					$message = smilesReplace($message);
					
					C::t('forum_threadimage')->delete($tid);
					$topicInstance = new topic();
					$topicInstance ->SetAttachment($aid_Img,$aid,$tid,$pid,$_G,$threadimage);
					updateattach($thread['displayorder'] == -4 || $_G['forum_auditstatuson'], $tid, $pid, $aid_key, $aid_Img, $_G['uid']);
					$message = preg_replace ( '/\[attachimg\](\d+)\[\/attachimg\]/is', '[attach]\1[/attach]', $message );
					$message = str_replace ( "[attachimg][/attachimg]", "", $message );
					$setarr = array(
							'message' => $message,
							'subject' =>$subject,
							'usesig' => $_GET['usesig'],
							'htmlon' => $htmlon,
							'bbcodeoff' => $bbcodeoff,
							'parseurloff' => $parseurloff,
							'smileyoff' => $smileyoff,
							'dateline' => time()
					);
					$data = array('subject' =>$subject);
					C::t('forum_post')-> update_by_tid('tid:'.$tid, $tid, $setarr, $unbuffered = false, $low_priority = false, $first =1, $invisible = null, $status = null);
					C::t('forum_thread')->update_by_tid_displayorder($tid, 0, $data, $fid = 0, $tableid = 0);
					$_G['uid'] = 2;
				
					$arr['rs'] = 1;
					return $arr;
					
				}catch (Exception $e)
				{
					$arr['rs']	 = 0;
					$arr['errcode'] ="01040216";
					return $arr;
				}
				break;
		
		}
		}

}

?>