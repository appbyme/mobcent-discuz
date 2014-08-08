<?php
 
class mobcentGetInfo
{
	public function getWebUid($id)
	{
		$query = DB::fetch_first("SELECT uid FROM ".DB::table('home_weibo')." WHERE sina_uid ='".$id."'");
		return $query;
	}
	public function getqqUid($id)
	{
		$query = DB::fetch_first("SELECT uid FROM ".DB::table('common_member_connect')." WHERE conopenid='".$id."' ");
		return $query;
	}
	public function forum_display($fid,$topicInstance)
	{
		if(!file_exists('../../data/attachment/appbyme')){
			$url=file_exists('../manage/App.xml')?join("",file('../manage/App.xml')):array();
		}else{
			$url=file_exists('../../data/attachment/appbyme/App.xml')?join("",file('../../data/attachment/appbyme/App.xml')):array();
		}
		$result =$topicInstance->xml_to_array($url);
		if(empty($result['board']['fid']))
		{
			$gid = intval(getgpc('gid'));
			$catlist = $forumlist = $sublist = $forumname = $collapse = $favforumlist = array();
			if(!$gid) {
				$forums = C::t('forum_forum')->fetch_all_by_status(1);
			
				$fids = array();
				foreach($forums as $forum) {
					$fids[$forum['fid']] = $forum['fid'];
				}
			
				$forum_access = array();
				if(!empty($_G['member']['accessmasks'])) {
					$forum_access = C::t('forum_access')->fetch_all_by_fid_uid($fids, $_G['uid']);
				}
			
				$forum_fields = C::t('forum_forumfield')->fetch_all($fids);
				foreach($forums as $forum) {
					if($forum_fields[$forum['fid']]['fid']) {
						$forum = array_merge($forum, $forum_fields[$forum['fid']]);
					}
					if($forum_access['fid']) {
						$forum = array_merge($forum, $forum_access[$forum['fid']]);
					}
					$forumname[$forum['fid']] = strip_tags($forum['name']);
					$forum['extra'] = empty($forum['extra']) ? array() : dunserialize($forum['extra']);
					if(!is_array($forum['extra'])) {
						$forum['extra'] = array();
					}
			
					if($forum['type'] != 'group') {
			
						$threads += $forum['threads'];
						$posts += $forum['posts'];
						$todayposts += $forum['todayposts'];
			
						if($forum['type'] == 'forum' && isset($catlist[$forum['fup']])) {
							if(forum($forum)) {
								$catlist[$forum['fup']]['forums'][] = $forum['fid'];
								$forum['orderid'] = $catlist[$forum['fup']]['forumscount']++;
								$forum['subforums'] = '';
								$forumlist[$forum['fid']] = $forum;
							}
			
						} elseif(isset($forumlist[$forum['fup']])) {
			
							$forumlist[$forum['fup']]['threads'] += $forum['threads'];
							$forumlist[$forum['fup']]['posts'] += $forum['posts'];
							$forumlist[$forum['fup']]['todayposts'] += $forum['todayposts'];
							if($_G['setting']['subforumsindex'] && $forumlist[$forum['fup']]['permission'] == 2 && !($forumlist[$forum['fup']]['simple'] & 16) || ($forumlist[$forum['fup']]['simple'] & 8)) {
								$forumurl = !empty($forum['domain']) && !empty($_G['setting']['domain']['root']['forum']) ? 'http://'.$forum['domain'].'.'.$_G['setting']['domain']['root']['forum'] : 'forum.php?mod=forumdisplay&fid='.$forum['fid'];
								$forumlist[$forum['fup']]['subforums'] .= (empty($forumlist[$forum['fup']]['subforums']) ? '' : ', ').'<a href="'.$forumurl.'" '.(!empty($forum['extra']['namecolor']) ? ' style="color: ' . $forum['extra']['namecolor'].';"' : '') . '>'.$forum['name'].'</a>';
							}
						}
			
					} else {
			
						if($forum['moderators']) {
							$forum['moderators'] = moddisplay($forum['moderators'], 'flat');
						}
						$forum['forumscount'] 	= 0;
						$catlist[$forum['fid']] = $forum;
			
					}
				}
			}else {
				$gquery = C::t('forum_forum')->fetch_all_info_by_fids($gid);
				$query = C::t('forum_forum')->fetch_all_info_by_fids(0, 1, 0, $gid, 1, 0, 0, 'forum');
				if(!empty($_G['member']['accessmasks'])) {
					$fids = array_keys($query);
					$accesslist = C::t('forum_access')->fetch_all_by_fid_uid($fids, $_G['uid']);
					foreach($query as $key => $val) {
						$query[$key]['allowview'] = $accesslist[$key];
					}
				}
				$query = array_merge($gquery, $query);
				$fids = array();
				foreach($query as $forum) {
					$forum['extra'] = dunserialize($forum['extra']);
					if(!is_array($forum['extra'])) {
						$forum['extra'] = array();
					}
					if($forum['type'] != 'group') {
						$threads += $forum['threads'];
						$posts += $forum['posts'];
						$todayposts += $forum['todayposts'];
						if(forum($forum)) {
							$forum['orderid'] = $catlist[$forum['fup']]['forumscount'] ++;
							$forum['subforums'] = '';
							$forumlist[$forum['fid']] = $forum;
							$catlist[$forum['fup']]['forums'][] = $forum['fid'];
							$fids[] = $forum['fid'];
						}
					} else {
						$forum['collapseimg'] = 'collapsed_no.gif';
						$collapse['category_'.$forum['fid']] = '';
			
						if($forum['moderators']) {
							$forum['moderators'] = moddisplay($forum['moderators'], 'flat');
						}
						$catlist[$forum['fid']] = $forum;
			
						$navigation = '<em>&rsaquo;</em> '.$forum['name'];
						$navtitle_g = strip_tags($forum['name']);
					}
				}
				unset($forum_access, $forum_fields);
				if($catlist) {
					foreach($catlist as $key => $var) {
						$catlist[$key]['forumcolumns'] = $var['catforumcolumns'];
						if($var['forumscount'] && $var['catforumcolumns']) {
							$catlist[$key]['forumcolwidth'] = (floor(100 / $var['catforumcolumns']) - 0.1).'%';
							$catlist[$key]['endrows'] = '';
							if($colspan = $var['forumscount'] % $var['catforumcolumns']) {
								while(($var['catforumcolumns'] - $colspan) > 0) {
									$catlist[$key]['endrows'] .= '<td>&nbsp;</td>';
									$colspan ++;
								}
								$catlist[$key]['endrows'] .= '</tr>';
							}
						}
					}
					unset($catid, $category);
				}
				$query = C::t('forum_forum')->fetch_all_subforum_by_fup($fids);
				foreach($query as $forum) {
					if($_G['setting']['subforumsindex'] && $forumlist[$forum['fup']]['permission'] == 2) {
						$forumurl = !empty($forum['domain']) && !empty($_G['setting']['domain']['root']['forum']) ? 'http://'.$forum['domain'].'.'.$_G['setting']['domain']['root']['forum'] : 'forum.php?mod=forumdisplay&fid='.$forum['fid'];
						$forumlist[$forum['fup']]['subforums'] .= '<a href="'.$forumurl.'"><u>'.$forum['name'].'</u></a>&nbsp;&nbsp;';
					}
					$forumlist[$forum['fup']]['threads'] 	+= $forum['threads'];
					$forumlist[$forum['fup']]['posts'] 	+= $forum['posts'];
					$forumlist[$forum['fup']]['todayposts'] += $forum['todayposts'];
			
				}
			}
			foreach($forumlist as $key =>$val)
			{
				$tids[]=$key;
			}
			$tids = implode(',', $tids);
			$tids =empty($tids)?0:$tids;
		}else{
			if(count($result[board]['fid']) ==1){
				$arr[]=$result[board]['fid'][0];
			}else{
				foreach($result[board]['fid'] as $key =>$val)
				{
					$arr[] =$val[0];
				}
			}
			$tids =implode(',', $arr);
			$tids = rtrim($tids,',');
		}
		return $tids;
	}
	public function rank_check_allow($accessSecret,$accessToken,$qquser)
	{
		if(empty($accessSecret) || empty($accessToken))
		{
			$group = $this-> sel_QQuser($qquser);
		}
		else
		{
			$arrAccess = $this->sel_accessTopkent($accessSecret,$accessToken);
			$userId = $arrAccess['user_id'];
			if(empty($userId))
			{
				return $this -> userAccessError();
				exit();
			}
			
			$group = $this-> sel_group_by_uid_allow($userId);
		}
		return $group;
	}
	public function isAllowdirectpost($groupid){
	    $query = DB::fetch_first("SELECT allowdirectpost FROM .".DB::table('common_usergroup_field'). " WHERE groupid=%d",array($groupid));
	    return $query;
	}
	public function search_check_allow($accessSecret,$accessToken,$qquser)
	{
		if(empty($accessSecret) || empty($accessToken))
		{
			$group = $this-> rx_QQuser($qquser);
		}
		else
		{
			$arrAccess = $this->sel_accessTopkent($accessSecret,$accessToken);
			$userId = $arrAccess['user_id'];
			if(empty($userId))
			{
				return $this -> userAccessError();
				exit();
			}
				
			$group = $this-> rx_group_by_uid_allow($userId);
		}
		return $group;
	}
	public function getUserGroupRank($groupid)
	{
		if ($groupid){
			$user_limit = table_common_member::userAccess($groupid);
			if ($user_limit['allowvisit'] == 0){
				$res['rs'] = 0;
				$res['errcode'] = '01010044';
				return $res; exit();
			}
			else
			{
				return true;
			}
		}
	}
	public function userAccessError()
	{
		$data_post['rs'] = 0;
		$data_post['errcode'] = 50000000;
		return $data_post;
	}
	public function getUserInfo($uid){
		$uid = empty($uid)?0:(int)$uid;
		return getuserbyuid($uid,1);
	}
	public function getBoard($boardId){
		return $board = C::t('forum_forum')->fetch_info_by_fid($boardId);
		if($board['type']!='group'){
			return false;
		}else{
			return $board;
		}
	}
	public function getForum($forumId){
		return $forum = C::t('forum_forum')->fetch_info_by_fid($forumId);
		if($forum['type']!='forum'){
			return false;
		}else{
			return $forum;
		}
	}
	public function getForumSub($SubId){
		return $Sub = C::t('forum_forum')->fetch_info_by_fid($SubId);
		if($Sub['type']!='sub'){
			return false;
		}else{
			return $Sub;
		}
	}
	public function sel_accessTopkentByUid($uid)
	{
        $tableName = 'appbyme_user_access';
		$query = DB::fetch_first('SELECT user_access_token, user_access_secret FROM '.DB::table($tableName).' WHERE user_id =%d',array($uid));
		return $query;
	}
	public function sel_accessTopkent($accessSecret,$accessToken)
	{
		$tableName = 'appbyme_user_access';
		$query = DB::fetch_first('SELECT * FROM '.DB::table($tableName).' WHERE user_access_token =%s AND user_access_secret = %s',array($accessToken,$accessSecret));
		return $query;
	}
	
