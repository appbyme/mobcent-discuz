<?php
 
class surround_user
{
	private static $_table='home_surrounding_user';
	private static $_topic='forum_post';
	private static $_thread='forum_thread';
	private static $_forum = 'forum_forum';
	public function __construct()
	{
		$parameter = array (
				'common_member',
				'home_surrounding_user'
		);

	}
	 
	public static function fetch_num_by_userid($userid)
	{
		$data=DB::fetch_first("SELECT count(*) as num FROM %t WHERE object_id= %d AND type=1",array(self::$_table,$userid));
		return $data['num'];
	}
	 
	public static function fetch_update_by_userid($longitude,$latitude,$location,$userId)
	{
		$data = DB::query('UPDATE %t SET longitude = %f, latitude = %f, location = %s  WHERE object_id= %d AND type=1',array(self::$_table,$longitude,$latitude,$location,$userId), false, true);
		return $data;
	}
	 
	public static function fetch_insert_by_userid($longitude,$latitude,$location,$userId)
	{
		
		$data = DB::query('INSERT INTO  %t VALUES(poi_id,%f,%f,%d,%d,%s)',array(self::$_table,$longitude,$latitude,$userId,1,$location));
		return $data;
	}
	 
	public static function fetch_all_surrounding_user($userId,$longitude,$latitude,$longpoor,$longsum,$latpoor,$latsum,$start_limit,$limit)
	{
		$data = DB::fetch_all('SELECT user.*,poi_id,longitude,latitude,SQRT(POW((%f-longitude)/0.012*1023,2)+POW((%f-latitude)/0.009*1001,2)) AS distance,location FROM '.DB::table('common_member').' as user,'.DB::table('home_surrounding_user').' as poi WHERE user.uid=poi.object_id AND type= 1 AND  poi.object_id<> %d AND longitude between '.$longpoor.'  AND '.$longsum.'  AND latitude between '.$latpoor.'  AND '.$latsum.'    ORDER BY distance limit '.$start_limit.','.$limit.'',array($longitude,$latitude,$userId));
		return $data;
		
	}
	public static function fetch_all_surrounding_user_count($userId,$longitude,$latitude,$longpoor,$longsum,$latpoor,$latsum)
	{
		$data = DB::fetch_first('SELECT count(user.uid) as num FROM '.DB::table('common_member').' as user,'.DB::table('home_surrounding_user').' as poi WHERE user.uid=poi.object_id AND type= 1 AND  poi.object_id<> %d AND longitude between '.$longpoor.'  AND '.$longsum.'  AND latitude between '.$latpoor.'  AND '.$latsum.'',array($longitude,$latitude,$userId));
		return $data['num'];
	
	}
	 
	public static function insert_all_apply_location($longitude,$latitude,$location,$pid)
	{
		$data = DB::query('INSERT INTO  %t VALUES(poi_id,%f,%f,%d,%d,%s)',array(self::$_table,$longitude,$latitude,$pid,2,$location));
		return $data;
	}
	 
	public static function insert_all_thread_location($longitude,$latitude,$location,$pid)
	{
		$data = DB::query('INSERT INTO  %t VALUES(poi_id,%f,%f,%d,%d,%s)',array(self::$_table,$longitude,$latitude,$pid,3,$location));
		return $data;
	}
	 
	public static function fetch_all_by_pid($pid)
	{
		$data = DB::fetch_first('SELECT location FROM %t WHERE type != 1 and object_id = %d',array(self::$_table,$pid));
		return $data;
	}
	 
	public static function fetch_all_surrounding_topic_count($longitude,$latitude,$longpoor,$longsum,$latpoor,$latsum)
	{
		$data = DB::fetch_first("SELECT count(*) as num FROM %t WHERE type=3 AND longitude between %d  AND %d  AND latitude between %d  AND %d ",array(self::$_table,$longpoor,$longsum,$latpoor,$latsum));
		return $data['num'];
	}
	 
	public static function fetch_all_surrounding_topic($longitude,$latitude,$longpoor,$longsum,$latpoor,$latsum,$start_limit,$limit)
	{
		$data = DB::fetch_all("SELECT topic.pid,topic.tid,topic.fid,topic.first,topic.attachment,longitude,latitude,SQRT(POW((%f-longitude)/0.012*1023,2)+POW((%f-latitude)/0.009*1001,2)) AS distance,location FROM %t as pio ,%t topic  WHERE topic.pid=pio.object_id AND pio.type=3 AND pio.longitude between %d  AND %d  AND pio.latitude between %d  AND %d ORDER BY distance limit %d,%d",array($longitude,$latitude,self::$_table,self::$_topic,$longpoor,$longsum,$latpoor,$latsum,$start_limit,$limit));
		return $data;
	}
	public static function fetch_all_surrounding_topic_info($topicid)
	{
		$data = DB::fetch_all('SELECT * FROM %t WHERE tid = %d',array(self::$_thread,$topicid));
		return $data;
	}
	public static function fetch_border_by_fid($fid)
	{
		$data = DB::fetch_first("SELECT name FROM %t WHERE fid = %d",array(self::$_forum,$fid));
		return $data['name'];
	}
}