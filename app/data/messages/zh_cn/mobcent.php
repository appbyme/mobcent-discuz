<?php

return array(
    'do_success' => WebUtils::t('00000000:操作成功 '),

    'error_custom' => WebUtils::t('11100001: {customMsg}'),
    'mobcent_error_params' => WebUtils::t('11100002: 客户端参数不正确'),

    // 论坛版块 errcode: 001xxxxx
    'to_login' => WebUtils::t('00100001:您需要先登录才能继续本操作'),

    'group_nopermission' => WebUtils::t('00100100:抱歉，您所在的用户组({grouptitle})无法进行此操作'),

    'forum_access_view_disallow' => WebUtils::t('00100102:抱歉，您在本版没有权限进行此操作'),
    'forum_nonexistence' => WebUtils::t('00200001:抱歉，指定的版块不存在'),
    'forum_permforum_nomedal' => WebUtils::t("00200002:您需要拥有以下勋章才能访问这个版块\n\n{forum_permforum_nomedal} "),
    'forum_permforum_disallow' => WebUtils::t('00200003:本版块只有特定用户可以访问'),
    'forum_permforum_nopermission' => WebUtils::t("00200004:您需要满足以下条件才能访问这个版块\n访问条件:\n{formulamessage}\n您的信息:{usermsg} "),
    'forum_permforum_nopermission_custommsg' => WebUtils::t('{formulamessage} '),

    // 'mobcent_forum_passwd' => WebUtils::t('本版块需要密码，您必须在下面输入正确的密码才能浏览这个版块'),
    'mobcent_forum_passwd' => WebUtils::t('本版块需要密码，暂时不支持直接访问，请见谅'),

    'viewperm_none_nopermission' => WebUtils::t('00200100:抱歉，您没有权限访问该版块'),
    'viewperm_upgrade_nopermission' => WebUtils::t("00200101:抱歉，您需要升级您所在的用户组后才能访问该版块,\n有权访问的用户组或认证用户为:\n   {permgroups}"),
    'viewperm_login_nopermission' => WebUtils::t('00200102:抱歉，您尚未登录，没有权限访问该版块'),

    'thread_nonexistence' => WebUtils::t('00300001:抱歉，指定的主题不存在或已被删除或正在被审核'),
    'thread_nopermission' => WebUtils::t('00300002:抱歉，本帖要求阅读权限高于 {readperm} 才能浏览'),

    // 活动帖 errcode: 004xxxxx
    'activity_apply_params_error' => WebUtils::t('00400001:活动帖请求参数错误'),
    'activity_stop' => WebUtils::t('00400002:抱歉，活动已停止申请'),
    'activity_repeat_apply' => WebUtils::t('00400003:抱歉，活动不能重复申请'),
    'activity_exile_field' => WebUtils::t('00400004:带 "*" 号为必填项，请填写完整'),
    'activity_completion' => WebUtils::t('00000000:活动申请成功 '),
    'activity_cancel_success' => WebUtils::t('00000000:活动报名取消成功'),

    'UPLOAD_ATTACHMENT_ERROR' => WebUtils::t('01000001: {attachment} 上传失败'),

    'post_thread_closed' => WebUtils::t('00500000:抱歉，本主题已关闭，不再接受新内容'),
    'forum_passwd' => WebUtils::t('00500001:本版块需要密码'),
    'post_newbie_span' => WebUtils::t('00500002:抱歉，您在注册时间起 {newbiespan} 分钟后才能拥有发帖权限'),
    'post_hide_nopermission' =>  WebUtils::t('00500003:抱歉，您没有权限使用 [hide] 代码'),
    'postperm_qqonly_nopermission' => WebUtils::t('00500004:为避免您的帐号被盗用，请您绑定QQ帐号后发帖，绑定后请使用QQ帐号登录'),
    'postperm_login_nopermission' => WebUtils::t('00500005:抱歉，您尚未登录，没有权限在该版块发帖'),
    'postperm_login_nopermission_mobile' => WebUtils::t('00500006:您尚未登录，没有权限在该版块发帖'),
    'postperm_none_nopermission' =>  WebUtils::t('00500007:抱歉，您没有权限在该版块发帖'),
    'post_forum_newthread_nopermission' => WebUtils::t('00500008:抱歉，本版块只有特定用户组可以发新主题'),
    'replyperm_login_nopermission' => WebUtils::t('00500009:抱歉，您尚未登录，没有权限在该版块回帖'),
    'replyperm_none_nopermission' => WebUtils::t('00500010:抱歉，您没有权限在该版块回帖'),
    'post_nonexistence' => WebUtils::t('00500010:帖子不存在'),
    'post_flood_ctrl' => WebUtils::t('00500011:抱歉，您两次发表间隔少于 {floodctrl} 秒，请稍候再发表'),
    'post_flood_ctrl_posts_per_hour' => WebUtils::t('00500012:抱歉，您所在的用户组每小时限制发回帖 {posts_per_hour} 个，请稍候再发表'),
    'post_thread_closed_by_dateline' => WebUtils::t('00500013:抱歉，管理员设置了本版块发表于 {autoclose} 天以前的主题自动关闭，不再接受新回复'),
    'post_thread_closed_by_lastpost' => WebUtils::t('00500014:抱歉，管理员设置了本版块最后回复于 {autoclose} 天以前的主题自动关闭，不再接受新回复'),
    'forum_access_disallow' => WebUtils::t('00500015:抱歉，您在本版没有权限进行此操作'),
    'reply_quotepost_error' => WebUtils::t('00500016:禁止引用自己和主题帖之外的帖子'),
    'post_sm_isnull' =>  WebUtils::t('00500017:抱歉，您尚未输入标题或内容'),
    'thread_flood_ctrl_threads_per_hour' => WebUtils::t('00500018:抱歉，您所在的用户组每小时限制发主题 {threads_per_hour} 个，请稍候再发表'),
    'post_type_isnull' => WebUtils::t('00500019:抱歉，您尚未选择主题的类别'),
    'post_sort_isnull' => WebUtils::t('00500020:抱歉，您尚未选择主题的分类信息'),
    'threadtype_expiration_invalid' => WebUtils::t('00500021:抱歉，此主题必须指定有效期'),
    'post_newthread_mod_succeed' => WebUtils::t('00500022:新主题需要审核，您的帖子通过审核后才能显示'),
    'post_forum_newreply_nopermission' => WebUtils::t('00500023:抱歉，本版块只有特定用户组可以回复'),
    'post_message_tooshort' => WebUtils::t('00500024:抱歉，您的帖子小于 {minpostsize} 个字符的限制'),
    'post_subject_toolong' =>  WebUtils::t('00500025:抱歉，您的标题超过 80 个字符修改标题长度'),
    'post_message_toolong' => WebUtils::t('00500026:抱歉，您的帖子超过 {maxpostsize} 个字符的限制'),
    'post_reply_mod_succeed' => WebUtils::t('00500027:回复需要审核，请等待通过'),
    'replyperm_upgrade_nopermission' => WebUtils::t('00500028:抱歉，您需要升级所在的用户组后才能回帖'),
    'post_not_found' => WebUtils::t('00600000:没有找到帖子'),

    // 文章评论 errcode: 010xxxxx
    'operating_too_fast' => WebUtils::t('01000001:抱歉，两次发布操作太快，请等待 {waittime} 秒再试'),
    'comment_comment_noexist' => WebUtils::t('01000002:抱歉，要评论的文章不存在'),
    'content_is_too_short' => WebUtils::t('01000003:抱歉，输入的内容不能少于 2 个字符'),
    'comment_comment_notallowed' => WebUtils::t('01000004:该文章不允许评论'),
    'comment_no_aid_id' => WebUtils::t('01000005:抱歉，您尚未正确指定要查看的文章 ID'),
    'comment_no_topicid_id' => WebUtils::t('01000006:抱歉，您尚未正确指定要查看的专题 ID'),
    'comment_aid_no_exist' => WebUtils::t('01000007:抱歉，您指定要查看的文章不存在'),
    'comment_topicid_no_exist' => WebUtils::t('01000008:抱歉，您指定要查看的专题不存在'),
    'aid_comment_is_forbidden' => WebUtils::t('01000009:抱歉，您指定要查看的文章禁止评论'),
    'topicid_comment_is_forbidden' => WebUtils::t('01000010:抱歉，您指定要查看的专题禁止评论'),

    // search: 011xxxxx
    'search_forum_closed' => WebUtils::t('01100001:抱歉，论坛搜索已关闭'),
    'search_no_results' => WebUtils::t('01100002:对不起，没有找到匹配结果'),
    'search_ctrl' => WebUtils::t('01100003:抱歉，您在 {searchctrl} 秒内只能进行一次搜索'),
    'search_toomany' => WebUtils::t('01100004:抱歉，站点设置每分钟系统最多响应搜索请求 {maxspm} 次，请稍候再试'),
    'search_id_invalid' => WebUtils::t('01100005:指定的搜索不存在或已过期'),
    'faq_keywords_empty' => WebUtils::t('01100006:抱歉，您尚未指定要搜索的关键字'),

    // 主题分类 errcode: 013xxxxx
    'threadtype_required_invalid' => WebUtils::t('01300002:抱歉，资料填写不全，请检查 {typetitle} 选项'),
    'threadtype_toolong_invalid' => WebUtils::t('01300003:抱歉，资料长度过长，请检查 {typetitle} 选项'),
    'threadtype_num_invalid' =>  WebUtils::t('01300004:抱歉，资料数值不正确，请检查 {typetitle} 选项'),
    'threadtype_unchangeable_invalid' =>  WebUtils::t('01300005:抱歉，资料不得修改，请检查{typetitle}选项'),
	'threadtype_select_invalid' =>  WebUtils::t('01300006:抱歉，必填项目没有填写'),
		

    // 用户中心 errcode: 020xxxxx
    'space_does_not_exist' => WebUtils::t('02000001:抱歉，您指定的用户空间不存在'),
    'to_view_the_photo_does_not_exist' => WebUtils::t('02000002:抱歉，您要查看的相册不存在或正在审核'),
    'to_view_the_photo_set_privacy' => WebUtils::t('02000003:抱歉！由于 {username} 的隐私设置，您不能访问当前内容'),
    'to_view_the_defaultAlbum_does_not_exist' => WebUtils::t('02000004:该相册下还没有图片'),
    'no_privilege_sendpm' => WebUtils::t('02000010:抱歉，您目前没有权限发短消息'),

    'follow_not_follow_self' => WebUtils::t('02000020:不能关注自己'),
    'follow_other_unfollow' => WebUtils::t('02000021:对方不允许您关注TA'),
    'follow_followed_ta' => WebUtils::t('02000022:您已经收听了TA'),
    'follow_add_succeed' => WebUtils::t('02000023:成功收听'),
    'follow_cancel_succeed' => WebUtils::t('02000024:取消成功'),
    'space_does_not_exist' => WebUtils::t('02000026:抱歉，您指定的用户空间不存在'),
    'unable_to_manage_self' => WebUtils::t('02000027:抱歉，您不能对自己进行操作'),
    'favorite_cannot_favorite' => WebUtils::t('02000028:抱歉，您指定的信息无法收藏'),
    'favorite_repeat' => WebUtils::t('02000029:抱歉，您已收藏，请勿重复收藏'),
    'favorite_do_success' => WebUtils::t('02000030:信息收藏成功'),
    'favorite_does_not_exist' => WebUtils::t('02000031:抱歉，您指定的收藏不存在'),

    'user_info_avatar_error' => WebUtils::t('03000001:用户头像保存失败'),
    'user_name_null' => WebUtils::t('03000001:输入的用户名为空'),
    'location_activation' => WebUtils::t('03000001:当前用户需要激活,请到pc端激活.'),

    // 用户举报 errcode: 040xxxxx
    'report_parameters_invalid' => WebUtils::t('04000001:页面参数错误，暂不能举报'),
    'report_succeed' => WebUtils::t('04000002:举报成功'),

    // 公告 errcode: 050xxxxx
    'announcement_nonexistence' => WebUtils::t('05000001:抱歉，目前没有公告供查看'),    
);