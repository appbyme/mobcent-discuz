<?php
function digest($uid,$digest,$start = 0, $limit = 0, $tableid = 0,$digestglue = '>=', $displayorder = 0, $glue = '>=') {
	$thread_obj = C::t('forum_thread');
	$parameter = array($thread_obj->get_table_name($tableid), $digest, $displayorder);
	$digestglue = helper_util::check_glue($digestglue);
	$glue = helper_util::check_glue($glue);
	if($uid) $where='authorid='.$uid.' AND ';
	
	$data = DB::fetch_all("SELECT * FROM %t WHERE ".$where." digest{$digestglue}%d AND displayorder{$glue}%d".DB::limit($start, $limit), $parameter, 'tid');
	$data[count] = DB::result_first("SELECT count(*) FROM %t WHERE ".$where." digest{$digestglue}%d AND displayorder{$glue}%d", $parameter, 'tid');
	return $data;
}

function get_subject_by_tid($tid){
	$sql = 'select subject from %t where tid=%d';
	$array1 = array('forum_thread',$tid); 
	$data = DB::result_first($sql,$array1);
	return $data;
}
function update_thread_pos($postionid,$author,$tid){
	$sql = 'UPDATE %t SET maxposition=%d,lastposter=%s,replies=replies+1,lastpost=%d WHERE tid=%d';
	$array1 = array('forum_thread',$postionid,$author,time(),$tid);
	return DB::query($sql,$array1);
}
 
function get_forum_attachment_unused($val){
	$sql = "SELECT * FROM %t WHERE aid=%d";
	$array1 = array('forum_attachment_unused',$val);
	return  DB::query ($sql , $array1);
}
 
function update_forum_attachment($tid,$attachtableid,$uid,$pid,$aids){
	$sql = "UPDATE %t SET tid=%d,tableid=%d,uid=%d,pid=%d WHERE aid IN (%n)";
	$array1 = array ('forum_attachment',$tid,$attachtableid,$uid,$pid,$aids);
	return DB::query($sql,$array1);
}
function get_forum_post_by_pid($toReplyId){
	$sql = "SELECT * FROM ".DB::table(forum_post)." WHERE pid=".$toReplyId." limit 1";
	return DB::fetch_first ($sql);
}
 
function get_hot_invitation($start_limit,$limit){
	$sql = "SELECT * FROM %t as t right join %t as img  on t.tid=img.tid WHERE img.attachment !='' ";
    $sql .= "DB::limit ( $start_limit, $limit )";
    $arr = array ('forum_thread','forum_threadimage');
	return  DB::fetch_all ($sql,$arr,'tid');
}
 
function get_thread_count(){
	$sql =  "SELECT count(*) as num  FROM %t as t right join %t as img  on t.tid=img.tid WHERE img.attachment !='' ";
	$arr = array ('forum_thread','forum_threadimage');
	return DB::fetch_first($sql,$arr);	
}

?>

