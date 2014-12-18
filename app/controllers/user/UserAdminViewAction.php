<?php

/**
 * 好友管理html视图接口
 *
 * @author 徐少伟 <xushaowei@mobcent.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

// Mobcent::setErrors();
class UserAdminViewAction extends MobcentAction {

    public function run($uid, $act='add') {
        $app = Yii::app()->getController()->mobcentDiscuzApp;

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

        $this->_adminUser($act, $uid);
    }

    private function _adminUser($act, $uid) {

        global $_G;
        $errorMsg = '';
        require_once libfile('function/spacecp');
        require_once libfile('function/home');
        require_once libfile('function/friend');
        if (friend_request_check($uid) && $act == 'add') {
            $act = 'add2';
        }

        if ($act == 'add'|| $act == 'add2') {
            if($uid == $_G['uid']) {
                $list = $this->makeErrorInfo($res, 'friend_self_error');
                $this->_exitWithHtmlAlert($list['errcode']);
            }

            if(friend_check($uid)) {
                $list = $this->makeErrorInfo($res, 'you_have_friends');
                $this->_exitWithHtmlAlert($list['errcode']);
            }

            $tospace = getuserbyuid($uid);
            if(empty($tospace)) {
                $list = $this->makeErrorInfo($res, 'space_does_not_exist');
                $this->_exitWithHtmlAlert($list['errcode']);
            }

            if(isblacklist($tospace['uid'])) {
                $list = $this->makeErrorInfo($res, 'is_blacklist');
                $this->_exitWithHtmlAlert($list['errcode']);
            }

            space_merge($space, 'count');
            space_merge($space, 'field_home');
            $maxfriendnum = checkperm('maxfriendnum');

            if($maxfriendnum && $space['friends'] >= $maxfriendnum + $space['addfriend']) {
                if($_G['magic']['friendnum']) {
                    $list = $this->makeErrorInfo($res, 'enough_of_the_number_of_friends_with_magic');
                    $this->_exitWithHtmlAlert($list['errcode']);
                } else {
                    $list = $this->makeErrorInfo($res, 'enough_of_the_number_of_friends');
                    $this->_exitWithHtmlAlert($list['errcode']);
                }
            } 

            if ($act == 'add') {

                if(!checkperm('allowfriend')) {
                    $list = $this->makeErrorInfo($res, 'no_privilege_addfriend');
                    $this->_exitWithHtmlAlert($list['errcode']);
                }

                if(C::t('home_friend_request')->count_by_uid_fuid($uid, $_G['uid'])) {
                    $list = $this->makeErrorInfo($res, 'waiting_for_the_other_test');
                    $this->_exitWithHtmlAlert($list['errcode']);
                }
            }
        }

        require_once libfile('function/friend');
        require_once libfile('function/spacecp');
        if (!empty($_POST)) {
            switch ($act) {
                case 'add':
                    $note = $_GET['note'];

                    if(!friend_request_check($uid)) {
                        $_POST['gid'] = $gid;
                        $_POST['note'] = censor(htmlspecialchars(cutstr($note, strtolower(CHARSET) == 'utf-8' ? 30 : 20, '')));
                        friend_add($uid, $_POST['gid'], $_POST['note']);
                        $note = array(
                            'uid' => $_G['uid'],
                            'url' => 'home.php?mod=spacecp&ac=friend&op=add&uid='.$_G['uid'].'&from=notice',
                            'from_id' => $_G['uid'],
                            'from_idtype' => 'friendrequest',
                            'note' => !empty($_POST['note']) ? lang('spacecp', 'friend_request_note', array('note' => $_POST['note'])) : ''
                        );

                        notification_add($uid, 'friend', 'friend_request', $note);
                        
                        // ios push 
                        UserUtils::pushIOSMessage($uid, 'friend', $_G['username'].WebUtils::t(' 请求加您为好友').$note['note']);

                        require_once libfile('function/mail');
                        $values = array(
                            'username' => $tospace['username'],
                            'url' => getsiteurl().'home.php?mod=spacecp&ac=friend&amp;op=request'
                        );
                        sendmail_touser($uid, lang('spacecp', 'friend_subject', $values), '', 'friend_add');
                        $list = $this->makeErrorInfo($res, 'request_has_been_sent');
                        $this->_exitWithHtmlAlert($list['errcode']);
                    }
                    $this->_exitWithHtmlAlert($res['errcode']);
                case 'add2':
                    global $_G;
                    require_once libfile('function/home');
                    $_POST['gid'] = intval($gid);
                    friend_add($uid, $uid);

                    if(ckprivacy('friend', 'feed')) {
                        require_once libfile('function/feed');
                        feed_add('friend', 'feed_friend_title', array('touser'=>"<a href=\"home.php?mod=space&uid=$tospace[uid]\">$tospace[username]</a>"));
                    }

                    notification_add($uid, 'friend', 'friend_add');
                    // showmessage('friends_add', dreferer(), array('username' => $tospace['username'], 'uid'=>$uid, 'from' => $_GET['from']), array('showdialog'=>1, 'showmsg' => true, 'closetime' => true));
                    $list = $this->makeErrorInfo($res, 'friends_add', array('{username}' => $tospace['username']));
                    $this->_exitWithHtmlAlert($list['errcode']);
                    break;
                case 'ignore':
                    global $_G;
                    require_once libfile('function/friend');
                    friend_delete($uid);
                    $params['noError'] = 1;
                    $list = $this->makeErrorInfo($res, 'do_success', $params);
                    $this->_exitWithHtmlAlert($list['errcode']);
                    break;
                case 'shield':
                    global $_G, $space;
                    // $type = empty($_GET['type'])?'':preg_replace("/[^0-9a-zA-Z\_\-\.]/", '', $_GET['type']);
                    // if(submitcheck('ignoresubmit')) {
                        $authorid = empty($_POST['authorid']) ? 0 : intval($_POST['authorid']);
                        $type = 'friend';
                        if($type) {
                            $type_uid = $type.'|'.$authorid;
                            if(empty($space['privacy']['filter_note']) || !is_array($space['privacy']['filter_note'])) {
                                $space['privacy']['filter_note'] = array();
                            }
                            $space['privacy']['filter_note'][$type_uid] = $type_uid;
                            privacy_update();
                        }
                        $this->_exitWithHtmlAlert('do_success');
                        // showmessage('do_success', dreferer(), array(), array('showdialog'=>1, 'showmsg' => true, 'closetime' => true));
                    // }
                    $formid = random(8);
                    break;
                default:
                    $errorMsg = '错误的动作参数';
                    break;
            }
        } else {
            if ($act == 'add') {
                require_once libfile('function/friend');
                $groups = $this->_getFriendGroupList();
                $tospace = getuserbyuid($uid);
            } elseif ($act == 'add2'){
                require_once libfile('function/friend');
                $groups = $this->_getFriendGroupList();
                $tospace = $this->_getFriendUserByUid($uid);
            }
        }

        $this->getController()->renderPartial('userAdmin', array(
            'formUrl' => WebUtils::createUrl_oldVersion('user/useradminview', array('uid' => $uid, 'act' => $act, 'type' => $type)),
            'errorMsg' => $errorMsg,
            'action' => $act,
            '_G' => $_G,
            'groups' => $groups,
            'tospace' => $tospace,
        ));
    }

    private function _getFriendGroupList() {
        return friend_group_list();
    }

    private function _getFriendUserByUid($uid) {
        return getuserbyuid($uid);
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