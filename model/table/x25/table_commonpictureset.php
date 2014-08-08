<?php
 
class commonpictureset
{
	public function  __construct()
	{
		$this->_table = 'forum_thread';
		$table = 'forum_threadimage';

	}
	public static function fetch_all_thread_img($start_limit, $limit)
	{
		$parameter = array (
				'forum_thread',
				'forum_threadimage',
		);
		$data = DB::fetch_all ( "SELECT * FROM %t as t right join %t as img  on t.tid=img.tid WHERE img.attachment !='' ORDER BY t.dateline DESC" . DB::limit ( $start_limit, $limit ),$parameter);
		return $data;
	}
}