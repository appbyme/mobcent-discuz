<?php

/**
 * 页面嵌入-手机版
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 * @license http://opensource.org/licenses/LGPL-3.0
 */

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

require_once dirname(__FILE__) . '/appbyme.class.php';

class mobileplugin_appbyme_app {

    public function global_footer() {
        if (Appbyme::isInstalled()) {
            Appbyme::init();
            
            Appbyme::runCron();
        }
        return '';
    }
}

class mobileplugin_appbyme_app_forum extends mobileplugin_appbyme_app {

    public function forumdisplay_thread_mobile_output() {
        $res = array();
        global $_G;
        foreach ($_G['forum_threadlist'] as $thread) {
            $res[] = Appbyme::getPostSign('mobile_sign_thread_mobile', 'forumdisplay_thread', $thread['status']);
        }
        return $res;
    }

    public function viewthread_posttop_mobile_output() {
        return $this->_getPostSignOutput('posttop');
    }

    public function viewthread_postbottom_mobile_output() {
        return $this->_getPostSignOutput('postbottom');
    }

    private function _getPostSignOutput($hookPosition) {
        $res = array();
        if ($hookPosition == Appbyme::$config['mobile_sign_position_mobile']) {
            global $postlist;
            foreach ($postlist as $post) {
                $res[] = Appbyme::getPostSign('mobile_sign_post_mobile', 'viewthread_post', $post['status']);
            }
        }
        return $res;
    }
}