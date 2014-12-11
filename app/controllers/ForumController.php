<?php

/**
 * @author  谢建平 <jianping_xie@aliyun.com>  
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class ForumController extends MobcentController  {
    
    public function actions() {
        return array(
            'forumlist' => 'application.controllers.forum.ForumListAction',
            'topiclist' => 'application.controllers.forum.TopicListAction',
            'postlist' => 'application.controllers.forum.PostListAction',
            'sendattachment' => 'application.controllers.forum.SendAttachmentAction',
            'topicactivity' => 'application.controllers.forum.TopicActivityAction',
            'topicactivityview' => 'application.controllers.forum.TopicActivityViewAction',
            'topicadminview' => 'application.controllers.forum.TopicAdminViewAction',
            'topicadmin' => 'application.controllers.forum.TopicAdminAction',
            'search' => 'application.controllers.forum.SearchAction',
            'photogallery' => 'application.controllers.forum.PhotoGalleryAction',
            'atuserlist' => 'application.controllers.forum.AtUserListAction',
            'announcement' => 'application.controllers.forum.AnnouncementAction',
            'support' => 'application.controllers.forum.SupportAction',
            'vote' => 'application.controllers.forum.VoteAction',
            'topicrate' => 'application.controllers.forum.TopicRateAction',
            'ratelistview' => 'application.controllers.forum.RateListViewAction',
            'classification' => 'application.controllers.forum.ClassificationAction',
            'sendattachmentex' => 'application.controllers.forum.SendAttachmentExAction',            
            'savealbum' => 'application.controllers.forum.saveAlbumAction',            
        );
    }

    protected function mobcentAccessRules() {
        return array(
            'forumlist' => false,
            'topiclist' => false,
            'postlist' => false,
            'sendattachment' => true,
            'topicactivity' => true,
            'topicactivityview' => true,
            'topicadminview' => true,
            'updatecache' => false,
            'topicadmin' => true,
            'search' => false,
            'photogallery' => false,
            'atuserlist' => true,
            'announcement' => false,
            'support' => true,
            'vote' => false,
            'topicrate' => true,
            'ratelistview' => false,
            'classification' => true,
            'sendattachmentex' => true,    
            'savealbum' => true,    
        );
    }
}