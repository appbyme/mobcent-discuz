<?php

 
require_once('../../source/class/discuz/discuz_database.php');
class forum_post_location 
{
	public function __construct() {

		$this->_table = 'forum_post_location';
		$this->_pk    = 'pid';
		$this->_pre_cache_key = 'forum_post_location_';
		$this->_cache_ttl = 0;

		parent::__construct();
	}

	public static function insert_all_apply_location($longitude,$latitude,$location,$userId,$pid,$tid)
	{

		$data = DB::query('INSERT INTO  %t VALUES(%d,%d,%d,%f,%f,%s)',array($this->_table,$pid,$tid,$userId,$longitude,$latitude,$location));
		return $data;
	}
}

?>