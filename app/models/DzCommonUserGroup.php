<?php

/**
 * 用户组 model类
 *
 * @author 谢建平 <xiejianping@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

class DzCommonUserGroup extends DiscuzAR {
    
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return '{{common_usergroup}}';
    }

    public function rules() {
        return array(
        );
    }

    public static function getAllowVisitGids() {
        return DbUtils::getDzDbUtils(true)->queryColumn('
            SELECT groupid 
            FROM %t 
            WHERE allowvisit>0', 
            array('common_usergroup')
        );
    }

    public static function getUserGroupsByGids($gids) {
        $gids = ArrayUtils::explode($gids);
        $usergroupCacheNames = array();
        foreach ($gids as $gid) {
            $usergroupCacheNames[$gid] = 'usergroup_'.$gid;
        }
        loadcache($usergroupCacheNames);
        
        $usergroups = array();
        global $_G;
        foreach ($usergroupCacheNames as $gid => $usergroup) {
            $usergroups[$gid] = is_array($_G['cache'][$usergroup]) ? $_G['cache'][$usergroup] : array();
        }
        return $usergroups;
    }
}