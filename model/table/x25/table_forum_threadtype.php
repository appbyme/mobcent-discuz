<?php
 
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_threadtype extends discuz_table
{
	public function __construct() {

		$this->_table = 'forum_threadtype';
		$this->_pk    = 'typeid';

		parent::__construct();
	}
 

	public function fetch_all_by_typeid($arr) {
		foreach($arr as $a){
			$result[]=DB::fetch_all('SELECT typeid as classificationTop_id,name as classificationTop_name FROM %t WHERE '.DB::field('typeid', $a), array($this->_table));
		}
		return $result;
	}
	
	public function fetch_name_by_typeid($typeid) {
		$result=DB::fetch_all('SELECT name FROM %t WHERE '.DB::field('typeid', $typeid), array($this->_table));
			
		return $result;
	}
}

?>