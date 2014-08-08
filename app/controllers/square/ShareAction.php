<?php

/**
 * 分享 接口
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class ShareAction extends MobcentAction {
  
    public function run($share) {
        $res = $this->initWebApiArray();

        // $share = "{'body': {'shareInfo': {'shareId':8,'shareType': 'news'}}}";
        $share = rawurldecode($share);
        $shareInfo = WebUtils::jsonDecode($share); 

        $res['body']['shareData'] = $this->_getShareData($shareInfo);

        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _getShareData($shareInfo) {
        $data = array('title' => '', 'source' => '', 'content' => array());

        $shareInfo = !empty($shareInfo['body']['shareInfo']) ? $shareInfo['body']['shareInfo'] : array();
        if (!empty($shareInfo)) {
            $sid = $shareInfo['shareId'];
            $type = $shareInfo['shareType'];
            switch ($type) {
                case 'topic':
                    $info = ForumUtils::getTopicInfo($sid);
                    $content = ForumUtils::getTopicContent($sid);
                    $data['title'] = !empty($info['subject']) ? $info['subject'] : '';
                    $data['source'] = !empty($info['dateline']) ? date('Y-m-d H:i:s', $info['dateline']) : '';
                    $data['content'] = $this->_getPostContent($content);
                    break;
                case 'news':
                    $info = PortalUtils::getNewsInfo($sid);
                    $content = PortalUtils::getNewsContent($info);
                    $data['title'] = !empty($info['title']) ? $info['title'] : '';
                    $data['source'] = !empty($info['dateline']) ? date('Y-m-d H:i:s', $info['dateline']) : '';
                    $data['content'] = $this->_filterContent($content);
                    break;
                default:
                    break;
            }
        }

        return $data;
    }

    private function _getPostContent($content) {
        return !empty($content['main']) ? $this->_filterContent($content['main']) : array();
    }

    private function _filterContent($content) {
        $newContent = array();
        foreach ($content as $key => $value) {
            $newContent[$key]['infor'] = $value['content'];
            $newContent[$key]['type'] = $value['type'];
            // 兼容老版本的url类型
            if ($value['type'] == 'url') {
                $newContent[$key]['infor'] = sprintf('[mobcent_url=%s]%s[/mobcent_url]', $value['extraInfo']['url'], $value['content']);
                $newContent[$key]['type'] = 'text';
            } else if ($value['type'] == 'image') {
                $image = ImageUtils::getThumbImageEx($value['content'], 5);
                // 分享返回缩略大图
                $image['image'] = str_replace('xgsize_', 'mobcentBigPreview_', $image['image']);
                $newContent[$key]['infor'] = $image['image'];
            }
        }
        return $newContent;
    }
}