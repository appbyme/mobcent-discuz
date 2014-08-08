<?php

/**
 * core
 *
 * 在 DISCUZ_ROOT/source/function_filecore.php 基础上做了改动
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @author hongliang
 * @copyright 2012-2014 Appbyme
 */

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

/**
 * 修改原cknewuser方法
 * 
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @param int $return 1为返回bool, 0为返回错误message
 * @return bool|string
 */
function mobcent_cknewuser($return=0) {
    global $_G;

    $result = true;

    if(!$_G['uid']) return true;

    if(checkperm('disablepostctrl')) {
        return empty($return) ? '' : $result;
    }
    $ckuser = $_G['member'];

    if($_G['setting']['newbiespan'] && $_G['timestamp']-$ckuser['regdate']<$_G['setting']['newbiespan']*60) {
        if(empty($return)) {
            // showmessage('no_privilege_newbiespan', '', array('newbiespan' => $_G['setting']['newbiespan']), array());
            return lang('message', 'no_privilege_newbiespan', array('newbiespan' => $_G['setting']['newbiespan']));
        } 
            
        $result = false;
    }
    if($_G['setting']['need_avatar'] && empty($ckuser['avatarstatus'])) {
        if(empty($return)) {
            // showmessage('no_privilege_avatar', '', array(), array());
            return lang('message', 'no_privilege_avatar');
        }
        $result = false;
    }
    if($_G['setting']['need_email'] && empty($ckuser['emailstatus'])) {
        if(empty($return)) {
            // showmessage('no_privilege_email', '', array(), array());
            return lang('message', 'no_privilege_email');
        }
        $result = false;
    }
    if($_G['setting']['need_friendnum']) {
        space_merge($ckuser, 'count');
        if($ckuser['friends'] < $_G['setting']['need_friendnum']) {
            if(empty($return)) {
                // showmessage('no_privilege_friendnum', '', array('friendnum' => $_G['setting']['need_friendnum']), array());
                return lang('message', 'no_privilege_friendnum', array('friendnum' => $_G['setting']['need_friendnum']));
            }
            $result = false;
        }
    }
    return empty($return) ? '' : $result;
}

function mobcent_updatecreditbyaction($action, $uid = 0, $extrasql = array(), $needle = '', $coef = 1, $update = 1, $fid = 0) {
    Mobcent::import(MOBCENT_APP_ROOT . '/components/discuz/source/class/class_credit.php');
    $credit = new Mobcent_class_credit();
    if($extrasql) {
        $credit->extrasql = $extrasql;
    }
    return $credit->execrule($action, $uid, $needle, $coef, $update, $fid);
}

function mobcent_batchupdatecredit($action, $uids = 0, $extrasql = array(), $coef = 1, $fid = 0) {
    Mobcent::import(MOBCENT_APP_ROOT . '/components/discuz/source/class/class_credit.php');
    $credit = new Mobcent_class_credit();
    //$credit = & credit::instance();
    if($extrasql) {
        $credit->extrasql = $extrasql;
    }
    return $credit->updatecreditbyrule($action, $uids, $coef, $fid);
}