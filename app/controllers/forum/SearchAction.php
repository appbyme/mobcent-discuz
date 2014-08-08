<?php

/**
 * 搜索接口
 *
 * @author HanPengyu
 * @param $keyword string 搜索关键字
 * @param $page int 当前页数
 * @param $pageSize int 每页显示页数
 * @param $searchid int 搜索缓存id
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class SearchAction extends MobcentAction {

    public function run($keyword, $page = 1, $pageSize = 10, $searchid = 0) {
        $keyword = rawurldecode($keyword);
        $res = WebUtils::initWebApiArray_oldVersion();
        $res = $this->_getForumData($res, $keyword, $page, $pageSize, $searchid);
        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _getForumData($res, $kw, $page, $pagesize, $searchid) {
        global $_G;
        //判断系统是否开启搜索的功能
        if (!$_G['setting']['search']['forum']['status']) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'search_forum_closed');
        }
        //判断当前的用户是否有搜索的权限
        if (!$_G['adminid'] && !($_G['group']['allowsearch'] & 2)) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'group_nopermission', array('{grouptitle}' => $_G['group']['grouptitle']));
        }
        if (trim($kw) == '') {
            return WebUtils::makeErrorInfo_oldVersion($res, 'faq_keywords_empty');
        }
        //纵横搜索数据
        $searchHelper = Cloud::loadClass('Service_SearchHelper');
        $searchparams = $searchHelper->makeSearchSignUrl();
        $mySearchData = $_G['setting']['my_search_data'];
        if ($mySearchData['status'] && $searchparams) {
            return $this->_zhSearchData($kw, $page, $pagesize, $res, $searchparams);
        }
        //普通搜索数据
        return  $this->_searchData($kw, $page, $pagesize, $res, $searchid);
    }

    //普通搜索取出数据
    private function _searchData($kw, $page, $pagesize, $res, $searchid) {
        global $_G;
        $srchtype='title'; 
        $orderby = 'lastpost';
        $ascdesc = 'desc';
        $srchtxt = $kw;
        $keyword = WebUtils::t(dhtmlspecialchars(trim($kw)));
        $_G['setting']['search']['forum']['searchctrl'] = intval($_G['setting']['search']['forum']['searchctrl']);

        require_once libfile('function/forumlist');
        require_once libfile('function/forum');
        require_once libfile('function/search'); 
        require_once libfile('function/misc'); 
        require_once libfile('function/post'); 

        loadcache(array('forums', 'posttable_info'));
        $srchmod = 2;
        $cachelife_time = 300;    
        $cachelife_text = 3600;
        $seltableid = 0;

        if (empty($searchid)) {

            //searchid 为空的时候就要通过拼接一个字符串来进行查找搜索缓存表了
            if ($_G['group']['allowsearch'] & 32 && $srchtype == 'fulltext') { //全文搜索
                //时间段设置检测
                periodscheck('searchban0periods');
            } elseif ($srchtype != 'title') {
                $srchtype = 'title';
            }
            $forumsarray = array();
            if (!empty($srchfid)) {
                foreach((is_array($srchfid) ? $srchfid : explode('_', $srchfid)) as $forum) {
                    if ($forum = intval(trim($forum))) {
                            $forumsarray[] = $forum;
                    }
                }
            }
            //取出板块的fid
            $fids = $comma = '';
            foreach ($_G['cache']['forums'] as $fid => $forum) {
                if ($forum['type'] != 'group' && (!$forum['viewperm'] && $_G['group']['readaccess']) || ($forum['viewperm'] && forumperm($forum['viewperm']))) {
                        if (!$forumsarray || in_array($fid, $forumsarray)) {
                            $fids .= "$comma'$fid'";
                            $comma = ',';
                        }
                }
            }  

            if ($_G['setting']['threadplugins'] && $specialplugin) {
                $specialpluginstr = implode("','", $specialplugin);
                $special[] = 127;
            } else {
                $specialpluginstr = '';
            }

            $special = '';    
            $specials = $special ? implode(',', $special) : '';
            $srchfilter = 'all';    //所有板块

            //搜索缓存表 查找字符串
            $srchuid = $srchuname = $srchfrom = $before = '';
            $searchstring = 'forum|'.$srchtype.'|'.base64_encode($srchtxt).'|'.intval($srchuid).'|'.$srchuname.'|'.addslashes($fids).'|'.intval($srchfrom).'|'.intval($before).'|'.$srchfilter.'|'.$specials.'|'.$specialpluginstr.'|'.$se0;

            $searchindex = array('id' => 0, 'dateline' => '0');

            foreach (C::t('common_searchindex')->fetch_all_search($_G['setting']['search']['forum']['searchctrl'], $_G['clientip'], $_G['uid'], $_G['timestamp'], $searchstring, $srchmod) as $index) {

                if ($index['indexvalid'] && $index['dateline'] > $searchindex['dateline']) {
                    $searchindex = array('id' => $index['searchid'], 'dateline' => $index['dateline']);
                    break;
                } elseif ($_G['adminid'] != '1' && $index['flood']) {
                    //抱歉，您在 秒内只能进行一次搜索
                    return WebUtils::makeErrorInfo_oldVersion($res, 'search_ctrl',array('searchctrl' => $_G['setting']['search']['forum']['searchctrl']));
                }
            }

            if ($searchindex['id']) {
                $searchid = $searchindex['id'];
            } else {

                if ($_G['adminid'] != '1' && $_G['setting']['search']['forum']['maxspm']) {
                    if (C::t('common_searchindex')->count_by_dateline($_G['timestamp'], $srchmod) >= $_G['setting']['search']['forum']['maxspm']) {
                        //抱歉，站点设置每分钟系统最多响应搜索请求 {maxspm} 次，请稍候再试
                        return WebUtils::makeErrorInfo_oldVersion($res, 'search_toomany',array('maxspm' => $_G['setting']['search']['forum']['maxspm']));
                    }
                }

                $digestltd = $srchfilter == 'digest' ? "t.digest>'0' AND" : '';
                $topltd = $srchfilter == 'top' ? "AND t.displayorder>'0'" : "AND t.displayorder>='0'";
                $sqlsrch = $srchtype == 'fulltext' ?
                "FROM ".DB::table(getposttable($seltableid))." p, ".DB::table('forum_thread')." t WHERE $digestltd t.fid IN ($fids) $topltd AND p.tid=t.tid AND p.invisible='0'" :
                        "FROM ".DB::table('forum_thread')." t WHERE $digestltd t.fid IN ($fids) $topltd";
                if ($srchtxt) {
                    $srcharr = $srchtype == 'fulltext' ? searchkey($keyword, "(p.message LIKE '%{text}%' OR p.subject LIKE '%{text}%')", true) : searchkey($keyword,"t.subject LIKE '%{text}%'", true);
                    $srchtxt = $srcharr[0];
                    $sqlsrch .= $srcharr[1];
                }
                $keywords = str_replace('%', '+', $srchtxt);
                $expiration = TIMESTAMP + $cachelife_text;
                $num = $ids = 0;
                $_G['setting']['search']['forum']['maxsearchresults'] = $_G['setting']['search']['forum']['maxsearchresults'] ? intval($_G['setting']['search']['forum']['maxsearchresults']) : 500;
                $query = DB::query("SELECT ".($srchtype == 'fulltext' ? 'DISTINCT' : '')." t.tid, t.closed, t.author, t.authorid $sqlsrch ORDER BY tid DESC LIMIT ".$_G['setting']['search']['forum']['maxsearchresults']);
                while ($thread = DB::fetch($query)) {
                    $ids .= ','.$thread['tid'];
                    $num++;
                }
                DB::free_result($query);

                $idsArr = explode(',', $ids);
                $idCount = count($idsArr);
                if ($idCount == 1) {
                     return WebUtils::makeErrorInfo_oldVersion($res, 'search_no_results');
                }

                $searchid = C::t('common_searchindex')->insert(array(
                    'srchmod' => $srchmod,
                    'keywords' => $keywords,
                    'searchstring' => $searchstring,
                    'useip' => $_G['clientip'],
                    'uid' => $_G['uid'],
                    'dateline' => $_G['timestamp'],
                    'expiration' => $expiration,
                    'num' => $num,
                    'ids' => $ids
                ), true);
                !($_G['group']['exempt'] & 2) && updatecreditbyaction('search');
            }

            
        }

        //这个分支要把数据取出来,并且返回正确的数据结构
        $start_limit = ($page - 1) * $pagesize;
        $index = C::t('common_searchindex')->fetch_by_searchid_srchmod($searchid, $srchmod);
        if(!$index) {
            //showmessage('search_id_invalid');//指定的搜索不存在或已过期
            return WebUtils::makeErrorInfo_oldVersion($res, 'search_id_invalid');
        }
        $keyword = dhtmlspecialchars($index['keywords']);
        $keyword = $keyword != '' ? str_replace('+', ' ', $keyword) : '';
        $index['keywords'] = rawurlencode($index['keywords']);
        $searchstring = explode('|', $index['searchstring']);
        $index['searchtype'] = $searchstring[0];
        $searchstring[2] = base64_decode($searchstring[2]);
        $srchuname = $searchstring[3];
        $modfid = 0;
        if($keyword) {
            $modkeyword = str_replace(' ', ',', $keyword);
            $fids = explode(',', str_replace('\'', '', $searchstring[5]));
            if(count($fids) == 1 && in_array($_G['adminid'], array(1,2,3))) {
                $modfid = $fids[0];
                if($_G['adminid'] == 3 && !C::t('forum_moderator')->fetch_uid_by_fid_uid($modfid, $_G['uid'])) {
                    $modfid = 0;
                }
            }
        }
         
        $threadlist = $posttables = array();
        foreach (C::t('forum_thread')->fetch_all_by_tid_fid_displayorder(explode(',', $index['ids']), null, 0, $orderby, $start_limit, $pagesize, '>=', $ascdesc, 0) as $thread) {
            $thread['subject'] = bat_highlight($thread['subject'], $keyword);
            $thread['realtid'] = $thread['isgroup'] == 1 ? $thread['closed'] : $thread['tid'];
            $tempLastPost = $thread['lastpost'];
            $tempDateLine = $thread['dateline'];
            $threadlist[$thread['tid']] = procthread($thread, 'dt');
            $threadlist[$thread['tid']]['lastpost'] = $tempLastPost;
            $threadlist[$thread['tid']]['dateline'] = $tempDateLine;
            $posttables[$thread['posttableid']][] = $thread['tid'];
        }
        if ($threadlist) {
            foreach ($posttables as $tableid => $tids) {
                foreach (C::t('forum_post')->fetch_all_by_tid($tableid, $tids, true, '', 0, 0, 1) as $post) {
                    $threadlist[$post['tid']]['message'] = bat_highlight(messagecutstr($post['message'], 200), $keyword);
                }
            }
        }

        $pageInfo = $row = $rows = array();
        $rows = $this->_fieldInfo($threadlist);            
        $pageInfo = WebUtils::getWebApiArrayWithPage_oldVersion($page, $pagesize, $index['num']);
        $res = array_merge($res, $pageInfo);
        $res['searchid'] = (int)$searchid;
        $res['list'] = $rows;
        return $res;

    }

    //纵横搜索取出数据
    private function _zhSearchData($kw, $page, $pagesize, $res, $searchparams){

        $keyword = dhtmlspecialchars(trim($kw));
        $keyword = WebUtils::t($keyword);

        require_once libfile('function/search');
        require_once libfile('function/misc');
        require_once libfile('function/post');

        // $searchHelper = Cloud::loadClass('Service_SearchHelper');
        // $searchparams = $searchHelper->makeSearchSignUrl();

        $appService = Cloud::loadClass('Service_App');
        if ($appService->getCloudAppStatus('search') && $searchparams) {
            $source = 'discuz';
            $params = array();
            $params['source'] = $source;
            $params['q'] = $keyword;
            $params['module'] = 'forum';
            $searchparams['params'] = array_merge($searchparams['params'], $params);
            $utilService = Cloud::loadClass('Service_Util');
            $url = $searchparams['url'] . '?' . $utilService->httpBuildQuery($searchparams['params'], '', '&');
        }

        $url = $url.'&page='.$page;
        // $pageInfo = WebUtils::emptyReturnLine(file_get_contents($url));
        $pageInfo = WebUtils::emptyReturnLine(WebUtils::httpRequest($url));

        $pregIds = '/&tid=(\d*)"/';
        $pregCount = '/<div class="allnum">(.*?)<\/div>/';  //取出搜索结果总数

        preg_match_all($pregIds, $pageInfo, $id);
        $ids = $id[1];
        if (empty($ids)) {
            return WebUtils::makeErrorInfo_oldVersion($res, 'search_no_results');
        }
        preg_match_all($pregCount, $pageInfo, $count);
        $countStr = trim($count[1][0]);
        preg_match_all('/\d/', $countStr, $total);

        $total_num = (int)str_replace(',', '', implode(',', $total[0]));
        $ids = $id[1];

        $data = $topicInfo = $topicSummary = array();
        foreach ($ids as $v) {
            $topicInfo = ForumUtils::getTopicInfo($v);
            // $topicInfo['lastpost'] = dgmdate($topicInfo['lastpost'], 'u');
            $topicInfos[] = $topicInfo;
        }
        unset($ids);

        $rows = $this->_fieldInfo($topicInfos);
        $pageInfo = WebUtils::getWebApiArrayWithPage_oldVersion($page, 10, $total_num);
        $res['searchid'] = 0;
        $res = array_merge($res, $pageInfo);
        $res['list'] = $rows;
        return $res;
    }

    private function _fieldInfo($topics) {
        $row = $rows = array();
        foreach ($topics as $v) {
            $topicSummary = ForumUtils::getTopicSummary($v['tid']);
            $row['board_id'] = (int)$v['fid'];
            $row['topic_id'] = (int)$v['tid'];
            $row['type_id'] = (int)$v['typeid'];
            $row['sort_id'] = (int)$v['sortid'];
            $row['vote'] = ForumUtils::isVoteTopic($v['tid']) ? 1 : 0;
            $row['title'] = (string)WebUtils::emptyHtml($v['subject']);
            $row['subject'] = (string)WebUtils::emptyReturnLine($topicSummary['msg']);
            $row['user_id'] = (int)$v['authorid'];
            $row['last_reply_date'] = $v['lastpost']."000";
            if ($row['last_reply_date'] == '000') {
                $row['last_reply_date'] = $v['dateline']."000";
            }
            $row['user_nick_name'] = (string)$v['author'];
            $row['hits'] = (int)$v['views'];
            $row['replies'] = (int)$v['replies'];
            $row['top'] = ForumUtils::isTopTopic($v['tid']) ? 1 : 0;
            $row['status'] = (int)$v['status'];
            $row['essence'] = (int)$v['digest'];
            $row['hot'] = ForumUtils::isHotTopic($v['tid']) ? 1 : 0;
            $row['pic_path'] = ImageUtils::getThumbImage($topicSummary['image']);
            $rows[] = $row;
        }
        return $rows;
    }


}
