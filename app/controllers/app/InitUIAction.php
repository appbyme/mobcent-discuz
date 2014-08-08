<?php

/**
 * 初始化 App UI接口
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class InitUIAction extends MobcentAction
{
    public function run()
    {
        $res = $this->initWebApiArray();
        $res['body'] = $this->_getUIconfig();
        echo WebUtils::outputWebApi($res, '', false);
    }

    private function _getUIconfig($res)
    {
        return array(
            'navigation' => array(
                'type' => 'bottom',
                'moduleList' => array(1, 2, 3, 4, 5),
            ),
            'moduleList' => array(
                array(
                    'type' => 'subnav',
                    'id' => 1,
                    'title' => WebUtils::t('首页'),
                    'icon' => 'http://temp.png',
                    'leftTopbars' => array(
                        array(
                            'id' => 1,
                            'type' => 'weather',
                            'title' => WebUtils::t('天气'),
                            'icon' => '',
                            'style' => 'default',
                            'extParams' => array(
                                'padding' => '',
                            ),
                        )
                    ),
                    'rightTopbars' => array(
                        array(
                            'id' => 1,
                            'type' => 'userinfo',
                            'title' => WebUtils::t('用户中心'),
                            'icon' => '',
                            'style' => 'default',
                            'extParams' => array(
                                'padding' => '',
                            ),
                        )  
                    ),
                    'layoutList' => array(
                        array(
                            'type' => 'default',
                            'ratio' => 0.0,
                            'componentList' => array(
                                array(
                                    'id' => 1,
                                    'type' => 'newslist',
                                    'title' => WebUtils::t('门户1'),
                                    'desc' => '',
                                    'icon' => 'http://news.png',
                                    'style' => 'flat',
                                    'extParams' => array(
                                        'padding' => '',
                                    ),
                                ),
                                array(
                                    'id' => 2,
                                    'type' => 'newslist',
                                    'title' => WebUtils::t('门户2'),
                                    'desc' => '',
                                    'icon' => 'http://news.png',
                                    'style' => 'flat',
                                    'extParams' => array(
                                        'padding' => '',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'extParams' => array('padding' => ''),
                ),
                array(
                    'type' => 'subnav',
                    'id' => 2,
                    'title' => WebUtils::t('社区'),
                    'icon' => 'http://module.png',
                    'leftTopbars' => array(
                    ),
                    'rightTopbars' => array(
                        array(
                            'id' => 1,
                            'type' => 'search',
                            'title' => WebUtils::t('搜索'),
                            'desc' => '',
                            'icon' => '',
                            'style' => 'default',
                            'extParams' => array(
                                'padding' => '',
                            ),
                        )  
                    ),
                    'layoutList' => array(
                        array(
                            'type' => 'default',
                            'ratio' => 0.0,
                            'componentList' => array(
                                array(
                                    'id' => 3,
                                    'type' => 'forumlist',
                                    'title' => WebUtils::t('版块'),
                                    'desc' => '',
                                    'icon' => 'http://news.png',
                                    'style' => 'default',
                                    'extParams' => array(
                                        'padding' => '',
                                    ),
                                ),
                                array(
                                    'id' => 4,
                                    'type' => 'newslist',
                                    'title' => WebUtils::t('最新'),
                                    'desc' => '',
                                    'icon' => 'http://news.png',
                                    'style' => 'card',
                                    'extParams' => array(
                                        'padding' => '',
                                    ),
                                ),
                                array(
                                    'id' => 5,
                                    'type' => 'newslist',
                                    'title' => WebUtils::t('精华'),
                                    'desc' => '',
                                    'icon' => 'http://news.png',
                                    'style' => 'flat',
                                    'extParams' => array(
                                        'padding' => '',
                                    ),
                                ),
                                array(
                                    'id' => 6,
                                    'type' => 'newslist',
                                    'title' => WebUtils::t('图片'),
                                    'desc' => '',
                                    'icon' => 'http://news.png',
                                    'style' => 'image',
                                    'extParams' => array(
                                        'padding' => '',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'id' => 3,
                    'type' => 'onelink',
                    'title' => WebUtils::t('快速发帖'),
                    'icon' => 'http://temp.png',
                    'leftTopbars' => array(
                    ),
                    'rightTopbars' => array(
                    ),
                    'layoutList' => array(
                        array(
                            'type' => 'default',
                            'ratio' => 0.0,
                            'componentList' => array(
                                array(
                                    'id' => 5,
                                    'type' => 'fastpost',
                                    'title' => WebUtils::t('文字'),
                                    'desc' => '',
                                    'icon' => 'http://news.png',
                                    'style' => '',
                                    'extParams' => array(
                                        'padding' => '',
                                    ),
                                ),
                                array(
                                    'id' => 6,
                                    'type' => 'fastimage',
                                    'title' => WebUtils::t('图片'),
                                    'desc' => '',
                                    'icon' => 'http://news.png',
                                    'style' => 'image',
                                    'extParams' => array(
                                        'padding' => '',
                                    ),
                                ),
                                array(
                                    'id' => 6,
                                    'type' => 'fastcamera',
                                    'title' => WebUtils::t('拍照'),
                                    'desc' => '',
                                    'icon' => 'http://news.png',
                                    'style' => 'image',
                                    'extParams' => array(
                                        'padding' => '',
                                    ),
                                ),
                                array(
                                    'id' => 6,
                                    'type' => 'sign',
                                    'title' => WebUtils::t('签到'),
                                    'desc' => '',
                                    'icon' => 'http://news.png',
                                    'style' => 'image',
                                    'extParams' => array(
                                        'padding' => '',
                                    ),
                                ),
                                array(
                                    'id' => 6,
                                    'type' => 'fastaudio',
                                    'title' => WebUtils::t('语音'),
                                    'desc' => '',
                                    'icon' => 'http://news.png',
                                    'style' => 'image',
                                    'extParams' => array(
                                        'padding' => '',
                                    ),
                                ),
                            ),
                        ),
                    ),
                    'extParams' => array(
                        'padding' => '',
                    ),
                ),
                array(
                    'type' => 'full',
                    'id' => 4,
                    'title' => WebUtils::t('消息'),
                    'icon' => 'http://temp.png',
                    'leftTopbars' => array(
                    ),
                    'rightTopbars' => array(
                    ),
                    'layoutList' => array(
                        array(
                            'type' => 'default',
                            'ratio' => 0.0,
                            'componentList' => array(
                                array(
                                    'id' => 7,
                                    'type' => 'messagelist',
                                    'title' => WebUtils::t('消息'),
                                    'icon' => 'http://news.png',
                                    'desc' => '',
                                    'style' => 'default',
                                    'extParams' => array(
                                        'padding' => '',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
                array(
                    'type' => 'custom',
                    'id' => 5,
                    'title' => WebUtils::t('发现'),
                    'icon' => 'http://temp.png',
                    'layoutList' => array(
                        array(
                            'type' => 'type1',
                            'ratio' => 3.0,
                            'componentList' => array(
                                array(
                                    'id' => 8,
                                    'type' => 'userinfo',
                                    'title' => WebUtils::t('个人中心'),
                                    'icon' => 'http://news.png',
                                    'desc' => '',
                                    'style' => 'default',
                                ),
                                array(
                                    'id' => 9,
                                    'type' => 'setting',
                                    'title' => WebUtils::t('设置'),
                                    'desc' => '',
                                    'icon' => 'http://news.png',
                                    'style' => 'default',
                                ),
                                array(
                                    'id' => 10,
                                    'type' => 'aboat',
                                    'title' => WebUtils::t('关于'),
                                    'desc' => '',
                                    'icon' => 'http://news.png',
                                    'style' => 'default',
                                ),
                            ),
                        ),
                        array(
                            'type' => 'type2',
                            'ratio' => 2.0,
                            'componentList' => array(
                                array(
                                    'id' => 11,
                                    'type' => 'surroudingpost',
                                    'title' => WebUtils::t('周边帖子'),
                                    'desc' => '',
                                    'icon' => 'http://news.png',
                                    'style' => 'flat',
                                ),
                                array(
                                    'id' => 12,
                                    'type' => 'userlist',
                                    'title' => WebUtils::t('周边用户'),
                                    'desc' => '',
                                    'icon' => 'http://news.png',
                                    'style' => 'default',
                                ),
                                array(
                                    'id' => 13,
                                    'type' => 'userlist',
                                    'title' => WebUtils::t('推荐用户'),
                                    'desc' => '',
                                    'icon' => 'http://news.png',
                                    'style' => 'default',
                                ),
                            ),
                        ),
                        array(
                            'type' => 'type2',
                            'ratio' => 2.0,
                            'componentList' => array(
                                array(
                                    'id' => 14,
                                    'type' => 'userlist',
                                    'title' => WebUtils::t('游戏'),
                                    'desc' => '',
                                    'icon' => 'http://news.png',
                                    'style' => 'default',
                                ),
                                array(
                                    'id' => 15,
                                    'type' => 'moduleRef',
                                    'title' => WebUtils::t('指向模块'),
                                    'desc' => '',
                                    'icon' => 'http://news.png',
                                    'style' => 'default',
                                    'extParams' => array(
                                        'redirect' => '1',
                                    ),
                                ),
                                array(
                                    'id' => 16,
                                    'type' => 'webapp',
                                    'title' => WebUtils::t('指向模块'),
                                    'desc' => '',
                                    'icon' => 'http://news.png',
                                    'style' => 'default',
                                    'extParams' => array(
                                        'redirect' => 'http://baidu.com',
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ),
        );
    }
}