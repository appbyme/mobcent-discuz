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
		$pid = $_GET ['replyPostsId'];
		$fid = $_GET ['boardId'];
		$modType = $_GET['type'];
		switch($modType)
		{
			case 1:
				try{
					$orig = C::t('forum_post')->fetch('tid:'.$tid, $pid, false);
					if($orig['authorid'] != $_G['uid']){
						$data['rs'] = 0;
						$data['errcode'] ="01040214";
						return $data;
						exit;
					}
					$_G['uid'] = 2;
					$modaction = 'DLP';
					$modpostsnum =1;
					$pids =array($pid);
					deletepost($pids, 'pid', true, false, true);
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
					
					$thread = C::t('forum_thread')->fetch($tid);
					$orig = C::t('forum_post')->fetch('tid:'.$tid, $pid, false);
					if($orig['authorid'] != $_G['uid']){
						$data['rs'] = 0;
						$data['errcode'] ="01040214";
						return $data;
						exit;
					}
				if($thread['closed'] == 1) {
						$data['rs'] = 0;
						$data['errcode'] ="01040216";
						return $data;
						exit;
				}
				$orig = C::t('forum_post')->fetch('tid:'.$tid, $pid, false);
				if($orig && $orig['fid'] == $fid && $orig['tid'] == $tid) {
					$user = getuserbyuid($orig['authorid']);
					$orig['adminid'] = $user['adminid'];
				} else {
					$orig = array();
				}
				$isorigauthor = $_G['uid'] && $_G['uid'] == $orig['authorid'];
				$isanonymous = ($_G['group']['allowanonymous'] || $orig['anonymous']) && getgpc('isanonymous') ? 1 : 0;
				$audit = $orig['invisible'] == -2 || $thread['displayorder'] == -2 ? $_GET['audit'] : 0;
				$rushreply = getstatus($thread['status'], 3);
				if($_G['group']['allowat']) {
					$atlist = $atlist_tmp = $ateduids = array();
					$atnum = $maxselect = 0;
					foreach(C::t('home_notification')->fetch_all_by_authorid_fromid($_G['uid'], $tid, 'at') as $row) {
						$atnum ++;
						$ateduids[$row[uid]] = $row['uid'];
					}
					$maxselect = $_G['group']['allowat'] - $atnum;
					preg_match_all("/@([^\r\n]*?)\s/i", $message.' ', $atlist_tmp);
					$atlist_tmp = array_slice(array_unique($atlist_tmp[1]), 0, $_G['group']['allowat']);
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
						if($atlist) {
							foreach($atlist as $atuid => $atusername) {
								$atsearch[] = "/@".str_replace('/', '\/', preg_quote($atusername))." /i";
								$atreplace[] = "[url=home.php?mod=space&uid=$atuid]@{$atusername}[/url] ";
							}
							$message = preg_replace($atsearch, $atreplace, $message.' ', 1);
						}
					}
				}
					$aid = $_REQUEST ['aid'];
					file_put_contents('aid.txt', $aid);
					$aid_Img=explode(',',$aid);
					$updateAid =$_REQUEST ['updateAid'];
					file_put_contents('uaid.txt', $updateAid);
					$updateAid_Img=explode(',',$updateAid);
					$Updateaid_Img = !empty($updateAid_Img)?array_merge($aid_Img,$updateAid_Img):$aid_Img;
					
					$subject = echo_urldecode ( $_GET ['title'] ) ;
					$array_message=echo_array(urldecode($_GET['content']));
					$message = '';
					$i=0;
					
					
					
						
					$attachment_Img = C::t('forum_attachment')->fetch_all_by_id('tid',$tid);
					$attachment_Img = array_keys($attachment_Img);
					foreach($attachment_Img as $optionid => $value) {
						$query = get_forum_attachment_unused($value);
						while ( $attach = DB::fetch ( $query ) ) {
						$aids = $attach ['aid'];
						if(!in_array($value,$updateAid_Img) && !empty($updateAid_Img))
						{
							file_put_contents('text.log', $value);
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
					}
					$topicInstance = new topic();
					$topicInstance ->SetAttachment($aid_Img,$aid,$tid,$pid,$_G,$threadimage);
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
			                            $message .= '[attachimg]' . $Updateaid_Img[$i] . '[/attachimg]';
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
					$message = preg_replace ( '/\[attachimg\](\d+)\[\/attachimg\]/is', '[attach]\1[/attach]', $message );
					$setarr = array(
							'message' => $message,
							'usesig' => $_GET['usesig'],
							'htmlon' => $htmlon,
							'bbcodeoff' => $bbcodeoff,
							'parseurloff' => $parseurloff,
							'smileyoff' => $smileyoff,
							'attachment' => $attachment,
							'dateline' => time()
					);
					C::t('forum_post')->update('tid:'.$tid, $pid, $setarr);
					if($_G['group']['allowat'] && $atlist) {
						foreach($atlist as $atuid => $atusername) {
							notification_add($atuid, 'at', 'at_message', array('from_id' => $tid, 'from_idtype' => 'at', 'buyerid' => $_G['uid'], 'buyer' => $_G['username'], 'tid' => $tid, 'subject' => $thread['subject'], 'pid' => $pid, 'message' => messagecutstr($message, 150)));
						}
						set_atlist_cookie(array_keys($atlist));
					}
				
					$data['rs'] = (Int)1;
					return $data;
					
				}catch (Exception $e)
				{
					$data['rs']	 = 0;
					$data['errcode'] =01040216;
					return $data;
				}
				break;
		
		}
		}

}

?>