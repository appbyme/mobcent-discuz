<?php
	class table_forum_announcement
	{
		public static function fetch_all_by_displayorder()
		{
		return DB::fetch_all('SELECT * FROM '.DB::table('forum_announcement').' WHERE endtime >= '.time().' OR endtime = 0 ORDER BY displayorder, starttime DESC');
		}
	}
?>