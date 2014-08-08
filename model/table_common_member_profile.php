<?php
class memberProfile
{
	private $_fields;
	public function __construct() {
		$this->_table = 'common_member_profile';
		
	}
	public function get_profile_by_uid($uids, $file, $start, $limit) {
		$uidss = implode ( ',', $uids );
		$parameter = array (
				$this->_table,
				'common_member',
				'common_usergroup' 
		);
		if ($uidss){
			$where = ' AND a.uid in(' . $uidss . ')  ';
		}
		return DB::fetch_all ( "SELECT c.stars,b.groupid,a.uid," . $file . " FROM %t AS a left join %t AS b ON a.uid=b.uid left join %t AS c ON b.groupid=c.groupid WHERE 1=1 " . $where . 'ORDER BY b.credits DESC' ,$parameter, 'uid' );
	}

	public static function fetch_gender_field_value($field,$userId) {
		$sql = 'SELECT '.$field.' as gender FROM %t WHERE uid=%d limit 1';
		$array1 = array ('common_member_profile',$userId);
		$gender = DB::fetch_first ($sql , $array1);
		return $gender['gender'];		
	}
	public static function get_online_member_count($todaytime,$tomorrowtime){
		$sql = 'SELECT COUNT(*) FROM %t where lastolupdate between %s and %s';
		$arr =  array('common_session',$todaytime,$tomorrowtime);
		return DB::result_first($sql,$arr);
	}
}

?>