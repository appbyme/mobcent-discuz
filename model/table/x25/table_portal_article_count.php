<?php 

class table_portal_article_count
{
	public static function count_by_aid($aid)
	{
		return DB::fetch_first("SELECT *  FROM %t WHERE aid=%d ", array('portal_article_count', $aid));
	}
}
?>