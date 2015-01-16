<?php

/**
 * 用户收藏帖子、版块,取消收藏接口
 *
 * @author 徐少伟 <xushaowei@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class UserFavoriteAction extends MobcentAction {

    public function run($action='favorite', $id=0, $idType='tid') {
        $res = $res = $this->initWebApiArray();
        $res = $this->_userSetFavoriteType($res, $action, $id, $idType);

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _userSetFavoriteType($res, $action, $id, $idType) {
        switch ($action) {
            case 'favorite':$res = $this->_userFavoriteSetting($res, $id, $idType); break;
            case 'delfavorite':$res = $this->_userUnFollowSetting($res, $id, $idType); break;
            default:
                break;
        }
        return $res;
    }

    private function _userFavoriteSetting($res, $id, $idType) {
        global $_G;
        $uid = $_G['uid'];

        if (($checkMessage = mobcent_cknewuser()) != '') {
            return $this->makeErrorInfo($res, WebUtils::emptyHtml($checkMessage));
        }
        
        $spaceuid = empty($_GET['spaceuid']) ? 0 : intval($_GET['spaceuid']);
        $title = '';

        switch($idType) {
            case 'tid':
                $idType = 'tid';
                $thread = C::t('forum_thread')->fetch($id);
                $title = $thread['subject'];
                break;
            case 'fid':
                $idType = 'fid';
                $foruminfo = C::t('forum_forum')->fetch($id);
                loadcache('forums');
                $forum = $_G['cache']['forums'][$id];
                if(!$forum['viewperm'] || ($forum['viewperm'] && forumperm($forum['viewperm'])) || strstr($forum['users'], "\t$_G[uid]\t")) {
                    $title = $foruminfo['status'] != 3 ? $foruminfo['name'] : '';
                    // $icon = '<img src="static/image/feed/discuz.gif" alt="forum" class="vm" /> ';
                }
                break;
            case 'blog':
                $idType = 'blogid';
                $bloginfo = C::t('home_blog')->fetch($id);
                $title = ($bloginfo['uid'] == $spaceuid) ? $bloginfo['subject'] : '';
                break;
            case 'group':
                $idType = 'gid';
                $foruminfo = C::t('forum_forum')->fetch($id);
                $title = $foruminfo['status'] == 3 ? $foruminfo['name'] : '';
                break;
            case 'album':
                $idType = 'albumid';
                $result = C::t('home_album')->fetch($id, $spaceuid);
                $title = $result['albumname'];
                break;
            case 'space':
                $idType = 'uid';
                $_member = getuserbyuid($id);
                $title = $_member['username'];
                $unset($_member);
                break;
            case 'article':
                $idType = 'aid';
                $article = C::t('portal_article_title')->fetch($id);
                $title = $article['title'];
                break;
            default:
                break;
        }

        if(empty($title)) {
            return $this->makeErrorInfo($res, 'favorite_cannot_favorite');
        } else {
            $fav = C::t('home_favorite')->fetch_by_id_idtype($id, $idType, $uid);
            if($fav) {
                $res = $this->makeErrorInfo($res, 'favorite_repeat');
            } else {
                $description = '';
                $description_show = nl2br($description);

                $fav_count = C::t('home_favorite')->count_by_id_idtype($id, $idType);
                require_once libfile('function/home');
                $description = WebUtils::t('用手机客户端收藏');
                $arr = array(
                    'uid' => intval($uid),
                    'idtype' => $idType,
                    'id' => $id,
                    'spaceuid' => $thread['authorid'],
                    'title' => getstr($title, 255),
                    'description' => getstr($description, '', 0, 0, 1),
                    'dateline' => TIMESTAMP
                );
                $favid = C::t('home_favorite')->insert($arr, true);
                if($_G['setting']['cloud_status']) {
                    $favoriteService = Cloud::loadClass('Service_Client_Favorite');
                    $favoriteService->add($arr['uid'], $favid, $arr['id'], $arr['idtype'], $arr['title'], $arr['description'], TIMESTAMP);
                }

                switch($idType) {
                    case 'tid':
                        C::t('forum_thread')->increase($id, array('favtimes'=>1));
                        require_once libfile('function/forum');
                        update_threadpartake($id);
                        break;
                    case 'fid': 
                        C::t('forum_forum')->update_forum_counter($id, 0, 0, 0, 0, 1);
                        // $extrajs = '<script type="text/javascript">$("number_favorite_num").innerHTML = parseInt($("number_favorite_num").innerHTML)+1;$("number_favorite").style.display="";</script>';
                        dsetcookie('nofavfid', '', -1);
                        break;
                    case 'blog':C::t('home_blog')->increase($id, $spaceuid, array('favtimes' => 1)); break;
                    case 'group':C::t('forum_forum')->update_forum_counter($id, 0, 0, 0, 0, 1);break;
                    case 'album':C::t('home_album')->update_num_by_albumid($id, 1, 'favtimes', $spaceuid);break;
                    case 'space':C::t('common_member_status')->increase($id, array('favtimes' => 1));break;
                    case 'article':C::t('portal_article_count')->increase($id, array('favtimes' => 1));break;
                    default:
                    break;
                }
                    $params['noError'] = 1;
                    $res = $this->makeErrorInfo($res, 'favorite_do_success', $params);
            }
        }
        return $res;
    }

    private function _userUnFollowSetting($res, $topicId, $idType) {
        global $_G;
        $uid = $_G['uid'];
        switch ($idType) {
            case 'forum':$idType = 'fid';break;
            default:
            break;
        }

        $setType = 'one';
        if($setType == 'all') {
            if($_GET['favorite']) {
                C::t('home_favorite')->delete($_GET['favorite'], false, $_G['uid']);
                if($_G['setting']['cloud_status']) {
                    $favoriteService = Cloud::loadClass('Service_Client_Favorite');
                    $favoriteService->remove($_G['uid'], $_GET['favorite'], TIMESTAMP);
                }
            }
            $res = $this->makeErrorInfo($res, 'favorite_delete_succeed');
        } else {
            $favid = DzUserFavoriteSet::getUserFavoriteSet($uid, $topicId, $idType);
            $thevalue = C::t('home_favorite')->fetch($favid);
            if(empty($thevalue) || $thevalue['uid'] != $_G['uid']) {
                $res = $this->makeErrorInfo($res, 'favorite_does_not_exist');
            } else {
                switch($thevalue['idtype']) {
                case 'fid':
                    C::t('forum_forum')->update_forum_counter($thevalue['id'], 0, 0, 0, 0, -1);
                    break;
                default:
                    break;
                }
                C::t('home_favorite')->delete($favid);
                if($_G['setting']['cloud_status']) {
                    $favoriteService = Cloud::loadClass('Service_Client_Favorite');
                    $favoriteService->remove($uid, $favid);
                }
                $params['noError'] = 1;
                $res = $this->makeErrorInfo($res, 'do_success', $params);
            }
        }
        return $res;
    }
}

class DzUserFavoriteSet extends DiscuzAR {

    public static function getUserFavoriteSet($uid, $topicId, $idType) {
        return DbUtils::getDzDbUtils(true)->queryScalar('
            SELECT favid 
            FROM %t 
            WHERE uid = %d 
            AND id = %d
            AND idtype = %s
            ',
            array('home_favorite', $uid, $topicId, $idType)
        );
    }
}
?>