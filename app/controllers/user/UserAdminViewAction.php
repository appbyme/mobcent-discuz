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
class UserAdminViewAction extends CAction {

    public function run($uid, $act='add') {
        $app = Yii::app()->getController()->mobcentDiscuzApp;
        $this->_adminUser($act, $uid);
    }

    private function _adminUser($act, $uid) {

        global $_G;
        $errorMsg = '';

        require_once libfile('function/friend');
        require_once libfile('function/spacecp');
        if (!empty($_POST)) {
            switch ($act) {
                case 'add':
                    $_POST = array_intersect_key($_REQUEST, $_POST);
                    $res = WebUtils::httpRequestAppAPI('user/useradmin', array(
                        'uid'=>$uid,
                        'type'=>'friend',
                        'gid'=>$_POST['gid'],
                        'message'=>$_POST['note'],
                    ));
                    $res = WebUtils::jsonDecode($res);
                    $this->_exitWithHtmlAlert($res['errcode']);
                    break;
                case 'add2':
                    $_POST = array_intersect_key($_REQUEST, $_POST);
                    $res = WebUtils::httpRequestAppAPI('user/useradmin', array(
                        'uid'=>$uid,
                        'type'=>'friend',
                        'gid'=>$_POST['gid'],
                        'message'=>$_POST['note'],
                    ));
                    $res = WebUtils::jsonDecode($res);
                    $this->_exitWithHtmlAlert($res['errcode']);
                    break;
                case 'ignore':
                    $_POST = array_intersect_key($_REQUEST, $_POST);
                    $res = WebUtils::httpRequestAppAPI('user/useradmin', array(
                        'uid'=>$uid,
                        'type'=>'delfriend',
                    ));
                    $list = WebUtils::jsonDecode($res);
                    $this->_exitWithHtmlAlert($list['errcode']);
                    break;
                case 'shield':
                    global $_G, $space;
                    // $type = empty($_GET['type'])?'':preg_replace("/[^0-9a-zA-Z\_\-\.]/", '', $_GET['type']);
                    // if(submitcheck('ignoresubmit')) {
                        $authorid = empty($_POST['authorid']) ? 0 : intval($_POST['authorid']);
                        $type = 'friend';
                        $_G['uid'] = 1;
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
            'groups' => WebUtils::u($groups),
            'tospace' => WebUtils::u($tospace),
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

function checkexpiration($expiration, $operation) {
    global $_G;
    if(!empty($expiration) && in_array($operation, array('recommend', 'stick', 'digest', 'highlight', 'close'))) {
        $expiration = strtotime($expiration) - $_G['setting']['timeoffset'] * 3600 + date('Z');
        if(dgmdate($expiration, 'Ymd') <= dgmdate(TIMESTAMP, 'Ymd') || ($expiration > TIMESTAMP + 86400 * 180)) {
            showmessage('admin_expiration_invalid', '', array('min'=>dgmdate(TIMESTAMP, 'Y-m-d'), 'max'=>dgmdate(TIMESTAMP + 86400 * 180, 'Y-m-d')));
        }
    } else {
        $expiration = 0;
    }
    return $expiration;
}