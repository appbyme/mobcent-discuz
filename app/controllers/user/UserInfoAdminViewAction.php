<?php

/**
 * 个人资料编辑html视图接口
 *
 * @author 徐少伟 <xushaowei@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

// Mobcent::setErrors();
class UserInfoAdminViewAction extends MobcentAction {

    public function run($act='base') {
        $app = Yii::app()->getController()->mobcentDiscuzApp;
        $app->loadForum($fid, $tid);

        if (!empty($_POST)) {
            // 把$_POST转成utf-8, 这是由于discuz源码会在mobile情况下把$_POST预先转码成对应的charset,
            $_POST = array_intersect_key($_REQUEST, $_POST);
            // 手动把转成utf-8的$_POST数据再次转成对应的charset
            foreach ($_POST as $key => $value) {
                if (is_string($value)) {
                    $_POST[$key] = WebUtils::t($value);
                }
            }
            $_GET = array_merge($_GET, $_POST);
        }
        $this->_adminUserInfo($act);
    }

    private function _adminUserInfo($act) {
        global $_G;
        $defaultop = '';
        $profilegroup = C::t('common_setting')->fetch('profilegroup', true);
        foreach($profilegroup as $key => $value) {
            if($value['available']) {
                $defaultop = $key;
                break;
            }
        }

        $errorMsg = '';
        $operation = $act;

        require_once libfile('function/editor');
        include_once libfile('function/profile');

        $space = getuserbyuid($_G['uid']);
        space_merge($space, 'profile');
        space_merge($space, 'field_home');
        space_merge($space, 'field_forum');
        $space['sightml'] = html2bbcode($space['sightml']);
        $vid = $_GET['vid'] ? intval($_GET['vid']) : 0;
        $privacy = $space['privacy']['profile'] ? $space['privacy']['profile'] : array();
        $_G['setting']['privacy'] = $_G['setting']['privacy'] ? $_G['setting']['privacy'] : array();
        $_G['setting']['privacy'] = is_array($_G['setting']['privacy']) ? $_G['setting']['privacy'] : dunserialize($_G['setting']['privacy']);
        $_G['setting']['privacy']['profile'] = !empty($_G['setting']['privacy']['profile']) ? $_G['setting']['privacy']['profile'] : array();
        $privacy = array_merge($_G['setting']['privacy']['profile'], $privacy);
        $actives = array('profile' =>' class="a"');
        $opactives = array($operation =>' class="a"');
        $allowitems = array();
        $allowitems = $profilegroup[$operation]['field'];
        $showbtn = ($vid && $verify['verify'.$vid] != 1) || empty($vid);
        if(!empty($verify) && is_array($verify)) {
            foreach($verify as $key => $flag) {
                if(in_array($key, array('verify1', 'verify2', 'verify3', 'verify4', 'verify5', 'verify6', 'verify7')) && $flag == 1) {
                    $verifyid = intval(substr($key, -1, 1));
                    if($_G['setting']['verify'][$verifyid]['available']) {
                        foreach($_G['setting']['verify'][$verifyid]['field'] as $field) {
                            $_G['cache']['profilesetting'][$field]['unchangeable'] = 1;
                        }
                    }
                }
            }
        }
        if($vid) {
            if($value = C::t('common_member_verify_info')->fetch_by_uid_verifytype($_G['uid'], $vid)) {
                $field = dunserialize($value['field']);
                foreach($field as $key => $fvalue) {
                    $space[$key] = $fvalue;
                }
            }
        }
        $htmls = $settings = array();
        foreach($allowitems as $fieldid) {
            if(!in_array($fieldid, array('sightml', 'customstatus', 'timeoffset'))) {
                $html = profile_setting($fieldid, $space, $vid ? false : true);
                if($html) {
                    $settings[$fieldid] = $_G['cache']['profilesetting'][$fieldid];
                    $htmls[$fieldid] = $html;
                }
            }
        }

        if (!empty($_POST)) {
            require_once libfile('function/discuzcode');

            $forum = $setarr = $verifyarr = $errorarr = array();
            $forumfield = array('customstatus', 'sightml');

            $censor = discuz_censor::instance();

            if($_GET['vid']) {
                $vid = intval($_GET['vid']);
                $verifyconfig = $_G['setting']['verify'][$vid];
                if($verifyconfig['available'] && (empty($verifyconfig['groupid']) || in_array($_G['groupid'], $verifyconfig['groupid']))) {
                    $verifyinfo = C::t('common_member_verify_info')->fetch_by_uid_verifytype($_G['uid'], $vid);
                    if(!empty($verifyinfo)) {
                        $verifyinfo['field'] = dunserialize($verifyinfo['field']);
                    }
                    foreach($verifyconfig['field'] as $key => $field) {
                        if(!isset($verifyinfo['field'][$key])) {
                            $verifyinfo['field'][$key] = $key;
                        }
                    }
                } else {
                    $_GET['vid'] = $vid = 0;
                    $verifyconfig = array();
                }
            }
            if(isset($_POST['birthprovince'])) {
                $initcity = array('birthprovince', 'birthcity', 'birthdist', 'birthcommunity');
                foreach($initcity as $key) {
                    $_GET[''.$key] = $_POST[$key] = !empty($_POST[$key]) ? $_POST[$key] : '';
                }
            }
            if(isset($_POST['resideprovince'])) {
                $initcity = array('resideprovince', 'residecity', 'residedist', 'residecommunity');
                foreach($initcity as $key) {
                    $_GET[''.$key] = $_POST[$key] = !empty($_POST[$key]) ? $_POST[$key] : '';
                }
            }
            foreach($_POST as $key => $value) {
                $field = $_G['cache']['profilesetting'][$key];
                if(in_array($field['formtype'], array('text', 'textarea')) || in_array($key, $forumfield)) {
                    $censor->check($value);
                    if($censor->modbanned() || $censor->modmoderated()) {
                        $list = $this->makeErrorInfo($res, 'profile_censor');
                        $this->_exitWithHtmlAlert($list['errcode']);
                        // profile_showerror($key, lang('spacecp', 'profile_censor'));
                    }
                }
                if(in_array($key, $forumfield)) {
                    if($key == 'sightml') {
                        loadcache(array('smilies', 'smileytypes'));
                        $value = cutstr($value, $_G['group']['maxsigsize'], '');
                        foreach($_G['cache']['smilies']['replacearray'] AS $skey => $smiley) {
                            $_G['cache']['smilies']['replacearray'][$skey] = '[img]'.$_G['siteurl'].'static/image/smiley/'.$_G['cache']['smileytypes'][$_G['cache']['smilies']['typearray'][$skey]]['directory'].'/'.$smiley.'[/img]';
                        }
                        $value = preg_replace($_G['cache']['smilies']['searcharray'], $_G['cache']['smilies']['replacearray'], trim($value));
                        $forum[$key] = discuzcode($value, 1, 0, 0, 0, $_G['group']['allowsigbbcode'], $_G['group']['allowsigimgcode'], 0, 0, 1);
                    } elseif($key=='customstatus' && $allowcstatus) {
                        $forum[$key] = dhtmlspecialchars(trim($value));
                    }
                    continue;
                } elseif($field && !$field['available']) {
                    continue;
                } elseif($key == 'timeoffset') {
                    if($value >= -12 && $value <= 12 || $value == 9999) {
                        C::t('common_member')->update($_G['uid'], array('timeoffset' => intval($value)));
                    }
                } elseif($key == 'site') {
                    if(!in_array(strtolower(substr($value, 0, 6)), array('http:/', 'https:', 'ftp://', 'rtsp:/', 'mms://')) && !preg_match('/^static\//', $value) && !preg_match('/^data\//', $value)) {
                        $value = 'http://'.$value;
                    }
                }
                if($field['formtype'] == 'file') {
                    if((!empty($_FILES[$key]) && $_FILES[$key]['error'] == 0) || (!empty($space[$key]) && empty($_GET['deletefile'][$key]))) {
                        $value = '1';
                    } else {
                        $value = '';
                    }
                }
                if(empty($field)) {
                    continue;
                } elseif(profile_check($key, $value, $space)) {
                    $setarr[$key] = dhtmlspecialchars(trim($value));
                } else {
                    if($key=='birthprovince') {
                        $key = 'birthcity';
                    } elseif($key=='resideprovince' || $key=='residecommunity'||$key=='residedist') {
                        $key = 'residecity';
                    } elseif($key=='birthyear' || $key=='birthmonth') {
                        $key = 'birthday';
                    }
                    // profile_showerror($key);
                    $list = $this->makeErrorInfo($res, 'check_date_item');
                    $errcode = $list['errcode'].$settings[$key]['title'];
                    $this->_exitWithHtmlAlert($errcode);
                }
                if($field['formtype'] == 'file') {
                    unset($setarr[$key]);
                }
                if($vid && $verifyconfig['available'] && isset($verifyconfig['field'][$key])) {
                    if(isset($verifyinfo['field'][$key]) && $setarr[$key] !== $space[$key]) {
                        $verifyarr[$key] = $setarr[$key];
                    }
                    unset($setarr[$key]);
                }
                if(isset($setarr[$key]) && $_G['cache']['profilesetting'][$key]['needverify']) {
                    if($setarr[$key] !== $space[$key]) {
                        $verifyarr[$key] = $setarr[$key];
                    }
                    unset($setarr[$key]);
                }
            }
            
            if($vid && !empty($verifyinfo['field']) && is_array($verifyinfo['field'])) {
                foreach($verifyinfo['field'] as $key => $fvalue) {
                    if(!isset($verifyconfig['field'][$key])) {
                        unset($verifyinfo['field'][$key]);
                        continue;
                    }
                    if(empty($verifyarr[$key]) && !isset($verifyarr[$key]) && isset($verifyinfo['field'][$key])) {
                        $verifyarr[$key] = !empty($fvalue) && $key != $fvalue ? $fvalue : $space[$key];
                    }
                }
            }
            if($forum) {
                if(!$_G['group']['maxsigsize']) {
                    $forum['sightml'] = '';
                }
                C::t('common_member_field_forum')->update($_G['uid'], $forum);
            }
            if(isset($_POST['birthmonth']) && ($space['birthmonth'] != $_POST['birthmonth'] || $space['birthday'] != $_POST['birthday'])) {
                $setarr['constellation'] = get_constellation($_POST['birthmonth'], $_POST['birthday']);
            }
            if(isset($_POST['birthyear']) && $space['birthyear'] != $_POST['birthyear']) {
                $setarr['zodiac'] = get_zodiac($_POST['birthyear']);
            }
            if($setarr) {
                C::t('common_member_profile')->update($_G['uid'], $setarr);
            }

            if($verifyarr) {
                C::t('common_member_verify_info')->delete_by_uid($_G['uid'], $vid);
                $setverify = array(
                        'uid' => $_G['uid'],
                        'username' => $_G['username'],
                        'verifytype' => $vid,
                        'field' => serialize($verifyarr),
                        'dateline' => $_G['timestamp']
                    );

                C::t('common_member_verify_info')->insert($setverify);
                if(!(C::t('common_member_verify')->count_by_uid($_G['uid']))) {
                    C::t('common_member_verify')->insert(array('uid' => $_G['uid']));
                }
                if($_G['setting']['verify'][$vid]['available']) {
                    manage_addnotify('verify_'.$vid, 0, array('langkey' => 'manage_verify_field', 'verifyname' => $_G['setting']['verify'][$vid]['title'], 'doid' => $vid));
                }
            }

            if(isset($_POST['privacy'])) {
                foreach($_POST['privacy'] as $key=>$value) {
                    if(isset($_G['cache']['profilesetting'][$key])) {
                        $space['privacy']['profile'][$key] = intval($value);
                    }
                }
                C::t('common_member_field_home')->update($space['uid'], array('privacy'=>serialize($space['privacy'])));
            }
            manyoulog('user', $_G['uid'], 'update');
            include_once libfile('function/feed');
            feed_add('profile', 'feed_profile_update_'.$operation, array('hash_data'=>'profile'));
            countprofileprogress();
            // $message = $vid ? lang('spacecp', 'profile_verify_verifying', array('verify' => $verifyconfig['title'])) : '';
            $list = $this->makeErrorInfo($res, 'update_date_success');
            $this->_exitWithHtmlAlert($list['errcode']);

        }

        $this->getController()->renderPartial('userInfoAdmin', array(
            'formUrl' => WebUtils::createUrl_oldVersion('user/userinfoadminview', array('act' => $act)),
            'errorMsg' => $errorMsg,
            'action' => $act,
            '_G' => $_G,
            'htmls' => $htmls,
            'settings' => $settings,
        ));
    }

    private function _exitWithHtmlAlert($message)
    {
        $message = lang('message', $message);
        $location = WebUtils::createUrl_oldVersion('index/returnmobileview');
        $htmlString = sprintf('
            <script>
                alert("%s");
                location.href = "%s";
            </script>',
            $message, $location
        );
        echo $htmlString;
        exit;
    }
}