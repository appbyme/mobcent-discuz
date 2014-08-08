<?php
class table_forum_thread_moderate
{
	private static $_table='forum_thread_moderate';
	public static function insert($tid,$status,$time)
	{
		return DB::query('insert into %t(id,status,dateline) values(%d,%d,%s)', array(self::$_table, $tid,$status,$time));
	}
}