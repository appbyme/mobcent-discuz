<?php
 
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_forum_typeoption extends discuz_table
{
	public function __construct() {

		$this->_table = 'forum_typeoption';
		$this->_pk    = 'typeid';

		parent::__construct();
	}
 

	public function fetch_all_by_classid($classid,$order = 'asc') {
		$result=DB::fetch_all('SELECT optionid, classid as classifiedTopId,title as classifiedTitle,identifier as classifiedName,type as classifiedType,rules as classifiedRules FROM %t WHERE '.DB::field('classid', $classid).($order ? 'ORDER BY '.DB::order('displayorder', $order) : ''), 
				array($this->_table));	 
		return $result;
	}
	
	 
	function fetch_info_by_classid($sortid){
		$sql =  "SELECT a.optionid,a.classid as classifiedTopId,a.title as classifiedTitle,a.identifier as classifiedName,a.type as classifiedType,a.rules as classifiedRules,b.* FROM %t as a left join %t as b  on a.optionid=b.optionid WHERE b.sortid=".$sortid." order by b.displayorder asc";
		$arr = array ('forum_typeoption','forum_typevar');
		return DB::fetch_all($sql,$arr);
	}
}

?>