<?php
class table_home_notification
{
	private static $tablename = 'home_notification';
	private static $_pk = 'pid';
	private static $_table='home_notification';
	
	public static function fetch_all_by_uid($uid, $new, $type, $start, $perpage)
	{
		$new = intval($new);
		$type = $type ? ' AND ('.$type.')': '';
		$new = ' AND '.DB::field('new', $new);
		return DB::fetch_all("SELECT * FROM %t WHERE uid=%d %i %i ORDER BY new DESC, dateline DESC %i", array(self::$tablename, $uid, $type, $new, DB::limit($start, $perpage)));
	}
	public static function count_by_uid($uid, $new, $type,$tids)
	{
		$new = intval($new);
		$str = explode('=',$type);
		$new = ' AND '.DB::field('new', $new);
		$arr =array(" 'post'");
		if($str[1] ===$arr[0])
		{
			$type = $type ? $type.'': '';
			$tNum = DB::fetch_first("SELECT COUNT(DISTINCT a.id) AS num FROM %t as a,%t as b WHERE  a.type='post' AND a.from_id=b.tid AND  a.uid = %d %i AND b.fid IN(".$tids.")", array(self::$tablename,'forum_post', $uid,  $new, $type));
			$qNum = DB::fetch_first("SELECT COUNT(DISTINCT a.id) AS num FROM %t as a,%t as b WHERE  a.type='post' AND a.from_id=b.pid AND  a.uid = %d %i AND b.fid IN(".$tids.")", array(self::$tablename,'forum_post', $uid,  $new, $type));
		}else{
			$type = $type ? ' '.$type.'': '';
			$tNum = DB::fetch_first("SELECT COUNT(DISTINCT a.id) AS num FROM %t as a,%t as b WHERE  ".$type." AND a.from_id=b.tid AND  a.uid = %d %i AND b.fid IN(".$tids.")", array(self::$tablename,'forum_post', $uid,  $new, $type));
			$qNum = DB::fetch_first("SELECT COUNT(DISTINCT a.id) AS num FROM %t as a,%t as b WHERE  ".$type." AND a.from_id=b.pid AND  a.uid = %d %i AND b.fid IN(".$tids.")", array(self::$tablename,'forum_post', $uid,  $new, $type));
		}
		$num = $qNum['num']+$tNum['num'];
		return ceil($num/2);
	}

	public static function  isread($type, $uid){
		$sql = 'UPDATE %t SET new = 0 WHERE type =%s AND uid =%d AND new = 1';
		$arr = array('home_notification',$type, $uid);
		return DB::query($sql,$arr);
	}
	public static function get_notificationCount($uid,$type,$tids){
		if($type =='post')
		{
			$Psql = DB::fetch_first("SELECT count(*) as num FROM %t as a,%t as b WHERE  a.type='post' AND a.from_id=b.pid AND  a.uid = ".$uid." AND b.fid IN(".$tids.")",array('home_notification','forum_post',$uid,$type, DB::limit ($start,$perpage)));
			$Tsql = DB::fetch_first("SELECT count(*) as num FROM %t as a,%t as b WHERE  a.type='post' AND a.from_id=b.tid AND  a.uid = ".$uid." AND b.fid IN(".$tids.")",array('home_notification','forum_post',$uid,$type, DB::limit ($start,$perpage)));
		}else{
			$Tsql= DB::fetch_first("SELECT count(*) as num FROM %t as a,%t as b WHERE  a.type='at' AND a.from_id=b.tid AND  a.uid = ".$uid." AND b.fid IN(".$tids.")",array('home_notification','forum_post',$uid,$type, DB::limit ($start,$perpage)));
			$Psql= DB::fetch_first("SELECT count(*) as num FROM %t as a,%t as b WHERE  a.type='at' AND a.from_id=b.Pid AND  a.uid = ".$uid." AND b.fid IN(".$tids.")",array('home_notification','forum_post',$uid,$type, DB::limit ($start,$perpage)));
		}
		return $Psql['num']+$Tsql['num'];
	}
	public static function get_notification($uid,$type,$start,$perpage,$tids){
		if($type =='post')
		{
			$ArrId = DB::fetch_all("SELECT id,from_id,author,new,authorid,note FROM %t WHERE type='post' AND  uid = ".$uid." ORDER BY dateline DESC limit ".$start.','.$perpage,array('home_notification',$uid));
			
			foreach($ArrId as $key=>$val)
			{
				$postTids[]=$val['from_id'];
				$postreplys[$val['from_id']]=$val;
			}
			
			$postTids = implode(',',$postTids);
			$postTids = empty($postTids)?0:$postTids;
			$postreply = DB::fetch_all("SELECT tid,pid,fid,subject,message,dateline FROM %t WHERE  fid in(".$tids.") AND tid in (".$postTids.") or pid in(".$postTids.") ORDER BY dateline DESC limit ".$start.','.$perpage,array('forum_post',$tids,));
			foreach($postreplys as $key =>$Pval)
			{
				foreach($postreply as $rkey=>$rval)
				{
					if($key ==$rval['tid'] || $key ==$rval['pid']){
						if($key ==$rval['tid']){
							$postArrReplys[$rval['tid']][]=array_merge($postreplys[$key],$rval);
							continue;
						}else if($key ==$rval['pid']){
							$postArrReplys[$rval['pid']][]=array_merge($postreplys[$key],$rval);
						}
					}
				}
			}
			
		}else{
			$ArrId = DB::fetch_all("SELECT id,from_id,author,new,authorid,note FROM %t WHERE type='at' AND  uid = ".$uid." ORDER BY dateline DESC limit ".$start.','.$perpage,array('home_notification',$uid, DB::limit ($start,$perpage)));
			foreach($ArrId as $key=>$val)
			{
				$postTids[]=$val['from_id'];
				$postreplys[$val['from_id']]=$val;
			}
				
			$postTids = implode(',',$postTids);
			$postTids = empty($postTids)?0:$postTids;
			$postreply = DB::fetch_all("SELECT tid,pid,fid,subject,message,dateline FROM %t WHERE  fid in(".$tids.") AND tid in (".$postTids.") or pid in(".$postTids.") ORDER BY dateline DESC limit ".$start.','.$perpage,array('forum_post',$tids,));
			
				
			foreach($postreplys as $key =>$Pval)
			{
				foreach($postreply as $rkey=>$rval)
				{
					if($key ==$rval['tid'] || $key ==$rval['pid']){
						
						if($key ==$rval['tid']){
							$postArrReplys[$rval['tid']][]=array_merge($postreplys[$key],$rval);
							continue;
						}else{
							$postArrReplys[$rval['pid']][]=array_merge($postreplys[$key],$rval);
						}
					}
				}
			}
		}
		
		foreach($postArrReplys as $Pkey=>$Pval)
		{
			$postArr[]=$Pval[0];
		}
		return $postArr;
	}
}