<?php 

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class AppbymeActivityModel extends DiscuzAR{

    public static function insertActivity($data) {
        return DbUtils::getDzDbUtils(true)->insert('appbyme_activity', $data);
    }

    public static function getActivityInvite() {
        return DbUtils::getDzDbUtils(true)->queryAll('
            SELECT *
            FROM %t
            ',
            array('appbyme_activity')
        );
    }
    
}
    
?>