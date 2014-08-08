<?php

define('APPBYME_DZ_PLUGIN_ID', 'appbyme_app');

$scriptlang[APPBYME_DZ_PLUGIN_ID] = array(
    'mobcent_tips_updatecache' => '
        <li>当客户端需要加快访问速度, 或者数据需要更新时, 您可以使用本功能重新生成缓存。更新缓存的时候，可能让服务器负载升高，请尽量避开会员访问的高峰时间</li>
        <li>由于生成时给服务器的压力会特别大，生成缩略图的时候请一定要设置好数量(0为不更新), 还要选择好时间</li>
    ',
    'mobcent_clean_datacache' => '清理数据缓存',
    'mobcent_update_datacache' => '更新数据缓存',
    'mobcent_clean_thumbcache' => '清理缩略图缓存',
    'mobcent_thumb_task_length_setting' => '设置缩略图任务数',
    'mobcent_thumb_task_length' => '当前缩略图后台任务数',
    'mobcent_tips_portal_module_source' => '
        <li>版块id与文章栏目id不能同时存在, 只能二者选一</li>
        <li>当 文章id/帖子id 与版块id/文章栏目id 都有存在时, 文章id/帖子id 会类似置顶的效果，排在 版块id/文章栏目id 数据前面</li>
    ',
    'mobcent_tips_portal_module_source_add' => '(可添加多个id,用`,`隔开)',
    'mobcent_tips_extcredit_base' => '奖励基数, 客户端会在本来奖励的基础上乘以该数字, 请注意，你只能输入数字, 并且1代表0.01, 100代表1, 即该数会在设置的基础上乘以0.01',
    'mobcent_error_not_installed' => '请先上传好安米网dz转换接口包!!!',
    'mobcent_error_portal_not_allow' => '请先打开启用门户模块配置项!!!',
    'mobcent_error_portal_module_param' => '此项仅适用于在模块内容设置里的内容来源类别里面设置了版块id或者文章栏目id',
    'mobcent_error_time_invalid' => '抱歉，您输入的时间格式不正确!',
    'mobcent_forum_style_setting' => '版块显示样式设置',
    'mobcent_forum_column_style_col1' => '单列显示',
    'mobcent_forum_column_style_col2' => '双列显示',
    'mobcent_forum_edit_succeed' => '论坛更新成功',
    'mobcent_portal_edit_succeed' => '门户更新成功',
    'mobcent_seo_edit_succeed' => 'SEO更新成功',
    'mobcent_operation_sign_extcredit_base' => '签到奖励基数设置',
    'mobcent_operation_forum_extcredit_base' => '发回帖奖励基数设置',
    'mobcent_operation_edit_succeed' => '运营更新成功',
    'mobcent_operation_setting' => '运营设置',
    'mobcent_portal_module_setting' => '门户模块设置',
    'mobcent_portal_module_name' => '模块名称(必填)',
    'mobcent_portal_module_source' => '内容来源',
    'mobcent_portal_module_slider' => '图片来源',
    'mobcent_portal_module_source_edit' => '模块内容设置',
    'mobcent_portal_module_slider_edit' => '幻灯片设置',
    'mobcent_portal_module_param_edit' => '模块参数设置',
    'mobcent_portal_module_param_topic_digest' => '精华主题选择,精华 I,精华 II,精华 III,普通主题',
    'mobcent_portal_module_param_topic_stick' => '置顶主题选择,置顶 I,置顶 II,置顶 III,普通主题',
    'mobcent_portal_module_param_topic_special' => '特殊主题选择,投票主题,商品主题,悬赏主题,活动主题,辩论主题,普通主题',
    'mobcent_portal_module_param_topic_picrequired' => '必须含图片附件',
    'mobcent_portal_module_param_topic_orderby' => '主题排序方式,按最后回复时间倒序排序,按发布时间倒序排序,按回复数倒序排序,按浏览次数倒序排序,按热度倒序排序,按主题评价倒序排序',
    'mobcent_portal_module_param_topic_postdateline' => '主题发布时间',
    'mobcent_portal_module_param_topic_lastpost' => '最后更新时间',
    'mobcent_portal_module_param_time' => '不限制,1小时内,24小时内,7天内,1个月内',
    'mobcent_portal_module_param_style' => '显示样式',
    'mobcent_portal_module_param_topic_style' => '发帖时间,最后回复时间',
    'mobcent_portal_module_param_article_picrequired' => '过滤无封面文章',
    'mobcent_portal_module_param_article_starttime' => '发布时间-起始',
    'mobcent_portal_module_param_article_endtime' => '发布时间-结束',
    'mobcent_portal_module_param_article_ordby' => '文章排序方式,按发布时间倒序,按查看数倒序,按评论数倒序',
    'mobcent_portal_module_param_article_publishdateline' => '文章发布时间',
    'mobcent_portal_module_source_type' => '内容来源类别',
    'mobcent_portal_module_source_type_aid' => '文章id',
    'mobcent_portal_module_source_type_tid' => '帖子id',
    'mobcent_portal_module_source_type_fid' => '版块id',
    'mobcent_portal_module_source_type_catid' => '文章栏目id',
    'mobcent_portal_module_slider_type' => '图片来源类别',
    'mobcent_portal_module_slider_title' => '幻灯片标题',
    'mobcent_infomation_appbyme' => '安米mobcent.zip转换接口包信息',
    'mobcent_version_user' => '当前使用的转换接口包版本',
    'mobcent_version_appbyme' => '安米网最新转换接口包版本',
    'appbyme_seo_title_download' => '下载页面',
);

$templatelang[APPBYME_DZ_PLUGIN_ID] = array(
    'appbyme_qrcode_install' => '扫描二维码安装',
    'appbyme_android_install' => '安卓版下载',
    'appbyme_apple_install' => 'iPhone版下载',
);

$installlang[APPBYME_DZ_PLUGIN_ID] = array(
);
