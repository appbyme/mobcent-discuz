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
	$sql = 'select subject,special from %t where tid=%d';
	$array1 = array('forum_thread',$tid); 
	$data = DB::result_first($sql,$array1);
	return $data;
}

function fetch_all_threadimage($group)
{
	$query = DB::query ( "SELECT B.tid,B.attachment,A.dateline from ".DB::table('forum_thread')." as A,".DB::table('forum_threadimage')." as B  where A.tid = B.tid AND B.tid=".( int ) $group ['tid']);
	$post = DB::fetch($query);
	return $post;
}

function get_special_by_tid($tid){
	$sql = 'select special from %t where tid=%d';
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
	$sql = "SELECT * FROM %t WHERE pid=%d limit 1";
	$array1 = array ('forum_post',$toReplyId);
	return DB::fetch_first ($sql , $array1);
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

/*renxing 2013.5.22*/
function fetch($tid, $tableid = 0) {
	$tid = intval($tid);
	$data = array();
	if($tid && ($data = $this->fetch_cache($tid)) === false) {
		$parameter = array($this->get_table_name($tableid), $tid);
		$data = DB::fetch_first("SELECT * FROM %t WHERE tid=%d", $parameter);
		if(!empty($data)) $this->store_cache($tid, $data, $this->_cache_ttl);
	}
	return $data;
}
/*end renxing*/
?>