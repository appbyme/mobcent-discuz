<?php

class table_common_block{
	public static function selAllBlackData($start_limit,$limit)
	{
		return DB::fetch_all(" SELECT * FROM %t where bid in (34,31,20,21,22) ORDER BY itemid DESC limit $start_limit,$limit",array('common_block_item'));
	
	}
	public static function selAllBlackItemData($classItem,$start_limit,$limit)
	{
		return DB::fetch_all(" SELECT * FROM %t where bid in ($classItem) ORDER BY displayorder, itemtype DESC limit $start_limit,$limit",array('common_block_item'));
		
	}
	
	public static function selAllSlidebid()
	{
		return DB::result_first(" SELECT * FROM %t where styleid =175",array('common_block'));
	
	}
	
	public static function selAllBlackDataNum($classItem)
	{
		return DB::result_first(" SELECT count(*) num  FROM %t where bid in ($classItem)",array('common_block_item'));
	}
	
	public static function selAllBlackPicData($bid,$strat,$piclimit)
	{
		$bid = empty($bid)?-1:$bid;
		return DB::fetch_all(" SELECT * FROM %t where bid = $bid ORDER BY displayorder, itemtype DESC LIMIT $strat,$piclimit",array('common_block_item'));
	}
}
?>