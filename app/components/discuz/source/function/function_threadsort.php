<?php
/**
 *
 *
 * 在 DISCUZ_ROOT/source/function_threadsort.php 基础上做了改动
 *
 * @author hongliang
 * @copyright 2012-2014 Appbyme
 */

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
function mobcent_threadsort_validator($sortoption, $pid) {
    global $_G, $var;
    $postaction = $_G['tid'] && $pid ? "edit&tid=$_G[tid]&pid=$pid" : 'newthread';
    $_G['forum_optiondata'] = array();
    foreach($_G['forum_checkoption'] as $var => $option) {
        if($_G['forum_checkoption'][$var]['required'] && ($sortoption[$var] === '' && $_G['forum_checkoption'][$var]['type'] != 'number')) {
            return array('message'=>'threadtype_required_invalid','params'=>array('{typetitle}' => $_G['forum_checkoption'][$var]['title']));
            //showmessage('threadtype_required_invalid', "forum.php?mod=post&action=$postaction&fid=$_G[fid]&sortid=".$_G['forum_selectsortid'], array('typetitle' => $_G['forum_checkoption'][$var]['title']));
        } elseif($sortoption[$var] && ($_G['forum_checkoption'][$var]['type'] == 'number' && !is_numeric($sortoption[$var]) || $_G['forum_checkoption'][$var]['type'] == 'email' && !isemail($sortoption[$var]))){
            return array('message'=>'threadtype_required_invalid','params'=>array('{typetitle}' => $_G['forum_checkoption'][$var]['title']));
            //showmessage('threadtype_format_invalid', "forum.php?mod=post&action=$postaction&fid=$_G[fid]&sortid=".$_G['forum_selectsortid'], array('typetitle' => $_G['forum_checkoption'][$var]['title']));
        } elseif($sortoption[$var] && $_G['forum_checkoption'][$var]['maxlength'] && strlen($sortoption[$var]) > $_G['forum_checkoption'][$var]['maxlength']) {
            return array('message'=>'threadtype_toolong_invalid','params'=>array('{typetitle}' => $_G['forum_checkoption'][$var]['title']));
           // showmessage('threadtype_toolong_invalid', "forum.php?mod=post&action=$postaction&fid=$_G[fid]&sortid=".$_G['forum_selectsortid'], array('typetitle' => $_G['forum_checkoption'][$var]['title']));
        } elseif($sortoption[$var] && (($_G['forum_checkoption'][$var]['maxnum'] && $sortoption[$var] > $_G['forum_checkoption'][$var]['maxnum']) || ($_G['forum_checkoption'][$var]['minnum'] && $sortoption[$var] < $_G['forum_checkoption'][$var]['minnum']))) {
            return array('message'=>'threadtype_num_invalid','params'=>array('{typetitle}' => $_G['forum_checkoption'][$var]['title']));
           // showmessage('threadtype_num_invalid', "forum.php?mod=post&action=$postaction&fid=$_G[fid]&sortid=".$_G['forum_selectsortid'], array('typetitle' => $_G['forum_checkoption'][$var]['title']));
        } elseif($sortoption[$var] && $_G['forum_checkoption'][$var]['unchangeable'] && !($_G['tid'] && $pid)) {
            return array('message'=>'threadtype_unchangeable_invalid','params'=>array('{typetitle}' => $_G['forum_checkoption'][$var]['title']));
            //showmessage('threadtype_unchangeable_invalid', "forum.php?mod=post&action=$postaction&fid=$_G[fid]&sortid=".$_G['forum_selectsortid'], array('typetitle' => $_G['forum_checkoption'][$var]['title']));
        } elseif($sortoption[$var] && ($_G['forum_checkoption'][$var]['type'] == 'select')) {
            if($_G['forum_optionlist'][$_G['forum_checkoption'][$var]['optionid']]['choices'][$sortoption[$var]]['level'] != 1) {
                //判断下拉选择项的必填项是否填写，如果为多级连动选择客户端会发不出去帖子，注释此行
            	//return array('message'=>'threadtype_select_invalid','params'=>array('{typetitle}' => $_G['forum_checkoption'][$var]['title']));
               // showmessage('threadtype_select_invalid', "forum.php?mod=post&action=$postaction&fid=$_G[fid]&sortid=".$_G['forum_selectsortid'], array('typetitle' => $_G['forum_checkoption'][$var]['title']));
            }
        }
        if($_G['forum_checkoption'][$var]['type'] == 'checkbox') {
            $sortoption[$var] = $sortoption[$var] ? implode("\t", $sortoption[$var]) : '';
        } elseif($_G['forum_checkoption'][$var]['type'] == 'url') {
            $sortoption[$var] = $sortoption[$var] ? (substr(strtolower($sortoption[$var]), 0, 4) == 'www.' ? 'http://'.$sortoption[$var] : $sortoption[$var]) : '';
        }

        if($_G['forum_checkoption'][$var]['type'] == 'image') {
            if($sortoption[$var]['aid']) {
                $_GET['attachnew'][$sortoption[$var]['aid']] = $sortoption[$var];
            }
            $sortoption[$var] = serialize($sortoption[$var]);
        } elseif($_G['forum_checkoption'][$var]['type'] == 'select') {
            $sortoption[$var] = censor(trim($sortoption[$var]));
        } else {
            $sortoption[$var] = dhtmlspecialchars(censor(trim($sortoption[$var])));
        }
        $_G['forum_optiondata'][$_G['forum_checkoption'][$var]['optionid']] = $sortoption[$var];
    }

    return $_G['forum_optiondata'];
}