	public function inser_accessTopkent($accessToken,$accessSecret,$uid,$time)
	{
		$tableName = 'appbyme_user_access';
		$time = strtotime($time);
		$query = DB::query('INSERT INTO '.DB::table($tableName).' VALUES(user_access_id,%s,%s,%d,%s)',array($accessToken,$accessSecret,$uid,$time));
		return $query;
	}
	public function update_accessTopkent($accessToken,$accessSecret,$uid,$time)
	{
		$tableName = 'appbyme_user_access';
		$time = strtotime($time);
		$query = DB::query('UPDATE '.DB::table($tableName).' SET user_access_secret = %s,create_time = %s WHERE user_id =%d AND user_access_token =%s',array($accessSecret,$time,$uid,$accessToken));
		return $query;
	}
	public function rx_QQuser($qquser)
	{
		$query = DB::fetch_first('SELECT a.*,b.* FROM '.DB::table('common_usergroup').' as a LEFT JOIN '.DB::table('common_usergroup_field').' as b on a.groupid = b.groupid WHERE a.groupid = 7');
		return $query;
	}
	public function rx_group_by_uid_allow($user)
	{
		$query = DB::fetch_first('SELECT b.*,c.* FROM  '.DB::table('common_member').' as a LEFT JOIN '.DB::table('common_usergroup').' as b ON a.groupid = b.groupid LEFT JOIN '.DB::table('common_usergroup_field').' as c ON a.groupid = c.groupid WHERE a.uid = %d',array($user));
		return $query;
	}
	public function sel_QQuser($qquser)
	{
		$query = DB::fetch_first('SELECT readaccess,a.allowvisit,a.groupid,b.allowpostpoll FROM '.DB::table('common_usergroup').' as a LEFT JOIN '.DB::table('common_usergroup_field').' as b on a.groupid = b.groupid WHERE a.groupid = 7');
		return $query;
	}
	public function sel_group_by_uid_allow($user)
	{
		$query = DB::fetch_first('SELECT b.allowvisit, b.groupid FROM  '.DB::table('common_member').' as a LEFT JOIN '.DB::table('common_usergroup').' as b on a.groupid = b.groupid WHERE a.uid = %d',array($user));
		return $query;
	}
	public function sel_group_by_uid($user)
	{
		$query = DB::fetch_first('SELECT b.readaccess, b.groupid,b.allowpostpoll FROM  '.DB::table('common_member').' as a LEFT JOIN '.DB::table('common_usergroup_field').' as b on a.groupid = b.groupid WHERE a.uid = %d',array($user));
		return $query;
	}
	public function updateUserStatus($uid,$status){
		$query = DB::fetch_first("UPDATE ".DB::table('common_member') ." SET status =".$status." WHERE uid = " .$uid);
		return $query;
	}
}