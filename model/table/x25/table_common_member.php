<?php

class commonMember {
	public static function getUserStatus($uids){
		$uid = implode("','",$uids);
		$limit = count($uids);
		$userList = DB::fetch_all('SELECT uid,username,status FROM %t WHERE uid in(%n) ORDER BY uid desc LIMIT %d', array('common_member', $uids, $limit), 'uid');
		return $userList;
	}
	public static function Anonymous_User($fid)
	{
		$userList=DB::fetch('SELECT authorid FROM '.DB::table(forum_post).' WHERE tid= '.$fid);
		return $userList;
	}
	public static function userAccess($groupid){
		$userAllow = DB::fetch_first("SELECT * FROM %t WHERE groupid = %d",array('common_usergroup',$groupid));
		return $userAllow;
	}
}

?>