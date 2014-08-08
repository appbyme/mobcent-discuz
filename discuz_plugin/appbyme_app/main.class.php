<?php

/**
 * 页面嵌入-普通版
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 * @license http://opensource.org/licenses/LGPL-3.0
 */

if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

require_once dirname(__FILE__) . '/appbyme.class.php';

class plugin_appbyme_app {

    public function common() {
        if (Appbyme::isInstalled()) {
            Appbyme::init();
            if (Appbyme::$config['mobile_allow_download_redirect']) {
                !defined('MOBILE_API_OUTPUT') && define('MOBILE_API_OUTPUT', 1);
            }
        }
    }

    public function global_footer() {
        Appbyme::runCron();
        return '';
    }
}

class plugin_appbyme_app_forum extends plugin_appbyme_app {

    public function forumdisplay_thread_subject_output() {
        $res = array();
        global $_G;
        foreach ($_G['forum_threadlist'] as $thread) {
            $res[] = Appbyme::getPostSign('mobile_sign_thread', 'forumdisplay_thread', $thread['status']);
        }
        return $res;
    }

    public function viewthread_postheader_output() {
        return $this->_getPostSignOutput('postheader');
    }
    
    public function viewthread_posttop_output() {
        return $this->_getPostSignOutput('posttop');
    }

    public function viewthread_postbottom_output() {
        return $this->_getPostSignOutput('postbottom');
    }

    public function viewthread_postfooter_output() {
        return $this->_getPostSignOutput('postfooter');
    }

    public function viewthread_postsightmlafter_output() {
        return $this->_getPostSignOutput('postsightmlafter');
    }

    public function viewthread_avatar_output() {
        $res = array();
        global $postlist;
        foreach ($postlist as $post) {
            $res[] = Appbyme::getPostSign('mobile_sign_avatar', 'viewthread_avatar', $post['status']);
        }
        return $res;
    }

    private function _getPostSignOutput($hookPosition) {
        $res = array();
        if ($hookPosition == Appbyme::$config['mobile_sign_position']) {
            global $postlist;
            foreach ($postlist as $post) {
                $res[] = Appbyme::getPostSign('mobile_sign_post', 'viewthread_post', $post['status']);
            }
        }
        return $res;
    }
}

class plugin_appbyme_app_misc extends plugin_appbyme_app {

    public function mobile() {
        Appbyme::$config['mobile_allow_download_redirect'] && header('Location: '.Appbyme::createAppApiUrl('misc/download'));
    }
}