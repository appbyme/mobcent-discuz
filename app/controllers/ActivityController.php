<?php 

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class ActivityController extends MobcentController
{    
    public function actions()
    {
        return array(
            'inviteactivity' => 'application.controllers.activity.InviteActivityAction',
            'invitecheck' => 'application.controllers.activity.InviteCheckAction',
            'inviteexchange' => 'application.controllers.activity.InviteExchangeAction',
        );
    }

    protected function mobcentAccessRules() 
    {
        return array(
            'inviteactivity' => false,
            'invitecheck' => true,
            'inviteexchange' => true,
        );
    }
}

?>