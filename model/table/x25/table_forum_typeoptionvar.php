<?php

 
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_typeoptionvar extends discuz_table
{
	public function __construct() {

		$this->_table = 'forum_typeoptionvar';
		$this->_pk    = '';

		parent::__construct();
	}
	
	public function fetch_all_by_tid_optionid($tids, $optionids = null) {
		if(empty($tids)) {
			return array();
		}
		return DB::fetch_all('SELECT * FROM %t WHERE '.DB::field('tid', $tids).($optionids ? ' AND '.DB::field('optionid', $optionids) : ''), array($this->_table));
	}	
	
	public function fetch_all_by_fid($fid) {		
		return DB::fetch_all('SELECT * FROM %t WHERE '.DB::field('fid', $fid), array($this->_table));
	}
	
	public function insert($data, $return_insert_id = false, $replace = false, $silent = false) {
		if($data && is_array($data)) {			
			return DB::insert($this->_table, $data, $return_insert_id, $replace, $silent);
		}
		return 0;
	}

}

?>