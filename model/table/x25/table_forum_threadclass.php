<?php
 
class table_forum_threadclass extends discuz_table
{
	public function __construct() {

		$this->_table = 'forum_threadclass';
		$this->_pk    = 'typeid';

		parent::__construct();
	}
 
 
	public function fetch_all_by_typeid($typeid) {
		$result=DB::fetch_all('SELECT * FROM %t WHERE '.DB::field('typeid', $typeid), array($this->_table));
		return $result;
	}
	
	public function fetch_info_by_fid($fid) {
		$result=DB::fetch_all('SELECT typeid as classificationType_id,name as classificationType_name FROM %t WHERE '.DB::field('fid', $fid), array($this->_table));
		return $result;
	}
}

?>