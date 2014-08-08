<?php

/**
 * 在 DISCUZ_ROOT/class/class_core.php 基础上进行二次开发
 * 
 * 如果你想按照你的需求修改此文件(比如说innodb的补丁)，
 * 请复制一份这个文件到相同目录，并且在原来的文件名基础上加上前缀my_,
 * 新建my_xxx.php文件不会随插件发布更新,请自行维护好！
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

define('EXTEND', true);
define('EXTEND_NO_CACHE', false);
define('EXTEND_NO_DETECT', false);

class core
{
    private static $_tables;
    private static $_imports;
    private static $_app;
    private static $_memory;

    public static function app() {
        return self::$_app;
    }

    public static function creatapp() {
        if(!is_object(self::$_app)) {
            self::$_app = discuz_application::instance();
        }
        return self::$_app;
    }

    public static function t($name) {
        $pluginid = null;
        if($name[0] === '#') {
            list(, $pluginid, $name) = explode('#', $name);
        }
        $classname = 'table_'.$name;
        if(!isset(self::$_tables[$classname])) {
            if(!class_exists($classname, false)) {
                self::import(($pluginid ? 'plugin/'.$pluginid : 'class').'/table/'.$name);
            }
            self::$_tables[$classname] = new $classname;
        }
        return self::$_tables[$classname];
    }

    public static function memory() {
        if(!self::$_memory) {
            self::$_memory = new discuz_memory();
            self::$_memory->init(self::app()->config['memory']);
        }
        return self::$_memory;
    }

    public static function import($name, $folder = '', $force = true) {
        $key = $folder.$name;
        if(!isset(self::$_imports[$key])) {
            $path = DISCUZ_ROOT.'/source/'.$folder;
            if(strpos($name, '/') !== false) {
                $pre = basename(dirname($name));
                $filename = dirname($name).'/'.$pre.'_'.basename($name).'.php';
            } else {
                $filename = $name.'.php';
            }

            if(is_file($path.'/'.$filename)) {
                self::$_imports[$key] = true;
                return include $path.'/'.$filename;
            } elseif(!$force) {
                return false;
            } else {
                // 交给 yii 的autoload方法
                return true;
            }
        }
        return true;
    }

    public static function autoload($class) {
        $class = strtolower($class);
        if(strpos($class, '_') !== false) {
            list($folder) = explode('_', $class);
            $file = 'class/'.$folder.'/'.substr($class, strlen($folder) + 1);
        } else {
            $file = 'class/'.$class;
        }

        return self::import($file);
    }

    static function setconstant() {
    }
}

class core_ext extends core 
{
    private static $_tables;
    private static $_imports;

    public static function t($name) {
        $pluginid = null;
        if($name[0] === '#') {
            list(, $pluginid, $name) = explode('#', $name);
        }
        $classname = 'table_'.$name;

        if($pluginid == null && defined('EXTEND') && EXTEND === true) {
            $name_ext = $name . '_ext';
            if(self::import('class/table/'.$name_ext, '', false, true) != false) {
                $classname .= '_ext';
            }
        }

        if(!isset(self::$_tables[$classname])) {
            if(!class_exists($classname, false)) {
                self::import(($pluginid ? 'plugin/'.$pluginid : 'class').'/table/'.$name);
            }
            self::$_tables[$classname] = new $classname;
        }
        return self::$_tables[$classname];
    }

    public static function import($name, $folder = '', $force = true, $ext = false, $exists = false, $getpath = false, $getmts = false) {
        $key = $folder.$name;
        if(!isset(self::$_imports[$key])) {
            $path = DISCUZ_ROOT.'/source/'.$folder;
            if($ext) $path = DISCUZ_ROOT.'/extend/'.$folder;

            if(strpos($name, '/') !== false) {
                $pre = basename(dirname($name));
                $filename = dirname($name).'/'.$pre.'_'.basename($name).'.php';
            } else {
                $filename = $name.'.php';
            }

            if(is_file($path.'/'.$filename)) {
                if($exists == true) return true;//Ö»·µ»Ø´æÔÚÓë·ñ
                if($getmts == true) return filemtime($path.'/'.$filename);
                self::$_imports[$key] = true;
                if($getpath == true) return $path.'/'.$filename;//Ö»·µ»ØÂ·¾¶,¶øÇÒÖ»Òª·µ»ØÂ·¾¶¾Í¿Ï¶¨ÔØÈëÁËÀàÎÄ¼þ
                $rt = include $path.'/'.$filename;
                return $rt;
            } elseif(!$force) {
                return false;
            } else {
                // throw new Exception('Oops! System file lost: '.$filename);
                // 交给 yii 的autoload方法
                return true;
            }
        }
        return true;
    }

    public static function autoload($class) {
        $class = strtolower($class);
        if(strpos($class, '_') !== false) {
            list($folder) = explode('_', $class);
            $file = 'class/'.$folder.'/'.substr($class, strlen($folder) + 1);
        } else {
            $file = 'class/'.$class;
        }

        try {

            if(defined('EXTEND') && EXTEND === true && (strpos($class, 'table') === FALSE) && self::import($file . '_ext', '', false, true, true) === true) {
                $mts = defined('EXTEND_NO_DETECT') && EXTEND_NO_DETECT === false ? (string)date('Ymd~Hi~s', self::import($file . '_ext', '', false, true, false, false, true)) : '';
                $cacf = DISCUZ_ROOT.'/data/sysdata/'.$class.'_ext'. $mts .'.php';
                if(defined('EXTEND_NO_CACHE') && EXTEND_NO_CACHE === true || !is_file($cacf)) {
                    $class_cont = self::combine_class($class);
                    self::put_class($class_cont, $cacf);
                }
                include $cacf;
                return true;
            }
            self::import($file);
            return true;

        } catch (Exception $exc) {

            $trace = $exc->getTrace();
            foreach ($trace as $log) {
                if(empty($log['class']) && $log['function'] == 'class_exists') {
                    return false;
                }
            }
            discuz_error::exception_error($exc);
        }
    }

    static function combine_class($class, $parentfullpath = '', $childfullpath = '') {
        if($parentfullpath == '' || $childfullpath == '') {
            $class = strtolower($class);
            if(strpos($class, '_') !== false) {
                list($folder) = explode('_', $class);
                $file = 'class/'.$folder.'/'.substr($class, strlen($folder) + 1);
            } else {
                $file = 'class/'.$class;
            }
            $parentfullpath = $parentfullpath == '' ? self::import($file, '', false, false, false, true) : $parentfullpath;
            $childfullpath = $childfullpath == '' ? self::import($file . '_ext', '', false, true, false, true) : $childfullpath;
        }
        
        $class_ext_cont = file_get_contents($childfullpath);
        preg_match_all('/class[\t ]+(\w+)_ext[\t ]+extends[\t ]+(\w+)/i', $class_ext_cont, $matches);
        $class_ext_cont = preg_replace('/class[\t ]+(\w+)_ext[\t ]+extends[\t ]+(\w+)/i', 'class ${1} extends ${2}_ext', $class_ext_cont);
        $class_ext_list = $matches[2];

        $class_cont = file_get_contents($parentfullpath);
        if(!empty($class_ext_list) && is_array($class_ext_list)) {
            foreach($class_ext_list as $class_v) {
                $class_cont = preg_replace("/class[\t ]+(".$class_v.")/i", 'class ${1}_ext', $class_cont, 1);
                $class_cont = preg_replace("/function[\t ]+(".$class_v.")/i", 'function __construct', $class_cont, 1);
                //TODO ¶Ô¹¹Ôìº¯ÊýµÄ´¦Àí£¬Èç¹ûÎÄ¼þÖÐ´æÔÚÆäËûÀàÇÒÓÐÍ¬Ãû·½·¨£¬»áµ¼ÖÂ´íÎó
            }
        }
        $str_find = array('<?php', '?>');
        $class_cont = str_replace($str_find, '', $class_cont.$class_ext_cont);
        return "<?php\n\n//Created: ".date("M j, Y, G:i")."\n".$class_cont;
    }

    static function put_class($cont, $cacf) {
        if(empty($cont)) return false;
        return file_put_contents($cacf, $cont, LOCK_EX);
    }

    static function setconstant() {
        global $_G;
        if($_G['config']['extend'] && is_array($_G['config']['extend'])) {
            foreach($_G['config']['extend'] as $k => $v) {
                define(strtoupper($k), $v['on']);
            }
        }
    }
}

// 装了innodb补丁的请把C改成由core_ext继承
class C extends core {}
// class C extends core_ext {}

Yii::registerAutoloader(array('C', 'autoload'));

class DB extends discuz_database {}

class MobcentDiscuzApp extends discuz_application {

    public function __construct() {
        $this->_initEnv();
    }

    public function init() {
        global $_G;
        $_G['siteurl'] = substr($_G['siteurl'], 0, -16);
        $_G['siteroot'] = substr($_G['siteroot'], 0, -16);
        
        $this->_initUser();

        loadcache('plugin');
        loadcache(MOBCENT_DZ_PLUGIN_ID);
    }

    public function loadForum($fid, $tid=0) {
        require_once libfile('function/forum');

        $path = Yii::getPathOfAlias('application.components.discuz.source.function');
        require_once(sprintf('%s/function_forum_%s.php', $path, MobcentDiscuz::getMobcentDiscuzVersion()));

        $_GET['fid'] = $fid;
        $_GET['tid'] = $tid;

        global $_G;
        $_G['setting']['forumpicstyle'] = null;
        
        loadforum();
    }

    protected function _initUser() {
        if($this->init_user) {
            $discuz_uid = isset($_GET['hacker_uid']) && MOBCENT_HACKER_UID ? $_GET['hacker_uid'] : UserUtils::getUserIdByAccess();

            if($discuz_uid) {
                $user = getuserbyuid($discuz_uid, 1);
            }

            if(!empty($user)) {
                if(isset($user['_inarchive'])) {
                    C::t('common_member_archive')->move_to_master($discuz_uid);
                }
                $this->var['member'] = $user;
            } else {
                $user = array();
                $this->_initGuest();
            }

            if($user && $user['groupexpiry'] > 0 && $user['groupexpiry'] < TIMESTAMP && (getgpc('mod') != 'spacecp' || CURSCRIPT != 'home')) {
                dheader('location: home.php?mod=spacecp&ac=usergroup&do=expiry');
            }

            $this->cachelist[] = 'usergroup_'.$this->var['member']['groupid'];
            if($user && $user['adminid'] > 0 && $user['groupid'] != $user['adminid']) {
                $this->cachelist[] = 'admingroup_'.$this->var['member']['adminid'];
            }
        } else {
            $this->_init_guest();
        }
        
        if(empty($this->var['cookie']['lastvisit'])) {
            $this->var['member']['lastvisit'] = TIMESTAMP - 3600;
            dsetcookie('lastvisit', TIMESTAMP - 3600, 86400 * 30);
        } else {
            $this->var['member']['lastvisit'] = $this->var['cookie']['lastvisit'];
        }

        setglobal('uid', getglobal('uid', 'member'));
        setglobal('username', getglobal('username', 'member'));
        setglobal('adminid', getglobal('adminid', 'member'));
        setglobal('groupid', getglobal('groupid', 'member'));
  
        !empty($this->cachelist) && loadcache($this->cachelist);
        
        if($this->var['member'] && $this->var['group']['radminid'] == 0 && $this->var['member']['adminid'] > 0 && $this->var['member']['groupid'] != $this->var['member']['adminid'] && !empty($this->var['cache']['admingroup_'.$this->var['member']['adminid']])) {
            $this->var['group'] = array_merge($this->var['group'], $this->var['cache']['admingroup_'.$this->var['member']['adminid']]);
        }
    }

    protected function _initGuest() {
        $username = '';
        $groupid = 7;
        if(!empty($this->var['cookie']['con_auth_hash']) && ($openid = authcode($this->var['cookie']['con_auth_hash']))) {
            $this->var['connectguest'] = 1;
            $username = 'QQ_'.substr($openid, -6);
            $this->var['setting']['cacheindexlife'] = 0;
            $this->var['setting']['cachethreadlife'] = 0;
            $groupid = $this->var['setting']['connect']['guest_groupid'] ? $this->var['setting']['connect']['guest_groupid'] : $this->var['setting']['newusergroupid'];
        }
        setglobal('member', array( 'uid' => 0, 'username' => $username, 'adminid' => 0, 'groupid' => $groupid, 'credits' => 0, 'timeoffset' => 9999));
    }

    protected function _initEnv() {
        global $_G;

        $this->var = & $_G;
    }
}