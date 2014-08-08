<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

/**
 * 更新发贴回贴奖励方法
 *
 * @author hongliang
 *
 */
function mobcent_updatepostcredits($operator, $uidarray, $action, $fid = 0) {
    global $_G;
    $val = $operator == '+' ? 1 : -1;
    $extsql = array();
    if(empty($uidarray)) {
        return false;
    }
    $uidarray = (array)$uidarray;
    $uidarr = array();
    foreach($uidarray as $uid) {
        $uidarr[$uid] = !isset($uidarr[$uid]) ? 1 : $uidarr[$uid]+1;
    }
    foreach($uidarr as $uid => $coef) {
        $opnum = $val*$coef;
        if($action == 'reply') {
            $extsql = array('posts' => $opnum);
        } elseif($action == 'post') {
            $extsql = array('threads' => $opnum, 'posts' => $opnum);
        }
        if($uid == $_G['uid']) {
            mobcent_updatecreditbyaction($action, $uid, $extsql, '', $opnum, 1, $fid);
        } elseif(empty($uid)) {
            continue;
        } else {
            mobcent_batchupdatecredit($action, $uid, $extsql, $opnum, $fid);
        }
    }
    if($operator == '+' && ($action == 'reply' || $action == 'post')) {
        C::t('common_member_status')->update(array_keys($uidarr), array('lastpost' => TIMESTAMP), 'UNBUFFERED');
    }
}

function mobcent_setthreadcover($pid, $tid = 0, $aid = 0, $countimg = 0, $imgurl = '') {

    global $_G;
    $cover = 0;
    if(empty($_G['uid']) || !intval($_G['setting']['forumpicstyle']['thumbheight']) || !intval($_G['setting']['forumpicstyle']['thumbwidth'])) {
        return false;
    }

    if(($pid || $aid) && empty($countimg)) {
        if(empty($imgurl)) {
            if($aid) {
                $attachtable = 'aid:'.$aid;
                $attach = C::t('forum_attachment_n')->fetch('aid:'.$aid, $aid, array(1, -1));
            } else {
                $attachtable = 'pid:'.$pid;
                $attach = C::t('forum_attachment_n')->fetch_max_image('pid:'.$pid, 'pid', $pid);
            }
            if(!$attach) {
                return false;
            }
            if(empty($_G['forum']['ismoderator']) && $_G['uid'] != $attach['uid']) {
                return false;
            }
            $pid = empty($pid) ? $attach['pid'] : $pid;
            $tid = empty($tid) ? $attach['tid'] : $tid;
            // $picsource = ($attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl']).'forum/'.$attach['attachment'];
            $picsource = ImageUtils::getAttachUrl($attach['remote']).'/'.'forum/'.$attach['attachment'];
        } else {
            $attachtable = 'pid:'.$pid;
            $picsource = $imgurl;
        }

        $basedir = !$_G['setting']['attachdir'] ? (DISCUZ_ROOT.'./data/attachment/') : $_G['setting']['attachdir'];
        $coverdir = 'threadcover/'.substr(md5($tid), 0, 2).'/'.substr(md5($tid), 2, 2).'/';
        dmkdir($basedir.'./forum/'.$coverdir);

        require_once libfile('class/image');
        $image = new image();
        if($image->Thumb($picsource, 'forum/'.$coverdir.$tid.'.jpg', $_G['setting']['forumpicstyle']['thumbwidth'], $_G['setting']['forumpicstyle']['thumbheight'], 2)) {
            $remote = '';
            if(getglobal('setting/ftp/on')) {
                if(ftpcmd('upload', 'forum/'.$coverdir.$tid.'.jpg')) {
                    $remote = '-';
                }
            }
            $cover = C::t('forum_attachment_n')->count_image_by_id($attachtable, 'pid', $pid);
            if($imgurl && empty($cover)) {
                $cover = 1;
            }
            $cover = $remote.$cover;
        } else {
            return false;
        }
    }
    if($countimg) {
        if(empty($cover)) {
            $thread = C::t('forum_thread')->fetch($tid);
            $oldcover = $thread['cover'];

            $cover = C::t('forum_attachment_n')->count_image_by_id('tid:'.$tid, 'pid', $pid);
            if($cover) {
                $cover = $oldcover < 0 ? '-'.$cover : $cover;
            }
        }
    }
    if($cover) {
        C::t('forum_thread')->update($tid, array('cover' => $cover));
        return true;
    }
}