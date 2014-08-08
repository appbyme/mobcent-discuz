<?php
class forum {
	public function loadmobcentforum($tid, $fid) {
		global $_G;
		if (! empty ( $_GET ['archiver'] )) {  
			if ($fid) {
				dheader ( 'location: archiver/?fid-' . $fid . '.html' );
			} elseif ($tid) {
				dheader ( 'location: archiver/?tid-' . $tid . '.html' );
			} else {
				dheader ( 'location: archiver/' );
			}
		}
		if (defined ( 'IN_ARCHIVER' ) && $_G ['setting'] ['archiverredirect'] && ! IS_ROBOT) {
			dheader ( 'location: ../forum.php' . ($_G ['mod'] ? '?mod=' . $_G ['mod'] . (! empty ( $_GET ['fid'] ) ? '&fid=' . $_GET ['fid'] : (! empty ( $_GET ['tid'] ) ? '&tid=' . $_GET ['tid'] : '')) : '') );
		}
		if ($_G ['setting'] ['forumpicstyle']) {
			$_G ['setting'] ['forumpicstyle'] = dunserialize ( $_G ['setting'] ['forumpicstyle'] );
			empty ( $_G ['setting'] ['forumpicstyle'] ['thumbwidth'] ) && $_G ['setting'] ['forumpicstyle'] ['thumbwidth'] = 214;
			empty ( $_G ['setting'] ['forumpicstyle'] ['thumbheight'] ) && $_G ['setting'] ['forumpicstyle'] ['thumbheight'] = 160;
		} else {
			$_G ['setting'] ['forumpicstyle'] = array (
					'thumbwidth' => 214,
					'thumbheight' => 160
			);
		}
		if ($fid) {
			$fid = is_numeric ( $fid ) ? intval ( $fid ) : (! empty ( $_G ['setting'] ['forumfids'] [$fid] ) ? $_G ['setting'] ['forumfids'] [$fid] : 0);
		}
	
		$modthreadkey = isset ( $_GET ['modthreadkey'] ) && $_GET ['modthreadkey'] == modauthkey ( $tid ) ? $_GET ['modthreadkey'] : '';
		$_G ['forum_auditstatuson'] = $modthreadkey ? true : false;
	
		$metadescription = $hookscriptmessage = '';
		$adminid = $_G ['adminid'];
	
		if (! empty ( $tid ) || ! empty ( $fid )) {
				
			if (! empty ( $tid )) {
				$archiveid = ! empty ( $_GET ['archiveid'] ) ? intval ( $_GET ['archiveid'] ) : null;
				$_G ['thread'] = get_thread_by_tid ( $tid, $archiveid );
				if (! $_G ['forum_auditstatuson'] && ! empty ( $_G ['thread'] ) && ! ($_G ['thread'] ['displayorder'] >= 0 || (in_array ( $_G ['thread'] ['displayorder'], array (
						- 4,
						- 3,
						- 2
				) ) && $_G ['uid'] && $_G ['thread'] ['authorid'] == $_G ['uid']))) {
					$_G ['thread'] = null;
				}
	
				$_G ['forum_thread'] = & $_G ['thread'];
	
				if (empty ( $_G ['thread'] )) {
					$fid = $tid = 0;
				} else {
					$fid = $_G ['thread'] ['fid'];
					$tid = $_G ['thread'] ['tid'];
				}
			}
				
			if ($fid) {
				$forum = C::t ( 'forum_forum' )->fetch_info_by_fid ( $fid );
			}
				
			if ($forum) {
				if ($_G ['uid']) {
					if ($_G ['member'] ['accessmasks']) {
						$query = C::t ( 'forum_access' )->fetch_all_by_fid_uid ( $fid, $_G ['uid'] );
						$forum ['allowview'] = $query [0] ['allowview'];
						$forum ['allowpost'] = $query [0] ['allowpost'];
						$forum ['allowreply'] = $query [0] ['allowreply'];
						$forum ['allowgetattach'] = $query [0] ['allowgetattach'];
						$forum ['allowgetimage'] = $query [0] ['allowgetimage'];
						$forum ['allowpostattach'] = $query [0] ['allowpostattach'];
						$forum ['allowpostimage'] = $query [0] ['allowpostimage'];
					}
					if ($adminid == 3) {
						$forum ['ismoderator'] = C::t ( 'forum_moderator' )->fetch_uid_by_fid_uid ( $fid, $_G ['uid'] );
					}
				}
				$forum ['ismoderator'] = ! empty ( $forum ['ismoderator'] ) || $adminid == 1 || $adminid == 2 ? 1 : 0;
				$fid = $forum ['fid'];
				$gorup_admingroupids = $_G ['setting'] ['group_admingroupids'] ? dunserialize ( $_G ['setting'] ['group_admingroupids'] ) : array (
						'1' => '1'
				);
	
				if ($forum ['status'] == 3) {
					if (! empty ( $forum ['moderators'] )) {
						$forum ['moderators'] = dunserialize ( $forum ['moderators'] );
					} else {
						require_once libfile ( 'function/group' );
						$forum ['moderators'] = update_groupmoderators ( $fid );
					}
					if ($_G ['uid'] && $_G ['adminid'] != 1) {
						$forum ['ismoderator'] = ! empty ( $forum ['moderators'] [$_G ['uid']] ) ? 1 : 0;
						$_G ['adminid'] = 0;
						if ($forum ['ismoderator'] || $gorup_admingroupids [$_G ['groupid']]) {
							$_G ['adminid'] = $_G ['adminid'] ? $_G ['adminid'] : 3;
							if (! empty ( $gorup_admingroupids [$_G ['groupid']] )) {
								$forum ['ismoderator'] = 1;
								$_G ['adminid'] = 2;
							}
								
							$group_userperm = dunserialize ( $_G ['setting'] ['group_userperm'] );
							if (is_array ( $group_userperm )) {
								$_G ['group'] = array_merge ( $_G ['group'], $group_userperm );
								$_G ['group'] ['allowmovethread'] = $_G ['group'] ['allowcopythread'] = $_G ['group'] ['allowedittypethread'] = 0;
							}
						}
					}
				}
				foreach ( array (
						'threadtypes',
						'threadsorts',
						'creditspolicy',
						'modrecommend'
				) as $key ) {
					$forum [$key] = ! empty ( $forum [$key] ) ? dunserialize ( $forum [$key] ) : array ();
					if (! is_array ( $forum [$key] )) {
						$forum [$key] = array ();
					}
				}
	
				if ($forum ['status'] == 3) {
					$_G ['isgroupuser'] = 0;
					$_G ['basescript'] = 'group';
					if ($forum ['level'] == 0) {
						$levelinfo = C::t ( 'forum_grouplevel' )->fetch_by_credits ( $forum ['commoncredits'] );
						$levelid = $levelinfo ['levelid'];
						$forum ['level'] = $levelid;
						C::t ( 'forum_forum' )->update_group_level ( $levelid, $fid );
					}
					if ($forum ['level'] != - 1) {
						loadcache ( 'grouplevels' );
						$grouplevel = $_G ['grouplevels'] [$forum ['level']];
						if (! empty ( $grouplevel ['icon'] )) {
							$valueparse = parse_url ( $grouplevel ['icon'] );
							if (! isset ( $valueparse ['host'] )) {
								$grouplevel ['icon'] = $_G ['setting'] ['attachurl'] . 'common/' . $grouplevel ['icon'];
							}
						}
					}
						
					$group_postpolicy = $grouplevel ['postpolicy'];
					if (is_array ( $group_postpolicy )) {
						$forum = array_merge ( $forum, $group_postpolicy );
					}
					$forum ['allowfeed'] = $_G ['setting'] ['group_allowfeed'];
					if ($_G ['uid']) {
						if (! empty ( $forum ['moderators'] [$_G ['uid']] )) {
							$_G ['isgroupuser'] = 1;
						} else {
							$groupuserinfo = C::t ( 'forum_groupuser' )->fetch_userinfo ( $_G ['uid'], $fid );
							$_G ['isgroupuser'] = $groupuserinfo ['level'];
							if ($_G ['isgroupuser'] <= 0 && empty ( $forum ['ismoderator'] )) {
								$_G ['group'] ['allowrecommend'] = $_G ['cache'] ['usergroup_' . $_G ['groupid']] ['allowrecommend'] = 0;
								$_G ['group'] ['allowcommentpost'] = $_G ['cache'] ['usergroup_' . $_G ['groupid']] ['allowcommentpost'] = 0;
								$_G ['group'] ['allowcommentitem'] = $_G ['cache'] ['usergroup_' . $_G ['groupid']] ['allowcommentitem'] = 0;
								$_G ['group'] ['raterange'] = $_G ['cache'] ['usergroup_' . $_G ['groupid']] ['raterange'] = array ();
								$_G ['group'] ['allowvote'] = $_G ['cache'] ['usergroup_' . $_G ['groupid']] ['allowvote'] = 0;
							} else {
								$_G ['isgroupuser'] = 1;
							}
						}
					}
				}
			} else {
				$fid = 0;
			}
		}
	
		$_G ['fid'] = $fid;
		$_G ['tid'] = $tid;
		$_G ['forum'] = &$forum;
		$_G ['current_grouplevel'] = &$grouplevel;
	
		if (! empty ( $_G ['forum'] ['widthauto'] )) {
			$_G ['widthauto'] = $_G ['forum'] ['widthauto'];
		}
	}
	function viewthread_procpost($post, $lastvisit, $ordertype, $maxposition = 0) {
		global $_G, $rushreply;
		$_G['setting']['plugins']['func'][HOOKTYPE]['discuzcode'] =true;
		if(!$_G['forum_newpostanchor'] && $post['dateline'] > $lastvisit) {
			$post['newpostanchor'] = '<a name="newpost"></a>';
			$_G['forum_newpostanchor'] = 1;
		} else {
			$post['newpostanchor'] = '';
		}
	
		$post['lastpostanchor'] = ($ordertype != 1 && $_G['forum_numpost'] == $_G['forum_thread']['replies']) || ($ordertype == 1 && $_G['forum_numpost'] == $_G['forum_thread']['replies'] + 2) ? '<a name="lastpost"></a>' : '';
	
		if($_G['forum_pagebydesc']) {
			if($ordertype != 1) {
				$post['number'] = $_G['forum_numpost'] + $_G['forum_ppp2']--;
			} else {
				$post['number'] = $post['first'] == 1 ? 1 : ($_G['forum_numpost'] - 1) - $_G['forum_ppp2']--;
			}
		} else {
			if($ordertype != 1) {
				$post['number'] = ++$_G['forum_numpost'];
			} else {
				$post['number'] = $post['first'] == 1 ? 1 : --$_G['forum_numpost'];
				$post['number'] = $post['number'] - 1;
			}
		}
	
		if($maxposition) {
			$post['number'] = $post['position'];
		}
		$_G['forum_postcount']++;
	
		$post['dbdateline'] = $post['dateline'];
		$post['dateline'] = dgmdate($post['dateline'], 'u', '9999', getglobal('setting/dateformat').' H:i:s');
		$post['groupid'] = $_G['cache']['usergroups'][$post['groupid']] ? $post['groupid'] : 7;
	
		if($post['username']) {
	
			$_G['forum_onlineauthors'][$post['authorid']] = 0;
			$post['usernameenc'] = rawurlencode($post['username']);
			$post['readaccess'] = $_G['cache']['usergroups'][$post['groupid']]['readaccess'];
			if($_G['cache']['usergroups'][$post['groupid']]['userstatusby'] == 1) {
				$post['authortitle'] = $_G['cache']['usergroups'][$post['groupid']]['grouptitle'];
				$post['stars'] = $_G['cache']['usergroups'][$post['groupid']]['stars'];
			}
			$post['upgradecredit'] = false;
			if($_G['cache']['usergroups'][$post['groupid']]['type'] == 'member' && $_G['cache']['usergroups'][$post['groupid']]['creditslower'] != 999999999) {
				$post['upgradecredit'] = $_G['cache']['usergroups'][$post['groupid']]['creditslower'] - $post['credits'];
			}
	
			$post['taobaoas'] = addslashes($post['taobao']);
			$post['regdate'] = dgmdate($post['regdate'], 'd');
			$post['lastdate'] = dgmdate($post['lastvisit'], 'd');
	
			$post['authoras'] = !$post['anonymous'] ? ' '.addslashes($post['author']) : '';
	
			if($post['medals']) {
				loadcache('medals');
				foreach($post['medals'] = explode("\t", $post['medals']) as $key => $medalid) {
					list($medalid, $medalexpiration) = explode("|", $medalid);
					if(isset($_G['cache']['medals'][$medalid]) && (!$medalexpiration || $medalexpiration > TIMESTAMP)) {
						$post['medals'][$key] = $_G['cache']['medals'][$medalid];
						$post['medals'][$key]['medalid'] = $medalid;
						$_G['medal_list'][$medalid] = $_G['cache']['medals'][$medalid];
					} else {
						unset($post['medals'][$key]);
					}
				}
			}
	
			$post['avatar'] = avatar($post['authorid']);
			$post['groupicon'] = $post['avatar'] ? g_icon($post['groupid'], 1) : '';
			$post['banned'] = $post['status'] & 1;
			$post['warned'] = ($post['status'] & 2) >> 1;
	
		} else {
			if(!$post['authorid']) {
				$post['useip'] = substr($post['useip'], 0, strrpos($post['useip'], '.')).'.x';
			}
		}
		$post['attachments'] = array();
		$post['imagelist'] = $post['attachlist'] = '';
	
		if($post['attachment']) {
			if($_G['group']['allowgetattach'] || $_G['group']['allowgetimage']) {
				$_G['forum_attachpids'][] = $post['pid'];
				$post['attachment'] = 0;
				if(preg_match_all("/\[attach\](\d+)\[\/attach\]/i", $post['message'], $matchaids)) {
					$_G['forum_attachtags'][$post['pid']] = $matchaids[1];
				}
			} else {
				$post['message'] = preg_replace("/\[attach\](\d+)\[\/attach\]/i", '', $post['message']);
			}
		}
	
		if($_G['setting']['ratelogrecord'] && $post['ratetimes']) {
			$_G['forum_cachepid'][$post['pid']] = $post['pid'];
		}
		if($_G['setting']['commentnumber'] && ($post['first'] && $_G['setting']['commentfirstpost'] || !$post['first']) && $post['comment']) {
			$_G['forum_cachepid'][$post['pid']] = $post['pid'];
		}
		$post['allowcomment'] = $_G['setting']['commentnumber'] && in_array(1, $_G['setting']['allowpostcomment']) && ($_G['setting']['commentpostself'] || $post['authorid'] != $_G['uid']) &&
		($post['first'] && $_G['setting']['commentfirstpost'] && in_array($_G['group']['allowcommentpost'], array(1, 3)) ||
				(!$post['first'] && in_array($_G['group']['allowcommentpost'], array(2, 3))));
		$forum_allowbbcode = $_G['forum']['allowbbcode'] ? -$post['groupid'] : 0;
		$post['signature'] = $post['usesig'] ? ($_G['setting']['sigviewcond'] ? (strlen($post['message']) > $_G['setting']['sigviewcond'] ? $post['signature'] : '') : $post['signature']) : '';
		if(!defined('IN_ARCHIVER')) {
			$post['message'] = discuzcode($post['message'], $post['smileyoff'], $post['bbcodeoff'], $post['htmlon'] & 1, $_G['forum']['allowsmilies'], $forum_allowbbcode, ($_G['forum']['allowimgcode'] && $_G['setting']['showimages'] ? 1 : 0), $_G['forum']['allowhtml'], ($_G['forum']['jammer'] && $post['authorid'] != $_G['uid'] ? 1 : 0), 0, $post['authorid'], $_G['cache']['usergroups'][$post['groupid']]['allowmediacode'] && $_G['forum']['allowmediacode'], $post['pid'], $_G['setting']['lazyload'], $post['dbdateline']);
			if($post['first']) {
				$_G['relatedlinks'] = '';
				$relatedtype = !$_G['forum_thread']['isgroup'] ? 'forum' : 'group';
				if(!$_G['setting']['relatedlinkstatus']) {
					$_G['relatedlinks'] = get_related_link($relatedtype);
				} else {
					$post['message'] = parse_related_link($post['message'], $relatedtype);
				}
	
			}
		}
		$_G['forum_firstpid'] = intval($_G['forum_firstpid']);
		$post['custominfo'] = $this->viewthread_custominfo($post);
		$post['mobiletype'] = getstatus($post['status'], 4) ? base_convert(getstatus($post['status'], 10).getstatus($post['status'], 9).getstatus($post['status'], 8), 2, 10) : 0;
		return $post;
	}
	function viewthread_custominfo($post) {
		global $_G;
	
		$types = array('left', 'menu');
		foreach($types as $type) {
			if(!is_array($_G['cache']['custominfo']['setting'][$type])) {
				continue;
			}
			$data = '';
			foreach($_G['cache']['custominfo']['setting'][$type] as $key => $order) {
				$v = '';
				if(substr($key, 0, 10) == 'extcredits') {
					$i = substr($key, 10);
					$extcredit = $_G['setting']['extcredits'][$i];
					$v = '<dt>'.($extcredit['img'] ? $extcredit['img'].' ' : '').$extcredit['title'].'</dt><dd>'.$post['extcredits'.$i].' '.$extcredit['unit'].'</dd>';
				} elseif(substr($key, 0, 6) == 'field_') {
					$field = substr($key, 6);
					if(!empty($post['privacy']['profile'][$field])) {
						continue;
					}
					require_once libfile('function/profile');
					$v = profile_show($field, $post);
					if($v) {
						$v = '<dt>'.$_G['cache']['custominfo']['profile'][$key][0].'</dt><dd title="'.dhtmlspecialchars(strip_tags($v)).'">'.$v.'</dd>';
					}
				} elseif($key == 'creditinfo') {
					$v = '<dt>'.lang('space', 'viewthread_userinfo_buyercredit').'</dt><dd><a href="home.php?mod=space&uid='.$post['uid'].'&do=trade&view=eccredit#buyercredit" target="_blank" class="vm"><img src="'.STATICURL.'image/traderank/seller/'.countlevel($post['buyercredit']).'.gif" /></a></dd>';
					$v .= '<dt>'.lang('space', 'viewthread_userinfo_sellercredit').'</dt><dd><a href="home.php?mod=space&uid='.$post['uid'].'&do=trade&view=eccredit#sellercredit" target="_blank" class="vm"><img src="'.STATICURL.'image/traderank/seller/'.countlevel($post['sellercredit']).'.gif" /></a></dd>';
				} else {
					switch($key) {
						case 'uid': $v = $post['uid'];break;
						case 'posts': $v = '<a href="home.php?mod=space&uid='.$post['uid'].'&do=thread&type=reply&view=me&from=space" target="_blank" class="xi2">'.$post['posts'].'</a>';break;
						case 'threads': $v = '<a href="home.php?mod=space&uid='.$post['uid'].'&do=thread&type=thread&view=me&from=space" target="_blank" class="xi2">'.$post['threads'].'</a>';break;
						case 'doings': $v = '<a href="home.php?mod=space&uid='.$post['uid'].'&do=doing&view=me&from=space" target="_blank" class="xi2">'.$post['doings'].'</a>';break;
						case 'blogs': $v = '<a href="home.php?mod=space&uid='.$post['uid'].'&do=blog&view=me&from=space" target="_blank" class="xi2">'.$post['blogs'].'</a>';break;
						case 'albums': $v = '<a href="home.php?mod=space&uid='.$post['uid'].'&do=album&view=me&from=space" target="_blank" class="xi2">'.$post['albums'].'</a>';break;
						case 'sharings': $v = '<a href="home.php?mod=space&uid='.$post['uid'].'&do=share&view=me&from=space" target="_blank" class="xi2">'.$post['sharings'].'</a>';break;
						case 'friends': $v = '<a href="home.php?mod=space&uid='.$post['uid'].'&do=friend&view=me&from=space" target="_blank" class="xi2">'.$post['friends'].'</a>';break;
						case 'follower': $v = '<a href="home.php?mod=follow&do=follower&uid='.$post['uid'].'" target="_blank" class="xi2">'.$post['follower'].'</a>';break;
						case 'following': $v = '<a href="home.php?mod=follow&do=following&uid='.$post['uid'].'" target="_blank" class="xi2">'.$post['following'].'</a>';break;
						case 'digest': $v = $post['digestposts'];break;
						case 'credits': $v = $post['credits'];break;
						case 'readperm': $v = $post['readaccess'];break;
						case 'regtime': $v = $post['regdate'];break;
						case 'lastdate': $v = $post['lastdate'];break;
						case 'oltime': $v = $post['oltime'].' '.lang('space', 'viewthread_userinfo_hour');break;
					}
					if($v !== '') {
						$v = '<dt>'.lang('space', 'viewthread_userinfo_'.$key).'</dt><dd>'.$v.'</dd>';
					}
				}
				$data .= $v;
			}
			$return[$type] = $data;
		}
		return $return;
	}
	function getrelateitem($tagarray, $tid, $relatenum, $relatetime, $relatecache = '', $type = 'tid') {
		$tagidarray = $relatearray = $relateitem = array();
		$updatecache = 0;
		$limit = $relatenum;
		if(!$limit) {
			return '';
		}
		foreach($tagarray as $var) {
			$tagidarray[] = $var['0'];
		}
		if(!$tagidarray) {
			return '';
		}
		if(empty($relatecache)) {
			$thread = C::t('forum_thread')->fetch($tid);
			$relatecache = $thread['relatebytag'];
		}
		if($relatecache) {
			$relatecache = explode("\t", $relatecache);
			if(TIMESTAMP > $relatecache[0] + $relatetime * 60) {
				$updatecache = 1;
			} else {
				if(!empty($relatecache[1])) {
					$relatearray = explode(',', $relatecache[1]);
				}
			}
		} else {
			$updatecache = 1;
		}
		if($updatecache) {
			$query = C::t('common_tagitem')->select($tagidarray, $tid, $type, '', '', $limit, 0, '<>');
			foreach($query as $result) {
				if($result['itemid']) {
					$relatearray[] = $result['itemid'];
				}
			}
			if($relatearray) {
				$relatebytag = implode(',', $relatearray);
			}
			C::t('forum_thread')->update($tid, array('relatebytag'=>TIMESTAMP."\t".$relatebytag));
		}
	
	
		if(!empty($relatearray)) {
			foreach(C::t('forum_thread')->fetch_all_by_tid($relatearray) as $result) {
				if($result['displayorder'] >= 0) {
					$relateitem[] = $result;
				}
			}
		}
		return $relateitem;
	}
	
}

?>