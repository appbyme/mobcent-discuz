<?php

/**
 * @author 谢建平 <jianping_xie@aliyun.com>  
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

// Mobcent::setErrors();
class TestController extends MobcentController {
    
    protected function beforeAction($action) {
        // parent::beforeAction($action);
        global $_G;
        $action->id != 'siteinfo' && $_G['uid'] != 1 && exit('Access Denied');
        
        return true;
    }

    public function actionIndex() {
        echo 'mobcent test';
    }

    public function actionPhpInfo() {
        phpinfo();
    }

    public function actionConfig() {
        echo '<pre>';
        print_r(Yii::app());
        echo '</pre>';
    }

    public function actionPluginConfig() {
        $appbymeAppConfig = WebUtils::getDzPluginAppbymeAppConfig();
        $appbymeApp = DbUtils::getDzDbUtils(true)->queryRow('
            SELECT *
            FROM %t
            WHERE identifier=%s
            ',
            array('common_plugin', MOBCENT_DZ_PLUGIN_ID)
        );
        
        $unserializeKeys = array('cache_usergroup', 'forum_announcement_show', 'forum_show', 'forum_show_image', 'forum_photo_show');
        foreach ($unserializeKeys as $key => $value) {
            $appbymeAppConfig[$value] = unserialize($appbymeAppConfig[$value]);
        }
        
        $config = array_merge(
            array('appbyme_app_info' => $appbymeApp), 
            array('appbyme_app_config' => $appbymeAppConfig)
        );

        debug($config);
        // echo WebUtils::jsonEncode($config);
    }

    public function actionFile() {
        echo WebUtils::jsonEncode(array(
            'file' => __FILE__,
        ));
    }
    
    public function actionPluginInfo() {
        $hasPortal = WebUtils::getDzPluginAppbymeAppConfig('portal_allow_open');
        $hasPortal = $hasPortal == 1 ? 1 : 0;
        
        echo WebUtils::jsonEncode(array(
            'mobcent_version' => MOBCENT_VERSION,
            'mobcent_release' => MOBCENT_RELEASE,
            'mobcent_release_debug' => MOBCENT_RELEASE_DEBUG,
            'discuz_version' => MobcentDiscuz::getDiscuzVersion(),
            'mobcent_discuz_version' => MobcentDiscuz::getMobcentDiscuzVersion(),
            'has_portal' => $hasPortal,
        ));
    }

    public function actionSiteInfo() {
        $res = array();
        
        global $_G;
        $setting = $_G['setting'];

        $tmpPassword = trim($_REQUEST['install_password']);
        $password = WebUtils::subString(WebUtils::getDzPluginAppbymeAppConfig('install_password'), 0, 10);
        if (!empty($password) && $password == $tmpPassword) {
            $res['info'] = array(
                'setting_basic_bbname' => $setting['bbname'],
                'setting_basic_sitename' => $setting['sitename'],
                'setting_basic_siteurl' => $setting['siteurl'],
                'setting_basic_adminemail' => $setting['adminemail'],
                'setting_basic_icp' => $setting['icp'],
                'setting_basic_boardlicensed' => $setting['boardlicensed'],

                'onlineinfo' => 0,
                'thread_num' => 0,
                'post_num' => 0,
                'person_num' => 0,
                'setting_basic_stat' => '',
            );
            $res['rs'] = 1;
        } else {
            $res = array('rs' => 0, 'errcode' => '01010000');
        }
        
        echo WebUtils::jsonEncode($res);
    }

    public function actionUserInfo($uid) {
        $user = array_merge(UserUtils::getUserInfo($uid), UserUtils::getUserProfile($uid));
        echo WebUtils::jsonEncode($user);
    }

    public function actionForumInfo($fid) {
        $forum = ForumUtils::getForumInfos($fid);
        echo WebUtils::jsonEncode($forum);
    }

    public function actionTopicInfo($tid) {
        $topic = ForumUtils::getTopicInfo($tid);
        echo WebUtils::jsonEncode($topic);
    }

    public function actionPostInfo($pid, $tid=1) {
        $postInfo = ForumUtils::getPostInfo($tid, $pid);
        echo WebUtils::jsonEncode($postInfo);
    }

    public function actionArticleInfo($aid) {
        $article = PortalUtils::getNewsInfo($aid);
        echo WebUtils::jsonEncode($article);
    }

    public function actionDebug() {
        $testApiList = array(
            array(
                'title' => 'debug: phpinfo',
                'route' => 'test/phpinfo',
                'params' => array(),
            ),
            array(
                'title' => 'debug: 测试文件',
                'route' => 'test/file',
                'params' => array(),
            ),
            array(
                'title' => 'debug: 网站配置',
                'route' => 'test/config',
                'params' => array(),
            ),
            array(
                'title' => 'debug: 接口信息',
                'route' => 'test/plugininfo',
                'params' => array(),
            ),
            array(
                'title' => 'debug: 插件配置',
                'route' => 'test/pluginconfig',
                'params' => array(),
            ),
            array(
                'title' => 'debug: 安装信息',
                'route' => 'test/siteinfo',
                'params' => array('install_password' => 12345678),
            ),
            array(
                'title' => 'debug: 用户信息',
                'route' => 'test/userinfo',
                'params' => array('uid' => 1),
            ),
            array(
                'title' => 'debug: 版块信息',
                'route' => 'test/foruminfo',
                'params' => array('fid' => 2),
            ),
            array(
                'title' => 'debug: 主题信息',
                'route' => 'test/topicinfo',
                'params' => array('tid' => 1),
            ),
            array(
                'title' => 'debug: 帖子信息',
                'route' => 'test/postinfo',
                'params' => array('pid' => 1, 'tid' => 1),
            ),
            array(
                'title' => 'debug: 文章信息',
                'route' => 'test/articleinfo',
                'params' => array('aid' => 1),
            ),
            array(
                'title' => 'api/cache: 更新缓存',
                'route' => 'cache/update',
                'params' => array(),
            ),
            array(
                'title' => 'api/cache: 清理缓存',
                'route' => 'cache/clean',
                'params' => array(),
            ),
            array(
                'title' => 'api/cache: 生成缩略图',
                'route' => 'cache/makethumb',
                'params' => array('count' => 20),
            ),
            array(
                'title' => 'api/cache: 清理缩略图',
                'route' => 'cache/cleanthumb',
                'params' => array(),
            ),
            array(
                'title' => 'api/forum: 版块列表',
                'route' => 'forum/forumlist',
                'params' => array('fid' => 0),
            ),
            array(
                'title' => 'api/forum: 帖子详情',
                'route' => 'forum/postlist',
                'params' => array('topicId' => 1),
            ),
            array(
                'title' => 'api/user: 获取设置',
                'route' => 'user/getsetting',
                'params' => array('getSetting' => "{'body': {'postInfo': {'forumIds': '0'}}}"),
            ),
            array(
                'title' => 'api/user: 登陆',
                'route' => 'user/login',
                'params' => array('type' => 'login', 'username' => 'admin', 'password'=>'admin'),
            ),
            array(
                'title' => 'api/app: 初始化AppUI',
                'route' => 'app/initui',
                'params' => array(),
            ),
        );
        $this->renderPartial('debug', array('testApiList' => $testApiList));
    } 
}