<?php

/**
 *
 * 在 DISCUZ_ROOT/source/function_spacecp.php 基础上做了改动
 *
 * @author HanPengyu
 * @copyright 2012-2014 Appbyme
 */

function mobcent_pic_save($FILE, $albumid, $title, $iswatermark = true, $catid = 0) {
    global $_G, $space;

    if($albumid<0) $albumid = 0;

    $allowpictype = array('jpg','jpeg','gif','png');

    $upload = new discuz_upload();
    $upload->init($FILE, 'album');

    if($upload->error()) {
        return lang('spacecp', 'lack_of_access_to_upload_file_size');
    }

    if(!$upload->attach['isimage']) {
        return lang('spacecp', 'only_allows_upload_file_types');
    }
    $oldgid = $_G['groupid'];
    if(empty($space)) {
        $_G['member'] = $space = getuserbyuid($_G['uid']);
        $_G['username'] = $space['username'];
        $_G['groupid'] = $space['groupid'];
    }
    $_G['member'] = $space;

    loadcache('usergroup_'.$space['groupid'], $oldgid != $_G['groupid'] ? true : false);
    $_G['group'] = $_G['cache']['usergroup_'.$space['groupid']];

    if(!checkperm('allowupload')) {
        return lang('spacecp', 'not_allow_upload');
    }

    if(!cknewuser(1)) {
        if($_G['setting']['newbiespan'] && $_G['timestamp'] - $_G['member']['regdate'] < $_G['setting']['newbiespan'] * 60) {
            return lang('message', 'no_privilege_newbiespan', array('newbiespan' => $_G['setting']['newbiespan']));
        }

        if($_G['setting']['need_avatar'] && empty($_G['member']['avatarstatus'])) {
            return lang('message', 'no_privilege_avatar');
        }

        if($_G['setting']['need_email'] && empty($_G['member']['emailstatus'])) {
            return lang('message', 'no_privilege_email');
        }

        if($_G['setting']['need_friendnum']) {
            space_merge($_G['member'], 'count');
            if($_G['member']['friends'] < $_G['setting']['need_friendnum']) {
                return lang('message', 'no_privilege_friendnum', array('friendnum' => $_G['setting']['need_friendnum']));
            }
        }
    }
    if($_G['group']['maximagesize'] && $upload->attach['size'] > $_G['group']['maximagesize']) {
        return lang('spacecp', 'files_can_not_exceed_size', array('extend' => $upload->attach['ext'], 'size' => sizecount($_G['group']['maximagesize'])));
    }

    $maxspacesize = checkperm('maxspacesize');
    if($maxspacesize) {
        space_merge($space, 'count');
        space_merge($space, 'field_home');
        if($space['attachsize'] + $upload->attach['size'] > $maxspacesize + $space['addsize'] * 1024 * 1024) {
            return lang('spacecp', 'inadequate_capacity_space');
        }
    }

    $showtip = true;
    $albumfriend = 0;
    if($albumid) {
        $catid = intval($catid);
        $albumid = album_creat_by_id($albumid, $catid);
    } else {
        $albumid = 0;
        $showtip = false;
    }

    $upload->save();
    if($upload->error()) {
        return lang('spacecp', 'mobile_picture_temporary_failure');
    }
    if(!$upload->attach['imageinfo'] || !in_array($upload->attach['imageinfo']['2'], array(1,2,3,6))) {
        @unlink($upload->attach['target']);
        return lang('spacecp', 'only_allows_upload_file_types');
    }

    $new_name = $upload->attach['target'];

    require_once libfile('class/image');
    $image = new image();
    $result = $image->Thumb($new_name, '', 140, 140, 1);
    $thumb = empty($result)?0:1;

    if($_G['setting']['maxthumbwidth'] && $_G['setting']['maxthumbheight']) {
        if($_G['setting']['maxthumbwidth'] < 300) $_G['setting']['maxthumbwidth'] = 300;
        if($_G['setting']['maxthumbheight'] < 300) $_G['setting']['maxthumbheight'] = 300;
        $image->Thumb($new_name, '', $_G['setting']['maxthumbwidth'], $_G['setting']['maxthumbheight'], 1, 1);
    }

    // 支持客户端上传相册水印 Author：HanPengyu Data：2014/12/04
    Yii::import('application.components.discuz.source.class.class_image', true);
    $image = new Mobcent_Image;
    $image->makeWatermark($new_name, '', 'album');

    // if ($iswatermark) {
    //     $image->Watermark($new_name, '', 'album');
    // }
    $pic_remote = 0;
    $album_picflag = 1;

    if(getglobal('setting/ftp/on')) {
        $ftpresult_thumb = 0;
        $ftpresult = ftpcmd('upload', 'album/'.$upload->attach['attachment']);
        if($ftpresult) {
            @unlink($_G['setting']['attachdir'].'album/'.$upload->attach['attachment']);
            if($thumb) {
                $thumbpath = getimgthumbname($upload->attach['attachment']);
                ftpcmd('upload', 'album/'.$thumbpath);
                @unlink($_G['setting']['attachdir'].'album/'.$thumbpath);
            }
            $pic_remote = 1;
            $album_picflag = 2;
        } else {
            if(getglobal('setting/ftp/mirror')) {
                @unlink($upload->attach['target']);
                @unlink(getimgthumbname($upload->attach['target']));
                return lang('spacecp', 'ftp_upload_file_size');
            }
        }
    }

    $title = getstr($title, 200);
    $title = censor($title);
    if(censormod($title) || $_G['group']['allowuploadmod']) {
        $pic_status = 1;
    } else {
        $pic_status = 0;
    }

    $setarr = array(
        'albumid' => $albumid,
        'uid' => $_G['uid'],
        'username' => $_G['username'],
        'dateline' => $_G['timestamp'],
        'filename' => addslashes($upload->attach['name']),
        'postip' => $_G['clientip'],
        'title' => $title,
        'type' => addslashes($upload->attach['ext']),
        'size' => $upload->attach['size'],
        'filepath' => $upload->attach['attachment'],
        'thumb' => $thumb,
        'remote' => $pic_remote,
        'status' => $pic_status,
    );
    $setarr['picid'] = C::t('home_pic')->insert($setarr, 1);

    C::t('common_member_count')->increase($_G['uid'], array('attachsize' => $upload->attach['size']));

    include_once libfile('function/stat');
    if($pic_status) {
        updatemoderate('picid', $setarr['picid']);
    }
    updatestat('pic');

    return $setarr;
}

?>