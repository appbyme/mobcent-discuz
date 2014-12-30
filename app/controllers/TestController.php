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
        $accessActions = array('siteinfo', 'plugininfo');
        if (in_array($action->id, $accessActions)) {
            return true;
        }
        
        global $_G;
        $_G['adminid'] != 1 && exit('Access Denied');
        
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
        $content = rawurlencode("[{'type':0,'infor':'人生得意须尽欢,[挖鼻屎][呵呵]@xjp'}]");
        $typeOption = rawurlencode("{'profile':1,'makes':1,'boolen':2,'floor':4,'price':'19999','image':'','address':'1,2'}");
        $topicAdminJson = '{
            "body":
            {
                "json":
                {
                    "fid": 2,
                    "tid":"7",
                    "location": "",
                    "aid": "",
                    "content":"'.$content.'",
                    "title": "1111111",
                    "longitude": "116.302891",
                    "latitude": "40.055069",
                    "isOnlyAuthor": 0,
                    "isHidden": 0,
                    "isAnonymous": 0,
                    "isShowPostion": 1,
                    "isQuote": "",
                    "replyId": "",
                    "sortId": "",
                    "typeId": "",
                    "typeOption":"'.$typeOption.'",
                },
            },
        }';
        $testApiList = array(
            array(
                'title' => 'debug: test/phpinfo phpinfo',
                'route' => 'test/phpinfo',
                'params' => array(),
            ),
            array(
                'title' => 'debug: test/file 测试文件',
                'route' => 'test/file',
                'params' => array(),
            ),
            array(
                'title' => 'debug: test/config 网站配置',
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
                'title' => 'api/forum: forum/forumlist 版块列表',
                'route' => 'forum/forumlist',
                'params' => array('fid' => 0),
            ),
            array(
                'title' => 'api/forum: forum/topiclist 主题列表',
                'route' => 'forum/topiclist',
                'params' => array('boardId' => 0, 'page' => 1, 'pageSize' => 10, 'sortby' => 'all', 'filterType' => '', 'filterId' => 0),
            ),
            array(
                'title' => 'api/forum: forum/postlist 帖子详情',
                'route' => 'forum/postlist',
                'params' => array('topicId' => 1),
            ),
            array(
                'title' => 'api/forum: forum/search 搜索',
                'route' => 'forum/search',
                'params' => array('keyword' => '测试', 'page' => 1, 'pageSize' => 10, 'searchid' => 0),
            ),
            array(
                'title' => 'api/forum: forum/sendattachmentex 上传',
                'route' => 'forum/sendattachmentex',
                'params' => array('type' => 'image', 'module' => 'forum', 'albumId' => -1),
            ),
            array(
                'title' => 'api/forum: forum/topicadmin 帖子管理',
                'route' => 'forum/topicadmin',
                'params' => array('platType' => 1, 'act' => 'new', 'json' => $topicAdminJson),
            ),
            array(
                'title' => 'api/portal: portal/newsview 文章详情',
                'route' => 'portal/newsview',
                'params' => array('json' => "{'aid': 1, 'page': 1}"),
            ),
            array(
                'title' => 'api/portal: portal/commentlist 文章评论',
                'route' => 'portal/commentlist',
                'params' => array('json' => "{'id': 1, 'idType': 'aid', 'page': 1, 'pageSize': 10, }"),
            ),
            array(
                'title' => 'api/portal: portal/modulelist 门户模块列表',
                'route' => 'portal/modulelist',
                'params' => array(),
            ), 
            array(
                'title' => 'api/portal: portal/newslist 门户模块内容列表',
                'route' => 'portal/newslist',
                'params' => array('moduleId' => 1),
            ),
            array(
                'title' => 'api/user: user/setting 设置接口',
                'route' => 'user/setting',
                'params' => array('setting' => "{'body': {'settingInfo': {'hidden': '0', 'deviceToken': '10769850486f002f7b67c3bc709a654bc9bd5db6a0eeba7f7238daa4f24a10ea'}}}"),
            ),
            array(
                'title' => 'api/user: user/getsetting 获取设置',
                'route' => 'user/getsetting',
                'params' => array('getSetting' => "{'body': {'postInfo': {'forumIds': '0'}}}"),
            ),
            array(
                'title' => 'api/user: user/vote 投票',
                'route' => 'user/vote',
                'params' => array('tid'=>101, 'options'=>61),
            ),
            array(
                'title' => 'api/user: user/login 登陆',
                'route' => 'user/login',
                'params' => array('type' => 'login', 'username' => 'admin', 'password'=>'admin'),
            ),
            array(
                'title' => 'api/user: user/useradminview 用户管理',
                'route' => 'user/useradminview',
                'params' => array('uid' => 2, 'act' => 'add'),
            ),
            array(
                'title' => 'api/user: user/uploadavatarex 上传头像',
                'route' => 'user/uploadavatarex',
                'params' => array(),
            ),
            array(
                'title' => 'api/user: user/saveavatar 上传头像',
                'route' => 'user/saveavatar',
                'params' => array('avatar' => ''),
            ),
            array(
                'title' => 'api/message: message/heart 心跳',
                'route' => 'message/heart',
                'params' => array(),
            ),
            array(
                'title' => 'api/message: message/notifylist 提醒列表',
                'route' => 'message/notifylist',
                'params' => array('type' => 'post', 'page' => 1, 'pageSize' => 20),
            ),
            array(
                'title' => 'api/message: message/pmsessionlist 私信总会话列表',
                'route' => 'message/pmsessionlist',
                'params' => array('json' => '{"page"=>1, "pageSize"=>10}'),
            ),
            array(
                'title' => 'api/message: message/pmlist 私信会话列表',
                'route' => 'message/pmlist',
                'params' => array('pmlist' => '{"body": {"pmInfos": [{"fromUid": 2, "startTime": "0", "stopTime": "0", "cacheCount": 0, "pmLimit": 10, }], "externInfo": {"onlyFromUid":0} } }'),
            ),
            array(
                'title' => 'api/message: message/pmadmin 私信管理',
                'route' => 'message/pmadmin',
                'params' => array('json' => "{'action': 'send', 'toUid': 2, 'plid': 0, 'pmid': 0, 'msg': {'type': 'text', 'content': 'http://localhost/31g/mobcent/app/data/phiz/default/10.png', }, }"),
            ),
            array(
                'title' => 'api/app: app/initui 初始化AppUI',
                'route' => 'app/initui',
                'params' => array(),
            ),
            array(
                'title' => 'api/app: app/moduleconfig 模块配置',
                'route' => 'app/moduleconfig',
                'params' => array('moduleId' => 1),
            ),
            array(
                'title' => 'api/app: app/servernotify 服务器事件通知接口',
                'route' => 'app/servernotify',
                'params' => array('event' => 'updateApp', 'appKey' => ''),
            ),
            array(
                'title' => 'api/app: app/serverupload 服务器上传接口',
                'route' => 'app/serverupload',
                'params' => array('type' => 'add_certfile_apns', 'certfile_apns_passphrase' => '1234'),
            ),
        );
        $this->renderPartial('debug', array('testApiList' => $testApiList));
    } 
}
