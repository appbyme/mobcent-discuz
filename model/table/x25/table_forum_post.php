<?php
class table_forum_post {
	private static $tablename = 'forum_post';
	private static $_pk = 'pid';
	public static function fetch_all_by_authorid($tableid, $authorid, $outmsg = true, $order = '', $start = 0, $limit = 0, $first = null, $invisible = null, $fid = null, $filterfid = null) {
		$array = array (
				'forum_post',
				'forum_thread ',
				$authorid,
				$start,
				$limit 
		);
		$query = DB::query ( 'SELECT a.pid,b.tid,b.attachment,b.subject,b.special FROM %t  a INNER JOIN %t b ON a.tid = b.tid WHERE a.authorid=%d AND a.first=0 GROUP BY b.tid '  . ($order ? 'ORDER BY a.dateline ' . $order : '') . DB::limit ( $start, $limit ), $array );
		while ( $post = DB::fetch ( $query ) ) {
			if (! $outmsg) {
				unset ( $post ['message'] );
			}
			$postlist [$post [self::$_pk]] = $post;
		}
		
		return $postlist;
	}
	public function count_by_authorid($tableid, $authorid) {
		return DB::result_first('SELECT COUNT(*) FROM %t WHERE authorid=%d AND invisible=0', array('forum_post', $authorid));
	}
}