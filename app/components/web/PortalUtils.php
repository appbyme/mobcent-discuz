<?php

/**
 * 门户相关工具类
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class PortalUtils {
    
    const CONTENT_DELIMITER = '[mobcent_br]';

    /**
     * 获取文章摘要(内容摘要以及图片)
     *
     * @param int $aid 文章id
     * @param bool $transBr 是否要转换换行
     * @return array array('msg' => '', 'image' => '')
     */
    public static function getArticleSummary($aid, $transBr=true) {
        $summary = array('msg' => '', 'image' => '');

        $summaryLength = WebUtils::getDzPluginAppbymeAppConfig('portal_summary_length');
        $allowImage = WebUtils::getDzPluginAppbymeAppConfig('portal_allow_image');
        $allowImage = !($allowImage === '0');
        if ($summaryLength === '0' && !$allowImage) {
            return $summary;
        }

        require_once DISCUZ_ROOT.'./source/function/function_home.php';

        $article = DzPortalArticle::getArticleByAid($aid);
        if (!empty($article)) {
            $msg = $article['summary'];
            if ($article['pic']) {
                // $article['pic'] = pic_get($article['pic'], '', $article['thumb'], $article['remote'], 1, 1);
                $article['pic'] = pic_get($article['pic'], '', $article['thumb'], $article['remote'], 0, 1);
                $allowImage && $summary['image'] = WebUtils::getHttpFileName($article['pic']);
            }
            $transBr && $msg = WebUtils::emptyReturnLine($msg, ' ');
            $msg = trim($msg);
            $summaryLength === false && $summaryLength = 40;
            $summary['msg'] = (string)WebUtils::subString($msg, 0, $summaryLength);
        }

        return $summary;
    }

    /**
     * 获取文章封面图片
     * 
     * @param int $aid 文章ID
     * @return string
     */
    public static function getArticleCover($aid) {
        $summary = PortalUtils::getArticleSummary($aid);
        return $summary['image'];
    }

    /**
     * 获取文章信息
     *
     * @param int $aid
     * @return array
     */
    public static function getNewsInfo($aid) {
        return DzPortalArticle::getArticleByAid($aid);
    }


    /**
     * 获取文章内容
     *
     * @param int|array $aid
     * @param int $page
     * @return array
     */
    public static function getNewsContent($aid, $page=1) {
        global $_G;

        $article = $aid;
        !is_array($aid) && $article = PortalUtils::getNewsInfo($aid);

        $content = C::t('portal_article_content')->fetch_by_aid_page($article['aid'], $page);
        if (empty($content)) {
            return $content;
        }

        $content['content'] = PortalUtils::preParseArticleContent($content['content']);

        require_once libfile('function/blog');
        $content['content'] = blog_bbcode($content['content']);

        if($article['idtype'] == 'tid' || $content['idtype']=='pid') {
            $thread = $firstpost = array();
            require_once libfile('function/discuzcode');
            require_once libfile('function/forum');
            $thread = get_thread_by_tid($article['id']);
            if(!empty($thread)) {
                if($content['idtype']=='pid') {
                    $firstpost = C::t('forum_post')->fetch($thread['posttableid'], $content['id']);
                } else {
                    $firstpost = C::t('forum_post')->fetch_threadpost_by_tid_invisible($article['id']);
                }
                if($firstpost && $firstpost['tid'] == $article['id']) {
                    $firstpost['uid'] = $firstpost['authorid'];
                    $firstpost['username'] = $firstpost['author'];
                }
            }
            if(!empty($firstpost) && !empty($thread) && $thread['displayorder'] != -1) {
                $_G['tid'] = $article['id'];
                $aids = array();
                $firstpost['message'] = $content['content'];
                if($thread['attachment']) {
                    $_G['group']['allowgetimage'] = 1;
                    if(preg_match_all("/\[attach\](\d+)\[\/attach\]/i", $firstpost['message'], $matchaids)) {
                        $aids = $matchaids[1];
                    }
                }

                if($aids) {
                    parseforumattach($firstpost, $aids);
                }
                $content['content'] = $firstpost['message'];
                $content['pid'] = $firstpost['pid'];
            } else {
                C::t('portal_article_title')->update($aid, array('id' => 0, 'idtype' => ''));
                C::t('portal_article_content')->update_by_aid($aid, array('id' => 0, 'idtype' => ''));
            }
        } elseif($article['idtype']=='blogid') {
            $content['content'] = '';
        }

        return PortalUtils::transArticleContent($content['content']);
    }

    public static function preParseArticleContent($content) {
        $content = WebUtils::emptyReturnLine($content);
        $content = $content.PortalUtils::CONTENT_DELIMITER;
        // 转换视音频，为了不走 discuz 原先的流程
        $content = preg_replace_callback(
            '/\[flash(.*?)\](.*?)\[\/flash\]/s',
            create_function('$matches', '
                $string = "";
                switch ($matches[1]) {
                    case "=mp3":
                        $string = "[mobcent_audio={$matches[2]}]";
                        break;
                    default:
                        $videoUrl = $matches[2];

                        // 转换优酷 .swf
                        $tempMatches = array();
                        preg_match("#^http://player\.youku\.com/player\.php/sid/(\w+?)/v\.swf$#s", $matches[2], $tempMatches);
                        if (!empty($tempMatches)) {
                            $videoUrl = "http://v.youku.com/v_show/id_{$tempMatches[1]}.html";
                        }
                        // 转换56 .swf
                        $tempMatches = array();
                        preg_match("#^http://player\.56\.com/(\w+?)\.swf#s", $matches[2], $tempMatches);
                        if (!empty($tempMatches)) {
                            $videoUrl = "http://www.56.com/u/{$tempMatches[1]}.html";
                        }

                        $string = "[mobcent_video={$videoUrl}]";
                        break;
                }
                return PortalUtils::CONTENT_DELIMITER.$string.PortalUtils::CONTENT_DELIMITER;
            '),
            $content
        );
        // 处理url
        $content = preg_replace_callback('/<a href="(.*?)".*?>(.*?)<\/a>/is', 
            create_function('$matches', '
                $string = "";
                $matches[1] = WebUtils::getHttpFileName($matches[1]);
                // 处理是图片的链接
                if (strpos($matches[2], "<img") !== false) {
                    $string = $matches[2];
                } else {
                    $string = sprintf("[mobcent_url=%s]%s[/mobcent_url]", $matches[1], $matches[2]);
                }
                return $string;
            '),
            $content
        );
        return $content;
    }

    public static function parseArticleContent($content) {
        // 处理图片
        $matches = array();
        $content = preg_replace_callback('/<img.*?src="(.*?)".*?>/is', 
            create_function('$matches', '
                $string = "";
                $matches[1] = WebUtils::getHttpFileName($matches[1]);
                // 处理表情
                if (strpos($matches[1], "static/image/smiley") !== false) {
                    $string = "[mobcent_phiz={$matches[1]}]";
                } else {
                    $string = sprintf("%s[mobcent_image=%s]%s", PortalUtils::CONTENT_DELIMITER, $matches[1], PortalUtils::CONTENT_DELIMITER);
                }
                return $string;
            '),
            $content
        );

        return $content;
    }

    public static function transArticleContent($content) {
        $content = PortalUtils::parseArticleContent($content);
        $newContent = array();
        $matches = array();
        preg_match_all('/(.+?)\[mobcent_br\]/s', $content, $matches);
        foreach ($matches[1] as $match) {
            $tempMatches = array();
            $tempContent = array('type' => 'text', 'content' => '');
            preg_match('/\[mobcent_(audio|video|image)=(.*?)\]/', $match, $tempMatches);
            if (!empty($tempMatches)) {
                $tempContent['type'] = $tempMatches[1];
                $tempContent['content'] = $tempMatches[2];
                if ($tempContent['type'] == 'image') {
                    $tempContent['extraInfo']['source'] = $tempMatches[2];
                    $tempContent['content'] = ImageUtils::getThumbImage($tempMatches[2]);
                }
            } else {
                $match = preg_replace('/(<p>|<\/p>|<div>|<\/div>|<br>|<br\/>)/i', PortalUtils::CONTENT_DELIMITER, $match);
                $match .= PortalUtils::CONTENT_DELIMITER;
                $tempText = '';
                $tempMatches = array();
                preg_match_all('/(.+?)\[mobcent_br\]/s', $match, $tempMatches);
                foreach ($tempMatches[1] as $value) {
                    $value = str_replace(PortalUtils::CONTENT_DELIMITER, '', $value);
                    $tempText .= $value . "\r\n";
                }
                $tempText = WebUtils::emptyHtml($tempText);
                $tempText = (string)trim($tempText);
                $tempContent['content'] = $tempText;
            }
            $tempContent['content'] != '' && $newContent[] = $tempContent;
        }
        return $newContent;
    }

    // 查询文章的回复数和评论数
    public static function getArticleCount($aid) {
        return DzPortalArticle::getArticleCountByAid($aid);
    }
}

function parseforumattach(&$post, $aids) {
    global $_G;
    if(($aids = array_unique($aids))) {
        require_once libfile('function/attachment');
        $finds = $replaces = array();
        foreach(C::t('forum_attachment_n')->fetch_all_by_id('tid:'.$post['tid'], 'aid', $aids) as $attach) {

            $attach['url'] = ($attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl']).'forum/';
            $attach['dateline'] = dgmdate($attach['dateline'], 'u');
            $extension = strtolower(fileext($attach['filename']));
            $attach['ext'] = $extension;
            $attach['imgalt'] = $attach['isimage'] ? strip_tags(str_replace('"', '\"', $attach['description'] ? $attach['description'] : $attach['filename'])) : '';
            $attach['attachicon'] = attachtype($extension."\t".$attach['filetype']);
            $attach['attachsize'] = sizecount($attach['filesize']);

            $attach['refcheck'] = (!$attach['remote'] && $_G['setting']['attachrefcheck']) || ($attach['remote'] && ($_G['setting']['ftp']['hideurl'] || ($attach['isimage'] && $_G['setting']['attachimgpost'] && strtolower(substr($_G['setting']['ftp']['attachurl'], 0, 3)) == 'ftp')));
            $aidencode = packaids($attach);
            $widthcode = attachwidth($attach['width']);
            $is_archive = $_G['forum_thread']['is_archived'] ? "&fid=".$_G['fid']."&archiveid=".$_G['forum_thread']['archiveid'] : '';
            if($attach['isimage']) {
                $attachthumb = getimgthumbname($attach['attachment']);
                    if($_G['setting']['thumbstatus'] && $attach['thumb']) {
                        $replaces[$attach['aid']] = "<a href=\"javascript:;\"><img id=\"_aimg_$attach[aid]\" aid=\"$attach[aid]\" onclick=\"zoom(this, this.getAttribute('zoomfile'), 0, 0, '{$_G[forum][showexif]}')\"
                        zoomfile=\"".($attach['refcheck']? "forum.php?mod=attachment{$is_archive}&aid=$aidencode&noupdate=yes&nothumb=yes" : $attach['url'].$attach['attachment'])."\"
                        src=\"".($attach['refcheck'] ? "forum.php?mod=attachment{$is_archive}&aid=$aidencode" : $attach['url'].$attachthumb)."\" alt=\"$attach[imgalt]\" title=\"$attach[imgalt]\" w=\"$attach[width]\" /></a>";
                    } else {
                        $replaces[$attach['aid']] = "<img id=\"_aimg_$attach[aid]\" aid=\"$attach[aid]\"
                        zoomfile=\"".($attach['refcheck'] ? "forum.php?mod=attachment{$is_archive}&aid=$aidencode&noupdate=yes&nothumb=yes" : $attach['url'].$attach['attachment'])."\"
                        src=\"".($attach['refcheck'] ? "forum.php?mod=attachment{$is_archive}&aid=$aidencode&noupdate=yes " : $attach['url'].$attach['attachment'])."\" $widthcode alt=\"$attach[imgalt]\" title=\"$attach[imgalt]\" w=\"$attach[width]\" />";
                    }
            } else {
                $replaces[$attach['aid']] = "$attach[attachicon]<a href=\"forum.php?mod=attachment{$is_archive}&aid=$aidencode\" onmouseover=\"showMenu({'ctrlid':this.id,'pos':'12'})\" id=\"aid$attach[aid]\" target=\"_blank\">$attach[filename]</a>";
            }
            $finds[$attach['aid']] = '[attach]'.$attach['aid'].'[/attach]';
        }
        if($finds && $replaces) {
            $post['message'] = str_ireplace($finds, $replaces, $post['message']);
        }
    }
}