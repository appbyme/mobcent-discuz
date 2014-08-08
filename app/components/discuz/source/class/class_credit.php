<?php
/**
 * 继承于discuz的credit类
 *
 * @author
 */
class discuz_class_credit extends credit {}
class Mobcent_class_credit extends discuz_class_credit {
    function updatemembercount($creditarr, $uids = 0, $checkgroup = true, $ruletxt = '') {
        global $_G;
        if(!$uids) $uids = intval($_G['uid']);
        $uids = is_array($uids) ? $uids : array($uids);
        if($uids && ($creditarr || $this->extrasql)) {
            if($this->extrasql) $creditarr = array_merge($creditarr, $this->extrasql);
            $sql = array();
            $allowkey = array('extcredits1', 'extcredits2', 'extcredits3', 'extcredits4', 'extcredits5', 'extcredits6', 'extcredits7', 'extcredits8', 'friends', 'posts', 'threads', 'oltime', 'digestposts', 'doings', 'blogs', 'albums', 'sharings', 'attachsize', 'views', 'todayattachs', 'todayattachsize');
            $creditnotice = $_G['setting']['creditnotice'] && $_G['uid'] && $uids == array($_G['uid']);
            if($creditnotice) {
                if(!isset($_G['cookiecredits'])) {
                    $_G['cookiecredits'] = !empty($_COOKIE['creditnotice']) ? explode('D', $_COOKIE['creditnotice']) : array_fill(0, 9, 0);
                    for($i = 1; $i <= 8; $i++) {
                        $_G['cookiecreditsbase'][$i] = getuserprofile('extcredits'.$i);
                    }
                }
                if($ruletxt) {
                    $_G['cookiecreditsrule'][$ruletxt] = $ruletxt;
                }
            }
            //$critarr 各项积分参数extcredit设置的值
            $settingValue = WebUtils::getDzPluginAppbymeAppConfig('dzsyscache_forum_extcredit_base');
            foreach($creditarr as $key => $value) {
                    $mutilute = 1;
                    foreach($settingValue as $k => $v){
                        if($key == 'extcredits'.$k ){
                            $mutilute = $v*0.01;
                        }
                    }
                if(!empty($key) && $value && in_array($key, $allowkey)) {
                    $sql[$key] = $value*$mutilute;
                    if($creditnotice && substr($key, 0, 10) == 'extcredits') {
                        $i = substr($key, 10);
                        $_G['cookiecredits'][$i] += $value*$mutilute;
                    }
                }
            }
            if($creditnotice) {
                dsetcookie('creditnotice', implode('D', $_G['cookiecredits']).'D'.$_G['uid']);
                dsetcookie('creditbase', '0D'.implode('D', $_G['cookiecreditsbase']));
                if(!empty($_G['cookiecreditsrule'])) {
                    dsetcookie('creditrule', strip_tags(implode("\t", $_G['cookiecreditsrule'])));
                }
            }
            //var_dump($sql);die;
            if($sql) {
                C::t('common_member_count')->increase($uids, $sql);
            }
            if($checkgroup && count($uids) == 1) $this->checkusergroup($uids[0]);
            $this->extrasql = array();
        }
    }
}