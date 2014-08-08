<?php

/**
 * 论坛相关工具类
 *
 * @author 谢建平 <xiejianping@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class ForumUtils {

    /**
     * 获取通告列表
     *
     * @return array
     */
    public static function getAnnouncementList() {
        return DzForumAnnouncement::getAnnouncements();
    }

    /**
     * 版块相关
     */

    /**
     * 获取客户端能显示的fids
     */
    public static function getForumShowFids() {
        return self::_getForumFids('forum_show');
    }

    /**
     * 获取客户端能显示版块图片的fids
     */
    public static function getForumImageShowFids() {
        return self::_getForumFids('forum_show_image');
    }

    /**
     * 获取客户端图库能显示的fids
     */
    public static function getForumPhotoGalleryShowFids() {
        return self::_getForumFids('forum_photo_show');
    }

    private static function _getForumFids($key) {
        $fids = array();
        $config = WebUtils::getDzPluginAppbymeAppConfig($key);
        if ($config && ($config = unserialize($config))) {
            $config[0] != '' && $fids = $config;
        } else {
            $fids = DzForumForum::getFids();
        }
        return $fids;
    }

    public static function getForumName($fid) {
        return DzForumForum::getNameByFid($fid);
    }

    /**
     * 获取版块信息
     *
     * @param string $fids 版块信息列表 空或者0则为全部版块
     * @return array
     */
    public static function getForumInfos($fids = '') {
        if ($fids == '' || $fids == '0') {
            $fids = ForumUtils::getForumShowFids();
        } else {
            $fids = ArrayUtils::explode($fids);
        }
        return DzForumForum::getForumInfos($fids);
    }

    public static function getForumGroupList() {
        return DzForumForum::getForumGroups();
    }

    public static function getForumList($gid) {
        return DzForumForum::getForumsByGid($gid);
    }

    /**
     * 获取子版块列表
     *
     * @param int $fid 版块id
     * @return array
     */
    public static function getForumSubList($fid) {
        return DzForumForum::getSubForumsByFid($fid);
    }

    /**
     * 主题相关
     */

    /**
     * 初始化加载版块
     */
    public static function initForum($fid, $tid=0) {
        $app = Yii::app()->getController()->mobcentDiscuzApp;
        $app->loadForum($fid, $tid);
    }

    /**
     * 获取发帖面板
     */
    public static function getNewTopicPanel($fid=0) {
        $fid > 0 && ForumUtils::initForum($fid);

        $panel = array();
        
        global $_G;

        if (($_G['forum']['simple'] & 1) || $_G['forum']['redirect']) {
            return $panel;
        }

        $_G['group']['allowpostpoll'] = $_G['group']['allowpost'] && $_G['group']['allowpostpoll'] && ($_G['forum']['allowpostspecial'] & 1);

        if (!$_G['forum']['threadsorts']['required'] && !$_G['forum']['allowspecialonly']) {
            $panel[] = array(
                'type' => 'normal',
                'action' => '',
                'title' => WebUtils::t('发表帖子'),
            );
        }
        if (is_array($_G['forum']['threadsorts']['types'])) {
            foreach($_G['forum']['threadsorts']['types'] as $tsortid => $name) {
                $panel[] = array(
                    'type' => 'sort',
                    'actionId' => $tsortid,
                    'action' => '',
                    'title' => WebUtils::emptyHtml($name),
                );
            }
        }
        if ($_G['group']['allowpostpoll']) {
            $panel[] = array(
                'type' => 'vote',
                'action' => '',
                'title' => WebUtils::t('发起投票'),
            );
        }
        
        return $panel;
    }

    /**
     * 获取主题分类，分类信息
     */
    public static function getTopicClassificationInfos($fid=0) {
        $fid > 0 && ForumUtils::initForum($fid);
        
        $infos = array('types' => array(), 'sorts' => array(), 'requireTypes' => false);

        // $field = DbUtils::getDzDbUtils(true)->queryRow('
        //     SELECT threadtypes, threadsorts
        //     FROM %t
        //     WHERE fid=%d
        //     ',
        //     array('forum_forumfield', $fid)
        // );
        global $_G;
        $field = $_G['forum'];
        if (!empty($field)) {
            // $types = unserialize($field['threadtypes']);
            $types = $field['threadtypes'];
            if (!empty($types['types'])) {
                $infos['requireTypes'] = $types['required'];
                foreach ($types['types'] as $key => $value) {
                    $infos['types'][] = array(
                        'classificationType_id' => $key,
                        'classificationType_name' => WebUtils::emptyHtml($value),
                    );
                }
            }
            // $sorts = unserialize($field['threadsorts']);
            $sorts = $field['threadsorts'];
            if (!empty($sorts['types'])) {
                foreach ($sorts['types'] as $key => $value) {
                    $infos['sorts'][] = array(
                        'classificationTop_id' => $key,
                        'classificationTop_name' => WebUtils::emptyHtml($value),
                    );
                }
            }
        }

        return $infos;
    }

    /**
     * 取得主题个数
     *
     * @param int $fid 版块id,0则代表全部版块
     * @param array $params
     * @return array
     */
    public static function getTopicCount($fid=0, $params=array()) {
        return DzForumThread::getCountByFid($fid, $params);
    }

    /**
     * 获取帖子列表
     *
     * @param int   $fid      版块id,0则代表全部版块
     * @param int   $page     Description.
     * @param int   $pageSize Description.
     * @param array $params   Description.
     *
     * @return array.
     */
    public static function getTopicList($fid=0, $page=1, $pageSize=10, $params=array()) {
        return DzForumThread::getTopicsByFid($fid, $page, $pageSize, $params);
    }

    public static function getTopicInfo($tid) {
        return DzForumThread::getTopicByTid($tid);
    }

    /**
     * 获取主题类型
     *
     * @param int|array $tid int为主题id, array为主题info
     *
     * @return array
     */
    public static function getTopicType($tid) {
        return DzForumThread::getTopicTypeByTid($tid);
    }

    /**
     * 获取主题摘要(内容摘要以及图片)
     *
     * @param int $tid 帖子id
     * @param string $type forum为论坛模块，portal为门户模块
     * @param bool $transBr 是否要转换换行
     * @return array array('msg' => '', 'image' => '')
     */
    public static function getTopicSummary($tid, $type='forum', $transBr=true) {
        $summary = array('msg' => '', 'image' => '');

        $summaryLength = WebUtils::getDzPluginAppbymeAppConfig($type == 'forum' ? 'forum_summary_length' : 'portal_summary_length');
        $allowImage = WebUtils::getDzPluginAppbymeAppConfig($type == 'forum' ? 'forum_allow_image' : 'portal_allow_image');
        $allowImage = !($allowImage === '0');
        if ($summaryLength === '0' && !$allowImage) {
            return $summary;
        }

        $content = self::getTopicContent($tid);
        if (!empty($content['main'])) {
            $isFindImage = false;
            $msg = '';
            foreach ($content['main'] as $line) {
                if (!$isFindImage && $line['type'] == 'image') {
                    $allowImage && $summary['image'] = $line['content'];
                    $isFindImage = true;
                }
                if ($line['type'] == 'text') {
                    $msg .= $line['content'] . "\r\n";
                }
            }
            $msg = preg_replace('/\[mobcent_phiz=.+?\]/', '', $msg);
            $msg = preg_replace(WebUtils::t('/本帖最后由 .*? 于 .*? 编辑/'), '', $msg);
            $transBr && $msg = WebUtils::emptyReturnLine($msg, ' ');
            $msg = trim($msg);
            $summaryLength === false && $summaryLength = 40;
            $summary['msg'] = (string)WebUtils::subString($msg, 0, $summaryLength);
        }
        return $summary;
    }

    /**
     * 获取主题封面
     * 
     * @param int $tid
     */
    public static function getTopicCover($tid) {
        $image = '';
        $topicImage = DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE tid=%d
            ',
            array('forum_threadimage', $tid)
        );
        if (!empty($topicImage)) {
            require_once DISCUZ_ROOT.'./source/function/function_home.php';
            $image = pic_get($topicImage['attachment'], 'forum', 0, $topicImage['remote']);
        }
        return WebUtils::getHttpFileName($image);
    }

    public static function getTopicContent($tid, $post=array()) {
        if (empty($post)) {
            $post = self::getTopicPostInfo($tid);
        }
        return self::getContentByPost($post);
    }

    /**
     * 获取投票主题数据
     *
     * @param int|array $tid int为主题id, array为主题info
     *
     * @return array
     */
    public static function getTopicVoteInfo($tid) {
        return DzForumThread::getTopicVoteInfoByTid($tid);
    }

    public static function getTopicActivityInfo($tid) {
        return DzForumThread::getTopicActivityInfoByTid($tid);
    }

    /**
     * 测试是否投票主题
     *
     * @param int|array $tid int为主题id, array为主题info
     *
     * @return bool
     */
    public static function isVoteTopic($tid) {
        return DzForumThread::isVote($tid);
    }

    public static function isActivityTopic($tid) {
        return DzForumThread::isActivity($tid);
    }

    public static function isHotTopic($tid) {
        return DzForumThread::isHot($tid);
    }

    public static function isMarrowTopic($tid) {
        return DzForumThread::isMarrow($tid);
    }

    public static function isTopTopic($tid) {
        return DzForumThread::isTop($tid);
    }

    public static function isFavoriteTopic($uid, $tid) {return DzForumThread::isFavorite($uid, $tid);
    }

    /**
     * 测试是否此主帖仅作者可见
     */
    public static function isOnlyAuthorTopic($tid) {
        return DzForumThread::isOnlyAuthor($tid);
    }

    /**
     * 帖子相关
     */

    /**
     * 获取主题帖信息
     */
    public static function getTopicPostInfo($tid) {
        return DzForumPost::getFirstPostByTid($tid);
    }

    public static function getPostInfo($tid, $pid) {
        return DzForumPost::getPostByTidAndPid($tid, $pid);
    }

    /**
     * 获取回帖列表
     *
     * @param int $tid      Description.
     * @param int   $page     Description.
     * @param int   $pageSize Description.
     * @param array $params   Description.
     *
     * @return array
     */
    public static function getPostList($tid, $page=1, $pageSize=10, $params=array()) {
        return DzForumPost::getPostsByTid($tid, $page, $pageSize, $params);
    }

    /**
     * 获取回帖个数
     *
     */
    public static function getPostCount($tid, $params=array()) {
        return DzForumPost::getCountByTid($tid, $params);
    }

    public static function getPostContent($tid, $pid, $post=array()) {
        if (empty($post)) {
            $post = self::getPostInfo($tid, $pid);
        }
        return self::getContentByPost($post);
    }

    public static function getContentByPost($post) {
        $content = array();
        if (!empty($post)) {
            // 兼容有些用户自己添加了帖子标题，表情
            // if (!empty($post['face'])) {
            //     $post['smileyoff'] = 0;
            //     $post['message'] .= $post['face'];
            // }
            // $post['message'] .= $post['subject'];

            $post = self::_transPostMessage($post);
            $content = self::_filterPostMessage($post['message']);
        }
        return $content;
    }

    public static function getTopicLocation($tid) {
        return SurroundingInfo::getLocationById($tid, SurroundingInfo::TYPE_TOPIC);
    }

    public static function getPostLocation($pid) {
        return SurroundingInfo::getLocationById($pid, SurroundingInfo::TYPE_POST);
    }

    /**
     * 测试是否投票主题
     *
     * @param int $tid int为主题id
     * @param int|array $pid int为帖子id, array为帖子info
     *
     * @return bool
     */
    public static function isAnonymousPost($tid, $pid) {
        return DzForumPost::isAnonymous($tid, $pid);
    }

    // 获取帖子管理面板信息
    public static function getPostManagePanel() {
        global $_G;

        // 从DISCUZ_ROOT/source/module/forum/forum_viewthread.php复制而来
        $rushreply = getstatus($_G['forum_thread']['status'], 3);

        $allowblockrecommend = $_G['group']['allowdiy'] || getstatus($_G['member']['allowadmincp'], 4) || getstatus($_G['member']['allowadmincp'], 5) || getstatus($_G['member']['allowadmincp'], 6);
        if($_G['setting']['portalstatus']) {
            $allowpostarticle = $_G['group']['allowmanagearticle'] || $_G['group']['allowpostarticle'] || getstatus($_G['member']['allowadmincp'], 2) || getstatus($_G['member']['allowadmincp'], 3);
            $allowpusharticle = empty($_G['forum_thread']['special']) && empty($_G['forum_thread']['sortid']) && !$_G['forum_thread']['pushedaid'];
        } else {
            $allowpostarticle = $allowpusharticle = false;
        }
        if($_G['forum_thread']['displayorder'] != -4) {
            $modmenu = array(
                'thread' => $_G['forum']['ismoderator'] || $allowblockrecommend || $allowpusharticle && $allowpostarticle,
                'post' => $_G['forum']['ismoderator'] && ($_G['group']['allowwarnpost'] || $_G['group']['allowbanpost'] || $_G['group']['allowdelpost'] || $_G['group']['allowstickreply']) || $_G['forum_thread']['pushedaid'] && $allowpostarticle || $_G['forum_thread']['authorid'] == $_G['uid']
            );
        } else {
            $modmenu = array();
        }

        if (empty($modmenu)) {
            $modmenu['thread'] = false;
            $modmenu['post'] = false;
        }

        $manageItems = array('topic' => array(), 'post' => array());

        if ($modmenu['thread']) {
            if($_G['forum']['ismoderator']) {
                // 删除主题
                if($_G['group']['allowdelpost']) {
                    $manageItems['topic'][] = array('action' => 'delete', 'title' => WebUtils::t('删除主题'));
                }
                // 置顶
                if($_G['group']['allowstickthread'] && ($_G['forum_thread']['displayorder'] <= 3 || $_G['adminid'] == 1) && !$_G['forum_thread']['is_archived']) {
                    $manageItems['topic'][] = array('action' => 'top', 'title' => WebUtils::t('置顶'));
                }
                // 精华
                if($_G['group']['allowdigestthread'] && !$_G['forum_thread']['is_archived']) {
                    $manageItems['topic'][] = array('action' => 'marrow', 'title' => WebUtils::t('精华'));
                }
            }
        }

        if ($modmenu['post']) {
            if($_G['forum']['ismoderator']) {
                if($_G['group']['allowdelpost'] && !$rushreply) {
                    $manageItems['post'][] = array('action' => 'delete', 'title' => WebUtils::t('删除'));
                }
            }
        }

        return $manageItems;
    }

    private static function _transPostMessage($post) {
        if ($post['bbcodeoff'] != 1) {
            // 转换视音频，为了不走 discuz 原先的流程
            $post['message'] = preg_replace_callback(
                '/\[(audio|media|flash).*?\](.*?)\[\/\1\]/s',
                create_function('$matches', '
                    $string = "";
                    switch ($matches[1]) {
                        case "audio":
                            $string = "[mobcent_audio={$matches[2]}]";
                            break;
                        case "media":
                        case "flash":
                            $videoUrl = $matches[2];

                            // 转换优酷 .swf
                            $tempMatches = array();
                            preg_match("#^http://player\.youku\.com/player\.php/sid/(\w+?)/v\.swf$#s", $matches[2], $tempMatches);
                            if (!empty($tempMatches)) {
                                $videoUrl = "http://v.youku.com/v_show/id_{$tempMatches[1]}.html";
                            }
                            if (WebUtils::getDzPluginAppbymeAppConfig("forum_allow_app_play_video")) {
                                preg_match("#^http://v\.youku\.com/v_show/id_(\w+?)\.html$#s", $matches[2], $tempMatches);
                                if (!empty($tempMatches)) {
                                    $videoUrl = "http://v.youku.com/player/getM3U8/vid/{$tempMatches[1]}/type/flv/video.m3u8";
                                }
                            }

                            // 转换56 .swf
                            $tempMatches = array();
                            preg_match("#^http://player\.56\.com/(\w+?)\.swf#s", $matches[2], $tempMatches);
                            if (!empty($tempMatches)) {
                                $videoUrl = "http://www.56.com/u/{$tempMatches[1]}.html";
                            }

                            $string = "[mobcent_video={$videoUrl}]";
                            break;
                        default:
                            break;
                    }
                    return "[mobcent_br]\r\n" . $string . "\r\n";
                '),
                $post['message']
            );
        }

        // 转换超链接
        $post['message'] = preg_replace_callback('/\[url=(.*?)\](.*?)\[\\/url\]/', create_function('$matches', '
                $matches[1] = WebUtils::getHttpFileName($matches[1]);
                return "[mobcent_br]\r\n[mobcent_url={$matches[1]}(title={$matches[2]})]\r\n";
            '),
            $post['message']
        );

        return DzForumPost::transPostContentToHtml($post);
    }

    private static function _filterPostMessage($postMsg) {
        $msgArr = array('main' => '', 'quote' => array('who' => '', 'msg' => ''));

        $postMsg = WebUtils::emptyReturnLine($postMsg);
        $postMsg .= '[mobcent_br]<br />';

        // 处理引用内容
        $matches = array();
        preg_match_all('/<blockquote>.*?<font.*?>.*?<font.*?>(.*?)<\/font>.*?<br \/>(.*?)<\/blockquote>/', $postMsg, $matches);
        if (!empty($matches[1])) {
            $msgArr['quote']['who'] = $matches[1][0];
        }
        if (!empty($matches[2])) {
            $tmpMsg = preg_replace('/<br \/>/s', "\r\n", $matches[2][0]);
            $tmpMsg = preg_replace('/\[mobcent_audio=(.*?)\]/', '${1}', $tmpMsg);
            $tmpMsg = preg_replace('/\[mobcent_url=.*?\]/', '', $tmpMsg);
            $tmpMsg = preg_replace('/\[mobcent_br\]/', '', $tmpMsg);
            $tmpMsg = self::_emptyPostHtmlContent($tmpMsg);
            $tmpMsg = self::__trimPostContent($tmpMsg);
            $msgArr['quote']['msg'] = $tmpMsg;
        }
        if (empty($matches[1])) {
            unset($msgArr['quote']);
        }
        $postMsg = preg_replace('/<blockquote>.*?<\/blockquote>/', '', $postMsg);
        $postMsg = preg_replace('/<div class="quote">.*?<\/div>/', '', $postMsg);

        // 处理图片缩略图
        $postMsg = preg_replace('/<style>.*?<\/style>/', '', $postMsg);

        // 处理附件下载
        $postMsg = preg_replace_callback('/<ignore_js_op>.*?<\/ignore_js_op>/',
            create_function('$matches', '
                $attachment = "";
                $tempAttachUrlMatches = $tempAttachTitleMatches = $tempAttachDescMatches = $tempImgMatches = array();

                // 处理附件下载
                preg_match("/<strong>(.+?)<\/strong>/", $matches[0], $tempAttachTitleMatches);
                $title = !empty($tempAttachTitleMatches) ? $tempAttachTitleMatches[1] : "";

                preg_match("/<a.*?href=\"(.+?)\".*?>(.+?)<\/a>/", $matches[0], $tempAttachUrlMatches);
                if (!empty($tempAttachUrlMatches)) {
                    $url = WebUtils::getHttpFileName($tempAttachUrlMatches[1]);
                    preg_match("/<em.*?>(.+?)<\/em>/", $matches[0], $tempAttachDescMatches);
                    $desc = !empty($tempAttachDescMatches) ? $tempAttachDescMatches[1] : "";
                    $title == "" && $title = $tempAttachUrlMatches[2];
                    $attachment .= "[mobcent_br]<br />[mobcent_attachment={$url}(title={$title})(desc={$tempAttachDescMatches[1]})]<br />";
                }

                // 处理图片附件
                $imgPattern = "/<img.*?id=\".*?\".*?aid=\"(\d+)\".*?(zoomfile|makefile)=\"(.+?)\".*?\/>/";
                preg_match($imgPattern, $matches[0], $tempImgMatches);
                if (!empty($tempImgMatches)) {
                    $image = WebUtils::getHttpFileName($tempImgMatches[3]);
                    $attachment .= "[mobcent_br]<br />[mobcent_image={$image}(aid={$tempImgMatches[1]})]<br />";
                }

                return $attachment;
            '),
            $postMsg
        );

        // 处理网络图片
        $postMsg = preg_replace_callback('/<img.*?id[^>]*?(src|file)="(.+?)".*?\/>/',
            create_function('$matches', '
                $image = WebUtils::getHttpFileName($matches[2]);
                // 处理给手机客户端的表情图片
                if (strpos($image, "/app/data/phiz/") !== false) {
                    if (($key = array_search(basename($image), WebUtils::getMobcentPhizMaps())) !== false) {
                        return $key;
                    }
                }
                return "[mobcent_br]<br />[mobcent_image={$image}]<br />";
            '),
            $postMsg
        );

        // 处理表情
        $postMsg = preg_replace_callback('/<img src="(.+?)".*?smilieid.*?\/>/',
            create_function('$matches', '
                $image = WebUtils::getHttpFileName($matches[1]);
                return "[mobcent_phiz={$image}]";
            '),
            $postMsg
        );

        // 处理文字排版
        $postMsg = str_replace('</div>', '<br />', $postMsg);

        // 转换格式
        $matches = array();
        preg_match_all('/(.*?)<br \/>/', $postMsg, $matches);
        if (!empty($matches[1])) {
            $lastLineIndex = count($matches[1]) - 1;
            $tempMsg = array('type' => 'text', 'content' => '');
            foreach ($matches[1] as $i => $line) {
                $line = self::_emptyPostHtmlContent($line);
                $msg = array('content' => $line, 'type' => 'text');

                $tempMatches = array();
                preg_match('/\[mobcent_(audio|video|image|url|attachment)=(.*?)\]/', $line, $tempMatches);
                if (!empty($tempMatches)) {
                    $msg['type'] = $tempMatches[1];
                    switch ($msg['type']) {
                        case 'audio':
                        case 'video':
                        case 'image':
                            $tempMatches2 = array();
                            preg_match('/(.+)\(aid=(\d+)\)/', $tempMatches[2], $tempMatches2);
                            if (!empty($tempMatches2)) {
                                $msg['content'] = $tempMatches2[1];
                                $msg['extraInfo']['aid'] = (int)$tempMatches2[2];
                            } else {
                                $msg['content'] = $tempMatches[2];
                            }
                            if ($msg['type'] == 'audio' && strpos($msg['content'], '.mobcent.mp3') !== false ) {
                                $msg['content'] = rtrim($msg['content'], '.mobcent.mp3');
                            }
                            // 处理有些用户用了远程附件插件的路径问题
                            if (strpos($msg['content'], '/mobcent/app/web/forum.php') !== false) {
                                $msg['content'] = str_replace('/mobcent/app/web/', '/', $msg['content']);
                            }
                            break;
                        case 'url':
                            $msg['content'] = $msg['extraInfo']['url'] = '';
                            $tempUrlMatches = array();
                            preg_match('/(.+?)\(title=(.+?)\)/', $tempMatches[2], $tempUrlMatches);
                            if (!empty($tempUrlMatches)) {
                                $msg['content'] = $tempUrlMatches[2];
                                $msg['extraInfo']['url'] = $tempUrlMatches[1];
                            }
                            break;
                        case 'attachment':
                            $msg['content'] = $msg['extraInfo']['url'] = $msg['extraInfo']['desc'] = '';
                            $tempAttachMatches = array();
                            preg_match('/(.+?)\(title=(.+?)\)\(desc=(.+)\)/', $tempMatches[2], $tempAttachMatches);
                            if (!empty($tempAttachMatches)) {
                                $msg['content'] = $tempAttachMatches[2];
                                $msg['extraInfo']['url'] = $tempAttachMatches[1];
                                $msg['extraInfo']['desc'] = $tempAttachMatches[3];
                            }
                        default: $msg['content'] = ''; break;
                    }
                }

                // 拼接text类型
                if ($msg['type'] == 'text') {
                    $line != '[mobcent_br]' && ($tempMsg['content'] .= $line . "\r\n");
                    $tempMsg['content'] = str_replace('[mobcent_br]', '', $tempMsg['content']);
                }
                if ($msg['type'] != 'text' || $i == $lastLineIndex) {
                    $tempMsg['content'] = self::__trimPostContent($tempMsg['content']);
                    $tempMsg['content'] != '' && $msgArr['main'][] = $tempMsg;
                    $tempMsg['content'] = '';
                    $msg['type'] != 'text' && $msgArr['main'][] = $msg;
                }
            }
        }

        return $msgArr;
    }

    private static function _emptyPostHtmlContent($postMsg) {
        $newPostMsg = $postMsg;

        // 处理引用内容
        $newPostMsg = preg_replace('/<blockquote>.*?<\/blockquote>/', '', $newPostMsg);

        // 处理隐藏内容
        // $newPostMsg = preg_replace('/<div class="showhide">.*?<\/div>/', '', $newPostMsg);
        // $newPostMsg = preg_replace('/<div class="locked">.*?<\/div>/', '', $newPostMsg);

        // 处理附件
        $newPostMsg = preg_replace('/<ignore_js_op>.*?<\/ignore_js_op>/', ' ', $newPostMsg);

        // 处理其他
        $newPostMsg = preg_replace('/<script.*?<\/script>/', ' ', $newPostMsg);
        $newPostMsg = preg_replace('/<!--.*?-->/', '', $newPostMsg);

        $newPostMsg = WebUtils::emptyHtml($newPostMsg);

        return $newPostMsg;
    }

    private static function __trimPostContent($str) {
        return trim($str, "\t\n\r\0\x0B");
    }

    public static function getPostSendStatus($type='topic', $platType=APP_TYPE_ANDROID) {
        // return $platType == APP_TYPE_APPLE ? DzForumPost::STATUS_SEND_FROM_APP_APPLE : DzForumPost::STATUS_SEND_FROM_APP_ANDROID;
        // topic 的15位的apple status代码有冲突，故改为与apple相同
        return $platType == APP_TYPE_APPLE && $type== 'post' ? setstatus(15, 1) : setstatus(16, 1);
    }

    /**
     * 获取获取设置客户端尾巴
     */
    public static function getMobileSign($status) {
        if (!WebUtils::getDzPluginAppbymeAppConfig('mobile_allow_app_sign')) {
            return '';
        }

        $postSigns = array(
            'android' => WebUtils::t('来自安卓手机客户端'),
            'apple' => WebUtils::t('来自苹果手机客户端'),
        );
        $postSignText = WebUtils::getDzPluginAppbymeAppConfig('mobile_sign_post');
        $postSignText = WebUtils::emptyReturnLine($postSignText);
        $matches = array();
        preg_match_all('/\[title_(all|android|apple)\](.*?)\[\/title_\1\]/', $postSignText, $matches, PREG_SET_ORDER);
        foreach ($matches as $matche) {
            if ($matche[1] == 'all') {
                $postSigns['android'] = $postSigns['apple'] = $matche[2];
                break;
            } else if ($matche[1] == 'android' || $matche[1] == 'apple') {
                $postSigns[$matche[1]] = $matche[2];
            }
        }

        $postSign = '';
        // if ($status & DzForumPost::STATUS_SEND_FROM_APP_ANDROID) {
        if (getstatus($status, 16)) {
            $postSign = $postSigns['android'];
        // } else if ($status & DzForumPost::STATUS_SEND_FROM_APP_APPLE) {
        } else if (getstatus($status, 15)) {
            $postSign = $postSigns['apple'];
        } else {
            $postSign = '';
        }
        return WebUtils::emptyHtml($postSign);
    }
}