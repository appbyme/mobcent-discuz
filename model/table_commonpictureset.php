<?php
class commonpictureset
{
	public function  __construct()
	{
		$this->_table = 'forum_thread';
		$table = 'forum_threadimage';

	}
	public static function fetch_all_thread_img($start_limit, $limit,$tids)
	{
		$parameter = array (
				'forum_thread',
				'forum_threadimage',
		);
		$data = DB::fetch_all("SELECT * FROM %t AS g RIGHT JOIN %t AS img ON g.tid=img.tid WHERE img.attachment !='' AND g.displayorder >-1 and g.fid in(".$tids.") ORDER BY g.dateline DESC ". DB::limit ( $start_limit, $limit ),$parameter);
		return $data;
	}
	
}