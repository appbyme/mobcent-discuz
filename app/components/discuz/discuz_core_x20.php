<?php

/**
 * 在 DISCUZ_ROOT/class/class_core.php 基础上进行二次开发
 * 
 * 如果你想按照你的需求修改此文件,
 * 请复制一份这个文件到相同目录，并且在原来的文件名基础上加上前缀my_,
 * 新建my_xxx.php文件不会随插件发布更新,请自行维护好！
 *
 * @author 谢建平 <jianping_xie@aliyun.com>
 * @copyright 2012-2014 Appbyme
 */

if (!defined('IN_DISCUZ') || !defined('IN_APPBYME')) {
    exit('Access Denied');
}

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
            self::$_app = discuz_core::instance();
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
                // leave for yii
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

class discuz_core {

    var $db = null;

    var $mem = null;

    var $session = null;

    var $config = array();

    var $var = array();

    var $cachelist = array();

    var $init_setting = true;
    var $init_user = true;
    var $init_session = true;
    var $init_cron = true;
    var $init_misc = true;
    var $init_memory = true;
    var $init_mobile = true;

    var $initated = false;

    var $superglobal = array(
        'GLOBALS' => 1,
        '_GET' => 1,
        '_POST' => 1,
        '_REQUEST' => 1,
        '_COOKIE' => 1,
        '_SERVER' => 1,
        '_ENV' => 1,
        '_FILES' => 1,
    );

    public static function &instance() {
        static $object;
        if(empty($object)) {
            $object = new discuz_core();
        }
        return $object;
    }

    function discuz_core() {
        $this->_init_env();
        $this->_init_config();
        $this->_init_input();
        $this->_init_output();
    }

    function init() {
        if(!$this->initated) {
            $this->_init_db();
            $this->_init_memory();
            $this->_init_user();
            $this->_init_session();
            $this->_init_setting();
            $this->_init_mobile();
            $this->_init_cron();
            $this->_init_misc();
        }
        $this->initated = true;
    }

    function _init_env() {

        error_reporting(E_ERROR);
        if(PHP_VERSION < '5.3.0') {
            set_magic_quotes_runtime(0);
        }

        define('DISCUZ_ROOT', substr(dirname(__FILE__), 0, -12));
        define('MAGIC_QUOTES_GPC', function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc());
        define('ICONV_ENABLE', function_exists('iconv'));
        define('MB_ENABLE', function_exists('mb_convert_encoding'));
        define('EXT_OBGZIP', function_exists('ob_gzhandler'));

        define('TIMESTAMP', time());
        $this->timezone_set();

        if(!defined('DISCUZ_CORE_FUNCTION') && !@include(DISCUZ_ROOT.'./source/function/function_core.php')) {
            exit('function_core.php is missing');
        }

        if(function_exists('ini_get')) {
            $memorylimit = @ini_get('memory_limit');
            if($memorylimit && return_bytes($memorylimit) < 33554432 && function_exists('ini_set')) {
                ini_set('memory_limit', '128m');
            }
        }

        define('IS_ROBOT', checkrobot());

        foreach ($GLOBALS as $key => $value) {
            if (!isset($this->superglobal[$key])) {
                $GLOBALS[$key] = null; unset($GLOBALS[$key]);
            }
        }

        global $_G;
        $_G = array(
            'uid' => 0,
            'username' => '',
            'adminid' => 0,
            'groupid' => 1,
            'sid' => '',
            'formhash' => '',
            'timestamp' => TIMESTAMP,
            'starttime' => dmicrotime(),
            'clientip' => $this->_get_client_ip(),
            'referer' => '',
            'charset' => '',
            'gzipcompress' => '',
            'authkey' => '',
            'timenow' => array(),

            'PHP_SELF' => '',
            'siteurl' => '',
            'siteroot' => '',
            'siteport' => '',

            'config' => array(),
            'setting' => array(),
            'member' => array(),
            'group' => array(),
            'cookie' => array(),
            'style' => array(),
            'cache' => array(),
            'session' => array(),
            'lang' => array(),
            'my_app' => array(),
            'my_userapp' => array(),

            'fid' => 0,
            'tid' => 0,
            'forum' => array(),
            'thread' => array(),
            'rssauth' => '',

            'home' => array(),
            'space' => array(),

            'block' => array(),
            'article' => array(),

            'action' => array(
                'action' => APPTYPEID,
                'fid' => 0,
                'tid' => 0,
            ),

            'mobile' => '',

        );
        $_G['PHP_SELF'] = htmlspecialchars($this->_get_script_url());
        $_G['basescript'] = CURSCRIPT;
        $_G['basefilename'] = basename($_G['PHP_SELF']);
        $sitepath = substr($_G['PHP_SELF'], 0, strrpos($_G['PHP_SELF'], '/'));
        if(defined('IN_API')) {
            $sitepath = preg_replace("/\/api\/?.*?$/i", '', $sitepath);
        } elseif(defined('IN_ARCHIVER')) {
            $sitepath = preg_replace("/\/archiver/i", '', $sitepath);
        }
        $_G['siteurl'] = htmlspecialchars('http://'.$_SERVER['HTTP_HOST'].$sitepath.'/');

        $url = parse_url($_G['siteurl']);
        $_G['siteroot'] = isset($url['path']) ? $url['path'] : '';
        $_G['siteport'] = empty($_SERVER['SERVER_PORT']) || $_SERVER['SERVER_PORT'] == '80' ? '' : ':'.$_SERVER['SERVER_PORT'];

        if(defined('SUB_DIR')) {
            $_G['siteurl'] = str_replace(SUB_DIR, '/', $_G['siteurl']);
            $_G['siteroot'] = str_replace(SUB_DIR, '/', $_G['siteroot']);
        }

        $this->var = & $_G;

    }

    function _get_script_url() {
        if($this->var['PHP_SELF'] === null){
            $scriptName = basename($_SERVER['SCRIPT_FILENAME']);
            if(basename($_SERVER['SCRIPT_NAME']) === $scriptName) {
                $this->var['PHP_SELF'] = $_SERVER['SCRIPT_NAME'];
            } else if(basename($_SERVER['PHP_SELF']) === $scriptName) {
                $this->var['PHP_SELF'] = $_SERVER['PHP_SELF'];
            } else if(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $scriptName) {
                $this->var['PHP_SELF'] = $_SERVER['ORIG_SCRIPT_NAME'];
            } else if(($pos = strpos($_SERVER['PHP_SELF'],'/'.$scriptName)) !== false) {
                $this->var['PHP_SELF'] = substr($_SERVER['SCRIPT_NAME'],0,$pos).'/'.$scriptName;
            } else if(isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'],$_SERVER['DOCUMENT_ROOT']) === 0) {
                $this->var['PHP_SELF'] = str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']));
            } else {
                system_error('request_tainting');
            }
        }
        return $this->var['PHP_SELF'];
    }

    function _init_input() {
        if (isset($_GET['GLOBALS']) ||isset($_POST['GLOBALS']) ||  isset($_COOKIE['GLOBALS']) || isset($_FILES['GLOBALS'])) {
            system_error('request_tainting');
        }

        // source bug!!!
        // if(!MAGIC_QUOTES_GPC) {
        //     $_GET = daddslashes($_GET);
        //     $_POST = daddslashes($_POST);
        //     $_COOKIE = daddslashes($_COOKIE);
        //     $_FILES = daddslashes($_FILES);
        // }
        if(MAGIC_QUOTES_GPC) {
            $_GET = dstripslashes($_GET);
            $_POST = dstripslashes($_POST);
            $_COOKIE = dstripslashes($_COOKIE);
        }
        
        $prelength = strlen($this->config['cookie']['cookiepre']);
        foreach($_COOKIE as $key => $val) {
            if(substr($key, 0, $prelength) == $this->config['cookie']['cookiepre']) {
                $this->var['cookie'][substr($key, $prelength)] = $val;
            }
        }


        if($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST)) {
            $_GET = array_merge($_GET, $_POST);
        }

        if(isset($_GET['diy'])) {
            $_GET['diy'] = empty($_GET['diy']) ? '' : $_GET['diy'];
        }

        foreach($_GET as $k => $v) {
            $this->var['gp_'.$k] = $v;
        }

        if(isset($this->var['gp_page'])) {
            $this->var['gp_page'] = rawurlencode($this->var['gp_page']);
        }

        $this->var['mod'] = empty($this->var['gp_mod']) ? '' : htmlspecialchars($this->var['gp_mod']);
        $this->var['inajax'] = empty($this->var['gp_inajax']) ? 0 : (empty($this->var['config']['output']['ajaxvalidate']) ? 1 : ($_SERVER['REQUEST_METHOD'] == 'GET' && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' || $_SERVER['REQUEST_METHOD'] == 'POST' ? 1 : 0));
        $this->var['page'] = empty($this->var['gp_page']) ? 1 : max(1, intval($this->var['gp_page']));
        $this->var['sid'] = $this->var['cookie']['sid'] = isset($this->var['cookie']['sid']) ? htmlspecialchars($this->var['cookie']['sid']) : '';
        $this->var['gp_handlekey'] = !empty($this->var['gp_handlekey']) && preg_match('/^\w+$/', $this->var['gp_handlekey']) ? $this->var['gp_handlekey'] : '';

        if(empty($this->var['cookie']['saltkey'])) {
            $this->var['cookie']['saltkey'] = random(8);
            dsetcookie('saltkey', $this->var['cookie']['saltkey'], 86400 * 30, 1, 1);
        }
        $this->var['authkey'] = md5($this->var['config']['security']['authkey'].$this->var['cookie']['saltkey']);
    }

    function _init_config() {
        $_config = array();
        @include DISCUZ_ROOT.'./config/config_global.php';
        if(empty($_config)) {
            if(!file_exists(DISCUZ_ROOT.'./data/install.lock')) {
                header('location: install');
                exit;
            } else {
                system_error('config_notfound');
            }
        }

        if(empty($_config['security']['authkey'])) {
            $_config['security']['authkey'] = md5($_config['cookie']['cookiepre'].$_config['db'][1]['dbname']);
        }

        if(empty($_config['debug']) || !file_exists(libfile('function/debug'))) {
            define('DISCUZ_DEBUG', false);
        } elseif($_config['debug'] === 1 || $_config['debug'] === 2 || !empty($_REQUEST['debug']) && $_REQUEST['debug'] === $_config['debug']) {
            define('DISCUZ_DEBUG', true);
            if($_config['debug'] == 2) {
                error_reporting(E_ALL);
            }
        } else {
            define('DISCUZ_DEBUG', false);
        }

        define('STATICURL', !empty($_config['output']['staticurl']) ? $_config['output']['staticurl'] : 'static/');
        $this->var['staticurl'] = STATICURL;

        $this->config = & $_config;
        $this->var['config'] = & $_config;

        if(substr($_config['cookie']['cookiepath'], 0, 1) != '/') {
            $this->var['config']['cookie']['cookiepath'] = '/'.$this->var['config']['cookie']['cookiepath'];
        }
        $this->var['config']['cookie']['cookiepre'] = $this->var['config']['cookie']['cookiepre'].substr(md5($this->var['config']['cookie']['cookiepath'].'|'.$this->var['config']['cookie']['cookiedomain']), 0, 4).'_';

    }

    function _init_output() {

        if($this->config['security']['urlxssdefend'] && $_SERVER['REQUEST_METHOD'] == 'GET' && !empty($_SERVER['REQUEST_URI'])) {
            $this->_xss_check();
        }

        if($this->config['security']['attackevasive'] && (!defined('CURSCRIPT') || !in_array($this->var['mod'], array('seccode', 'secqaa', 'swfupload')) && !defined('DISABLEDEFENSE'))) {
            require_once libfile('misc/security', 'include');
        }

        if(!empty($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') === false) {
            $this->config['output']['gzip'] = false;
        }

        $allowgzip = $this->config['output']['gzip'] && empty($this->var['inajax']) && $this->var['mod'] != 'attachment' && EXT_OBGZIP;
        setglobal('gzipcompress', $allowgzip);
        ob_start($allowgzip ? 'ob_gzhandler' : null);

        setglobal('charset', $this->config['output']['charset']);
        define('CHARSET', $this->config['output']['charset']);
        if($this->config['output']['forceheader']) {
            @header('Content-Type: text/html; charset='.CHARSET);
        }

    }

    function reject_robot() {
        if(IS_ROBOT) {
            exit(header("HTTP/1.1 403 Forbidden"));
        }
    }

    function _xss_check() {
        $temp = strtoupper(urldecode(urldecode($_SERVER['REQUEST_URI'])));
        if(strpos($temp, '<') !== false || strpos($temp, '"') !== false || strpos($temp, 'CONTENT-TRANSFER-ENCODING') !== false) {
            system_error('request_tainting');
        }
        return true;
    }

    function _get_client_ip() {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_CLIENT_IP']) && preg_match('/^([0-9]{1,3}\.){3}[0-9]{1,3}$/', $_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']) AND preg_match_all('#\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}#s', $_SERVER['HTTP_X_FORWARDED_FOR'], $matches)) {
            foreach ($matches[0] AS $xip) {
                if (!preg_match('#^(10|172\.16|192\.168)\.#', $xip)) {
                    $ip = $xip;
                    break;
                }
            }
        }
        return $ip;
    }

    function _init_db() {
        $class = 'db_mysql';
        if(count(getglobal('config/db/slave'))) {
            require_once libfile('class/mysql_slave');
            $class = 'db_mysql_slave';
        }
        $this->db = & DB::object($class);
        $this->db->set_config($this->config['db']);
        $this->db->connect();
    }

    function _init_session() {
        $this->session = new discuz_session();

        if($this->init_session) {
            $this->session->init($this->var['cookie']['sid'], $this->var['clientip'], $this->var['uid']);
            $this->var['sid'] = $this->session->sid;
            $this->var['session'] = $this->session->var;

            if($this->var['sid'] != $this->var['cookie']['sid']) {
                dsetcookie('sid', $this->var['sid'], 86400);
            }

            if($this->session->isnew) {
                if(ipbanned($this->var['clientip'])) {
                    $this->session->set('groupid', 6);
                }
            }

            if($this->session->get('groupid') == 6) {
                $this->var['member']['groupid'] = 6;
                sysmessage('user_banned');
            }

            if($this->var['uid'] && ($this->session->isnew || ($this->session->get('lastactivity') + 600) < TIMESTAMP)) {
                $this->session->set('lastactivity', TIMESTAMP);
                if($this->session->isnew) {
                    DB::update('common_member_status', array('lastip' => $this->var['clientip'], 'lastvisit' => TIMESTAMP), "uid='".$this->var['uid']."'");
                }
            }

        }
    }

    function _init_user() {
        if($this->init_user) {
            if($auth = getglobal('auth', 'cookie')) {
                $auth = daddslashes(explode("\t", authcode($auth, 'DECODE')));
            }
            list($discuz_pw, $discuz_uid) = empty($auth) || count($auth) < 2 ? array('', '') : $auth;

            if($discuz_uid) {
                $user = getuserbyuid($discuz_uid);
            }

            if(!empty($user) && $user['password'] == $discuz_pw) {
                $this->var['member'] = $user;
            } else {
                $user = array();
                $this->_init_guest();
            }

            if($user && $user['groupexpiry'] > 0 && $user['groupexpiry'] < TIMESTAMP && getgpc('mod') != 'spacecp' && getgpc('do') != 'expiry' && CURSCRIPT != 'home') {
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
        setglobal('username', addslashes(getglobal('username', 'member')));
        setglobal('adminid', getglobal('adminid', 'member'));
        setglobal('groupid', getglobal('groupid', 'member'));
    }

    function _init_guest() {
        setglobal('member', array( 'uid' => 0, 'username' => '', 'adminid' => 0, 'groupid' => 7, 'credits' => 0, 'timeoffset' => 9999));
    }

    function _init_cron() {
        $ext = empty($this->config['remote']['on']) || empty($this->config['remote']['cron']) || APPTYPEID == 200;
        if($this->init_cron && $this->init_setting && $ext) {
            if($this->var['cache']['cronnextrun'] <= TIMESTAMP) {
                require_once libfile('class/cron');
                discuz_cron::run();
            }
        }
    }

    function _init_misc() {
        if(!$this->init_misc) {
            return false;
        }
        lang('core');

        if($this->init_setting && $this->init_user) {
            if(!isset($this->var['member']['timeoffset']) || $this->var['member']['timeoffset'] == 9999 || $this->var['member']['timeoffset'] === '') {
                $this->var['member']['timeoffset'] = $this->var['setting']['timeoffset'];
            }
        }

        $timeoffset = $this->init_setting ? $this->var['member']['timeoffset'] : $this->var['setting']['timeoffset'];
        $this->var['timenow'] = array(
            'time' => dgmdate(TIMESTAMP),
            'offset' => $timeoffset >= 0 ? ($timeoffset == 0 ? '' : '+'.$timeoffset) : $timeoffset
        );
        $this->timezone_set($timeoffset);

        $this->var['formhash'] = formhash();
        define('FORMHASH', $this->var['formhash']);

        if($this->init_user) {
            if($this->var['group'] && isset($this->var['group']['allowvisit']) && !$this->var['group']['allowvisit']) {
                if($this->var['uid']) {
                    sysmessage('user_banned', null);
                } elseif((!defined('ALLOWGUEST') || !ALLOWGUEST) && !in_array(CURSCRIPT, array('member', 'api')) && !$this->var['inajax']) {
                    dheader('location: member.php?mod=logging&action=login&referer='.rawurlencode($this->var['siteurl'].$this->var['basefilename'].($_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : '')));
                }
            }
            if($this->var['member']['status'] == -1) {
                sysmessage('user_banned', null);
            }
        }

        if($this->var['setting']['ipaccess'] && !ipaccess($this->var['clientip'], $this->var['setting']['ipaccess'])) {
            sysmessage('user_banned', null);
        }

        if($this->var['setting']['bbclosed']) {
            if($this->var['uid'] && ($this->var['group']['allowvisit'] == 2 || $this->var['groupid'] == 1)) {
            } elseif(in_array(CURSCRIPT, array('admin', 'member', 'api')) || defined('ALLOWGUEST') && ALLOWGUEST) {
            } else {
                $closedreason = DB::result_first("SELECT svalue FROM ".DB::table('common_setting')." WHERE skey='closedreason'");
                $closedreason = str_replace(':', '&#58;', $closedreason);
                showmessage($closedreason ? $closedreason : 'board_closed', NULL, array('adminemail' => $this->var['setting']['adminemail']), array('login' => 1));
            }
        }

        if(CURSCRIPT != 'admin' && !(in_array($this->var['mod'], array('logging', 'seccode')))) {
            periodscheck('visitbanperiods');
        }

        if(defined('IN_MOBILE')) {
            $this->var['tpp'] = $this->var['setting']['mobile']['mobiletopicperpage'] ? intval($this->var['setting']['mobile']['mobiletopicperpage']) : 20;
            $this->var['ppp'] = $this->var['setting']['mobile']['mobilepostperpage'] ? intval($this->var['setting']['mobile']['mobilepostperpage']) : 5;
        } else {
            $this->var['tpp'] = $this->var['setting']['topicperpage'] ? intval($this->var['setting']['topicperpage']) : 20;
            $this->var['ppp'] = $this->var['setting']['postperpage'] ? intval($this->var['setting']['postperpage']) : 10;
        }

        if($this->var['setting']['nocacheheaders']) {
            @header("Expires: -1");
            @header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
            @header("Pragma: no-cache");
        }

        if($this->session->isnew && $this->var['uid']) {
            updatecreditbyaction('daylogin', $this->var['uid']);

            include_once libfile('function/stat');
            updatestat('login', 1);
            if(defined('IN_MOBILE')) {
                updatestat('mobilelogin', 1);
            }
            if($this->var['setting']['connect']['allow'] && $this->var['member']['conisbind']) {
                updatestat('connectlogin', 1);
            }
        }
        if($this->var['member']['conisbind'] && $this->var['setting']['connect']['newbiespan'] !== '') {
            $this->var['setting']['newbiespan'] = $this->var['setting']['connect']['newbiespan'];
        }

        $lastact = TIMESTAMP."\t".htmlspecialchars(basename($this->var['PHP_SELF']))."\t".htmlspecialchars($this->var['mod']);
        dsetcookie('lastact', $lastact, 86400);
        setglobal('currenturl_encode', base64_encode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']));

        if((!empty($this->var['gp_fromuid']) || !empty($this->var['gp_fromuser'])) && ($this->var['setting']['creditspolicy']['promotion_visit'] || $this->var['setting']['creditspolicy']['promotion_register'])) {
            require_once libfile('misc/promotion', 'include');
        }

        $this->var['seokeywords'] = !empty($this->var['setting']['seokeywords'][CURSCRIPT]) ? $this->var['setting']['seokeywords'][CURSCRIPT] : '';
        $this->var['seodescription'] = !empty($this->var['setting']['seodescription'][CURSCRIPT]) ? $this->var['setting']['seodescription'][CURSCRIPT] : '';

    }

    function _init_setting() {
        if($this->init_setting) {
            if(empty($this->var['setting'])) {
                $this->cachelist[] = 'setting';
            }

            if(empty($this->var['style'])) {
                $this->cachelist[] = 'style_default';
            }

            if(!isset($this->var['cache']['cronnextrun'])) {
                $this->cachelist[] = 'cronnextrun';
            }
        }

        !empty($this->cachelist) && loadcache($this->cachelist);

        if(!is_array($this->var['setting'])) {
            $this->var['setting'] = array();
        }

        if($this->var['member'] && $this->var['group']['radminid'] == 0 && $this->var['member']['adminid'] > 0 && $this->var['member']['groupid'] != $this->var['member']['adminid'] && !empty($this->var['cache']['admingroup_'.$this->var['member']['adminid']])) {
            $this->var['group'] = array_merge($this->var['group'], $this->var['cache']['admingroup_'.$this->var['member']['adminid']]);
        }
    }

    function _init_style() {
        $styleid = !empty($this->var['cookie']['styleid']) ? $this->var['cookie']['styleid'] : 0;
        if(intval(!empty($this->var['forum']['styleid']))) {
            $this->var['cache']['style_default']['styleid'] = $styleid = $this->var['forum']['styleid'];
        } elseif(intval(!empty($this->var['category']['styleid']))) {
            $this->var['cache']['style_default']['styleid'] = $styleid = $this->var['category']['styleid'];
        }

        $styleid = intval($styleid);

        if($styleid && $styleid != $this->var['setting']['styleid']) {
            loadcache('style_'.$styleid);
            if($this->var['cache']['style_'.$styleid]) {
                $this->var['style'] = $this->var['cache']['style_'.$styleid];
            }
        }

        define('IMGDIR', $this->var['style']['imgdir']);
        define('STYLEID', $this->var['style']['styleid']);
        define('VERHASH', $this->var['style']['verhash']);
        define('TPLDIR', $this->var['style']['tpldir']);
        define('TEMPLATEID', $this->var['style']['templateid']);
    }

    function _init_memory() {
        $this->mem = new discuz_memory();
        if($this->init_memory) {
            $this->mem->init($this->config['memory']);
        }
        $this->var['memory'] = $this->mem->type;
    }

    function _init_mobile() {
        if(!$this->var['setting'] || !$this->init_mobile || !$this->var['setting']['mobile']['allowmobile'] || !is_array($this->var['setting']['mobile']) || IS_ROBOT) {
            $nomobile = true;
            $unallowmobile = true;
        }

        if($_GET['mobile'] === 'no') {
            dsetcookie('mobile', 'no', 3600);
            $nomobile = true;
        } elseif($this->var['cookie']['mobile'] == 'no' && $_GET['mobile'] === 'yes') {
            dsetcookie('mobile', '');
        } elseif($this->var['cookie']['mobile'] == 'no') {
            $nomobile = true;
        }

        if(!checkmobile()) {
            $nomobile = true;
        }

        if($this->var['setting']['mobile']['mobilepreview'] && !$this->var['mobile'] && !$unallowmobile) {
            if($_GET['mobile'] === 'yes') {
                dheader("Location:misc.php?mod=mobile");
            }
        }

        if($nomobile || (!$this->var['setting']['mobile']['mobileforward'] && $_GET['mobile'] !== 'yes')) {
            if($_SERVER['HTTP_HOST'] == $this->var['setting']['domain']['app']['mobile'] && $this->var['setting']['domain']['app']['default']) {
                dheader("Location:http://".$this->var['setting']['domain']['app']['default'].$_SERVER['REQUEST_URI']);
            } else {
                return;
            }
        }

        if(strpos($this->var['setting']['domain']['defaultindex'], CURSCRIPT) !== false && CURSCRIPT != 'forum' && !$_GET['mod']) {
            if($this->var['setting']['domain']['app']['mobile']) {
                $mobileurl = 'http://'.$this->var['setting']['domain']['app']['mobile'];
            } else {
                if($this->var['setting']['domain']['app']['forum']) {
                    $mobileurl = 'http://'.$this->var['setting']['domain']['app']['forum'].'?mobile=yes';
                } else {
                    $mobileurl = $this->var['siteurl'].'forum.php?mobile=yes';
                }
            }
            dheader("location:$mobileurl");
        }
        define('IN_MOBILE', true);
        setglobal('gzipcompress', 0);

        $arr = array(strstr($_SERVER['QUERY_STRING'], '&simpletype'), strstr($_SERVER['QUERY_STRING'], 'simpletype'), '&mobile=yes', 'mobile=yes');
        $query_sting_tmp = str_replace($arr, '', $_SERVER['QUERY_STRING']);
        $this->var['setting']['mobile']['nomobileurl'] = ($this->var['setting']['domain']['app']['forum'] ? 'http://'.$this->var['setting']['domain']['app']['forum'].'/' : $this->var['siteurl']).$this->var['basefilename'].($query_sting_tmp ? '?'.$query_sting_tmp.'&' : '?').'mobile=no';

        $this->var['setting']['lazyload'] = 0;

        if('utf-8' != CHARSET) {
            if(strtolower($_SERVER['REQUEST_METHOD']) === 'post') {
                foreach($_POST AS $pk => $pv) {
                    if(!is_numeric($pv)) {
                        $this->var['gp_'.$pk] = $_GET[$pk] = $_POST[$pk] = $this->mobile_iconv_recurrence($pv);
                    }
                }
            }
        }

        if($_GET['simpletype']) {
            if($_GET['simpletype'] == 'yes') {
                $this->var['setting']['mobile']['mobilesimpletype'] = 1;
                dsetcookie('simpletype', 1, 86400);
            } else {
                $this->var['setting']['mobile']['mobilesimpletype'] = 0;
                dsetcookie('simpletype', 0, 86400);
            }
        } elseif($this->var['cookie']['simpletype']) {
            $this->var['setting']['mobile']['mobilesimpletype'] = $this->var['cookie']['simpletype'] == 1 ? 1 : 0 ;
        }

        if(!$this->var['setting']['mobile']['mobilesimpletype']) {
            $this->var['setting']['imagemaxwidth'] = 224;
        }

        $this->var['setting']['regstatus'] = $this->var['setting']['mobile']['mobileregister'] ? $this->var['setting']['regstatus'] : 0 ;
        if(!$this->var['setting']['mobile']['mobileseccode']) {
            $this->var['setting']['seccodestatus'] = 0;
        }

        $this->var['setting']['seccodedata']['type'] = 99;
        $this->var['setting']['thumbquality'] = 50;


        $this->var['setting']['mobile']['simpletypeurl'] = array();
        $this->var['setting']['mobile']['simpletypeurl'][0] = $this->var['siteurl'].$this->var['basefilename'].($query_sting_tmp ? '?'.$query_sting_tmp.'&' : '?').'mobile=yes&simpletype=no';
        $this->var['setting']['mobile']['simpletypeurl'][1] =  $this->var['siteurl'].$this->var['basefilename'].($query_sting_tmp ? '?'.$query_sting_tmp.'&' : '?').'mobile=yes&simpletype=yes';
        unset($query_sting_tmp);
        ob_start();
    }

    function timezone_set($timeoffset = 0) {
        if(function_exists('date_default_timezone_set')) {
            @date_default_timezone_set('Etc/GMT'.($timeoffset > 0 ? '-' : '+').(abs($timeoffset)));
        }
    }

    function mobile_iconv_recurrence($value) {
        if(is_array($value)) {
            foreach($value AS $key => $val) {
                $value[$key] = $this->mobile_iconv_recurrence($val);
            }
        } else {
            $value = addslashes(diconv(stripslashes($value), 'utf-8', CHARSET));
        }
        return $value;
    }
}

class db_mysql
{
    var $tablepre;
    var $version = '';
    var $querynum = 0;
    var $slaveid = 0;
    var $curlink;
    var $link = array();
    var $config = array();
    var $sqldebug = array();
    var $map = array();

    function db_mysql($config = array()) {
        if(!empty($config)) {
            $this->set_config($config);
        }
    }

    function set_config($config) {
        $this->config = &$config;
        $this->tablepre = $config['1']['tablepre'];
        if(!empty($this->config['map'])) {
            $this->map = $this->config['map'];
        }
    }

    function connect($serverid = 1) {

        if(empty($this->config) || empty($this->config[$serverid])) {
            $this->halt('config_db_not_found');
        }

        $this->link[$serverid] = $this->_dbconnect(
            $this->config[$serverid]['dbhost'],
            $this->config[$serverid]['dbuser'],
            $this->config[$serverid]['dbpw'],
            $this->config[$serverid]['dbcharset'],
            $this->config[$serverid]['dbname'],
            $this->config[$serverid]['pconnect']
            );
        $this->curlink = $this->link[$serverid];

    }

    function _dbconnect($dbhost, $dbuser, $dbpw, $dbcharset, $dbname, $pconnect) {
        $link = null;
        $func = empty($pconnect) ? 'mysql_connect' : 'mysql_pconnect';
        if(!$link = @$func($dbhost, $dbuser, $dbpw, 1)) {
            $this->halt('notconnect');
        } else {
            $this->curlink = $link;
            if($this->version() > '4.1') {
                $dbcharset = $dbcharset ? $dbcharset : $this->config[1]['dbcharset'];
                $serverset = $dbcharset ? 'character_set_connection='.$dbcharset.', character_set_results='.$dbcharset.', character_set_client=binary' : '';
                $serverset .= $this->version() > '5.0.1' ? ((empty($serverset) ? '' : ',').'sql_mode=\'\'') : '';
                $serverset && mysql_query("SET $serverset", $link);
            }
            $dbname && @mysql_select_db($dbname, $link);
        }
        return $link;
    }

    function table_name($tablename) {
        if(!empty($this->map) && !empty($this->map[$tablename])) {
            $id = $this->map[$tablename];
            if(!$this->link[$id]) {
                $this->connect($id);
            }
            $this->curlink = $this->link[$id];
        } else {
            $this->curlink = $this->link[1];
        }
        return $this->tablepre.$tablename;
    }

    function select_db($dbname) {
        return mysql_select_db($dbname, $this->curlink);
    }

    function fetch_array($query, $result_type = MYSQL_ASSOC) {
        return mysql_fetch_array($query, $result_type);
    }

    function fetch_first($sql) {
        return $this->fetch_array($this->query($sql));
    }

    function result_first($sql) {
        return $this->result($this->query($sql), 0);
    }

    public function query($sql, $type = '') {

        if(defined('DISCUZ_DEBUG') && DISCUZ_DEBUG) {
            $starttime = dmicrotime();
        }
        $func = $type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query') ?
        'mysql_unbuffered_query' : 'mysql_query';
        if(!($query = $func($sql, $this->curlink))) {
            if(in_array($this->errno(), array(2006, 2013)) && substr($type, 0, 5) != 'RETRY') {
                $this->connect();
                return $this->query($sql, 'RETRY'.$type);
            }
            if($type != 'SILENT' && substr($type, 5) != 'SILENT') {
                $this->halt('query_error', $sql);
            }
        }

        if(defined('DISCUZ_DEBUG') && DISCUZ_DEBUG) {
            $this->sqldebug[] = array($sql, number_format((dmicrotime() - $starttime), 6), debug_backtrace());
        }

        $this->querynum++;
        return $query;
    }

    function affected_rows() {
        return mysql_affected_rows($this->curlink);
    }

    function error() {
        return (($this->curlink) ? mysql_error($this->curlink) : mysql_error());
    }

    function errno() {
        return intval(($this->curlink) ? mysql_errno($this->curlink) : mysql_errno());
    }

    function result($query, $row = 0) {
        $query = @mysql_result($query, $row);
        return $query;
    }

    function num_rows($query) {
        $query = mysql_num_rows($query);
        return $query;
    }

    function num_fields($query) {
        return mysql_num_fields($query);
    }

    function free_result($query) {
        return mysql_free_result($query);
    }

    function insert_id() {
        return ($id = mysql_insert_id($this->curlink)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
    }

    function fetch_row($query) {
        $query = mysql_fetch_row($query);
        return $query;
    }

    function fetch_fields($query) {
        return mysql_fetch_field($query);
    }

    function version() {
        if(empty($this->version)) {
            $this->version = mysql_get_server_info($this->curlink);
        }
        return $this->version;
    }

    function close() {
        return mysql_close($this->curlink);
    }

    function halt($message = '', $sql = '') {
        require_once libfile('class/error');
        discuz_error::db_error($message, $sql);
    }

}

class DB
{

    public static function table($table) {
        return DB::_execute('table_name', $table);
    }

    function delete($table, $condition, $limit = 0, $unbuffered = true) {
        if(empty($condition)) {
            $where = '1';
        } elseif(is_array($condition)) {
            $where = DB::implode_field_value($condition, ' AND ');
        } else {
            $where = $condition;
        }
        $sql = "DELETE FROM ".DB::table($table)." WHERE $where ".($limit ? "LIMIT $limit" : '');
        return DB::query($sql, ($unbuffered ? 'UNBUFFERED' : ''));
    }

    function insert($table, $data, $return_insert_id = false, $replace = false, $silent = false) {

        $sql = DB::implode_field_value($data);

        $cmd = $replace ? 'REPLACE INTO' : 'INSERT INTO';

        $table = DB::table($table);
        $silent = $silent ? 'SILENT' : '';

        $return = DB::query("$cmd $table SET $sql", $silent);

        return $return_insert_id ? DB::insert_id() : $return;

    }

    function update($table, $data, $condition, $unbuffered = false, $low_priority = false) {
        $sql = DB::implode_field_value($data);
        $cmd = "UPDATE ".($low_priority ? 'LOW_PRIORITY' : '');
        $table = DB::table($table);
        $where = '';
        if(empty($condition)) {
            $where = '1';
        } elseif(is_array($condition)) {
            $where = DB::implode_field_value($condition, ' AND ');
        } else {
            $where = $condition;
        }
        $res = DB::query("$cmd $table SET $sql WHERE $where", $unbuffered ? 'UNBUFFERED' : '');
        return $res;
    }

    function implode_field_value($array, $glue = ',') {
        $sql = $comma = '';
        foreach ($array as $k => $v) {
            $sql .= $comma."`$k`='$v'";
            $comma = $glue;
        }
        return $sql;
    }

    function insert_id() {
        return DB::_execute('insert_id');
    }

    public static function fetch($resourceid, $type = MYSQL_ASSOC) {
        return DB::_execute('fetch_array', $resourceid, $type);
    }

    function fetch_first($sql) {
        DB::checkquery($sql);
        return DB::_execute('fetch_first', $sql);
    }

    function result($resourceid, $row = 0) {
        return DB::_execute('result', $resourceid, $row);
    }

    function result_first($sql) {
        DB::checkquery($sql);
        return DB::_execute('result_first', $sql);
    }

    public static function query($sql, $type = '') {
        DB::checkquery($sql);
        return DB::_execute('query', $sql, $type);
    }

    function num_rows($resourceid) {
        return DB::_execute('num_rows', $resourceid);
    }

    function affected_rows() {
        return DB::_execute('affected_rows');
    }

    function free_result($query) {
        return DB::_execute('free_result', $query);
    }

    function error() {
        return DB::_execute('error');
    }

    function errno() {
        return DB::_execute('errno');
    }

    private static function _execute($cmd , $arg1 = '', $arg2 = '') {
        static $db;
        if(empty($db)) $db = & DB::object();
        $res = $db->$cmd($arg1, $arg2);
        return $res;
    }

    public static function &object($dbclass = 'db_mysql') {
        static $db;
        if(empty($db)) $db = new $dbclass();
        return $db;
    }

    public static function checkquery($sql) {
        static $status = null, $checkcmd = array('SELECT', 'UPDATE', 'INSERT', 'REPLACE', 'DELETE');
        if($status === null) $status = getglobal('config/security/querysafe/status');
        if($status) {
            $cmd = trim(strtoupper(substr($sql, 0, strpos($sql, ' '))));
            if(in_array($cmd, $checkcmd)) {
                $test = DB::_do_query_safe($sql);
                if($test < 1) DB::_execute('halt', 'security_error', $sql);
            }
        }
        return true;
    }

    public static function _do_query_safe($sql) {
        static $_CONFIG = null;
        if($_CONFIG === null) {
            $_CONFIG = getglobal('config/security/querysafe');
        }

        $sql = str_replace(array('\\\\', '\\\'', '\\"', '\'\''), '', $sql);
        $mark = $clean = '';
        if(strpos($sql, '/') === false && strpos($sql, '#') === false && strpos($sql, '-- ') === false) {
            $clean = preg_replace("/'(.+?)'/s", '', $sql);
        } else {
            $len = strlen($sql);
            $mark = $clean = '';
            for ($i = 0; $i <$len; $i++) {
                $str = $sql[$i];
                switch ($str) {
                    case '\'':
                        if(!$mark) {
                            $mark = '\'';
                            $clean .= $str;
                        } elseif ($mark == '\'') {
                            $mark = '';
                        }
                        break;
                    case '/':
                        if(empty($mark) && $sql[$i+1] == '*') {
                            $mark = '/*';
                            $clean .= $mark;
                            $i++;
                        } elseif($mark == '/*' && $sql[$i -1] == '*') {
                            $mark = '';
                            $clean .= '*';
                        }
                        break;
                    case '#':
                        if(empty($mark)) {
                            $mark = $str;
                            $clean .= $str;
                        }
                        break;
                    case "\n":
                        if($mark == '#' || $mark == '--') {
                            $mark = '';
                        }
                        break;
                    case '-':
                        if(empty($mark)&& substr($sql, $i, 3) == '-- ') {
                            $mark = '-- ';
                            $clean .= $mark;
                        }
                        break;

                    default:

                        break;
                }
                $clean .= $mark ? '' : $str;
            }
        }

        $clean = preg_replace("/[^a-z0-9_\-\(\)#\*\/\"]+/is", "", strtolower($clean));

        if($_CONFIG['afullnote']) {
            $clean = str_replace('/**/','',$clean);
        }

        if(is_array($_CONFIG['dfunction'])) {
            foreach($_CONFIG['dfunction'] as $fun) {
                if(strpos($clean, $fun.'(') !== false) return '-1';
            }
        }

        if(is_array($_CONFIG['daction'])) {
            foreach($_CONFIG['daction'] as $action) {
                if(strpos($clean,$action) !== false) return '-3';
            }
        }

        if($_CONFIG['dlikehex'] && strpos($clean, 'like0x')) {
            return '-2';
        }

        if(is_array($_CONFIG['dnote'])) {
            foreach($_CONFIG['dnote'] as $note) {
                if(strpos($clean,$note) !== false) return '-4';
            }
        }

        return 1;

    }

}

class discuz_session {

    var $sid = null;
    var $var;
    var $isnew = false;
    var $newguest = array('sid' => 0, 'ip1' => 0, 'ip2' => 0, 'ip3' => 0, 'ip4' => 0,
    'uid' => 0, 'username' => '', 'groupid' => 7, 'invisible' => 0, 'action' => 0,
    'lastactivity' => 0, 'fid' => 0, 'tid' => 0, 'lastolupdate' => 0);

    var $old =  array('sid' =>  '', 'ip' =>  '', 'uid' =>  0);

    function discuz_session($sid = '', $ip = '', $uid = 0) {
        $this->old = array('sid' =>  $sid, 'ip' =>  $ip, 'uid' =>  $uid);
        $this->var = $this->newguest;
        if(!empty($ip)) {
            $this->init($sid, $ip, $uid);
        }
    }

    function set($key, $value) {
        if(isset($this->newguest[$key])) {
            $this->var[$key] = $value;
        } elseif ($key == 'ip') {
            $ips = explode('.', $value);
            $this->set('ip1', $ips[0]);
            $this->set('ip2', $ips[1]);
            $this->set('ip3', $ips[2]);
            $this->set('ip4', $ips[3]);
        }
    }

    function get($key) {
        if(isset($this->newguest[$key])) {
            return $this->var[$key];
        } elseif ($key == 'ip') {
            return $this->get('ip1').'.'.$this->get('ip2').'.'.$this->get('ip3').'.'.$this->get('ip4');
        }
    }

    function init($sid, $ip, $uid) {
        $this->old = array('sid' =>  $sid, 'ip' =>  $ip, 'uid' =>  $uid);
        $session = array();
        if($sid) {
            $session = DB::fetch_first("SELECT * FROM ".DB::table('common_session').
                " WHERE sid='$sid' AND CONCAT_WS('.', ip1,ip2,ip3,ip4)='$ip'");
        }

        if(empty($session) || $session['uid'] != $uid) {
            $session = $this->create($ip, $uid);
        }

        $this->var = $session;
        $this->sid = $session['sid'];
    }

    function create($ip, $uid) {

        $this->isnew = true;
        $this->var = $this->newguest;
        $this->set('sid', random(6));
        $this->set('uid', $uid);
        $this->set('ip', $ip);
        $uid && $this->set('invisible', getuserprofile('invisible'));
        $this->set('lastactivity', time());
        $this->sid = $this->var['sid'];

        return $this->var;
    }

    function delete() {

        global $_G;
        $onlinehold = $_G['setting']['onlinehold'];
        $guestspan = 60;

        $onlinehold = time() - $onlinehold;
        $guestspan = time() - $guestspan;

        $condition = " sid='{$this->sid}' ";
        $condition .= " OR lastactivity<$onlinehold ";
        $condition .= " OR (uid='0' AND ip1='{$this->var['ip1']}' AND ip2='{$this->var['ip2']}' AND ip3='{$this->var['ip3']}' AND ip4='{$this->var['ip4']}' AND lastactivity>$guestspan) ";
        $condition .= $this->var['uid'] ? " OR (uid='{$this->var['uid']}') " : '';
        DB::delete('common_session', $condition);
    }

    function update() {
        global $_G;
        if($this->sid !== null) {

            $data = daddslashes($this->var);
            if($this->isnew) {
                $this->delete();
                DB::insert('common_session', $data, false, false, true);
            } else {
                DB::update('common_session', $data, "sid='$data[sid]'");
            }
            $_G['session'] = $data;
            dsetcookie('sid', $this->sid, 86400);
        }
    }

    function onlinecount($type = 0) {
        $condition = $type == 1 ? ' WHERE uid>0 ' : ($type == 2 ? ' WHERE invisible=1 ' : '');
        return DB::result_first("SELECT count(*) FROM ".DB::table('common_session').$condition);
    }

}


class discuz_process
{
    function islocked($process, $ttl = 0) {
        $ttl = $ttl < 1 ? 600 : intval($ttl);
        if(discuz_process::_status('get', $process)) {
            return true;
        } else {
            return discuz_process::_find($process, $ttl);
        }
    }

    function unlock($process) {
        discuz_process::_status('rm', $process);
        discuz_process::_cmd('rm', $process);
    }

    function _status($action, $process) {
        static $plist = array();
        switch ($action) {
            case 'set' : $plist[$process] = true; break;
            case 'get' : return !empty($plist[$process]); break;
            case 'rm' : $plist[$process] = null; break;
            case 'clear' : $plist = array(); break;
        }
        return true;
    }

    function _find($name, $ttl) {

        if(!discuz_process::_cmd('get', $name)) {
            discuz_process::_cmd('set', $name, $ttl);
            $ret = false;
        } else {
            $ret = true;
        }
        discuz_process::_status('set', $name);
        return $ret;
    }

    function _cmd($cmd, $name, $ttl = 0) {
        static $allowmem;
        if($allowmem === null) {
            $allowmem = memory('check') == 'memcache';
        }
        if($allowmem) {
            return discuz_process::_process_cmd_memory($cmd, $name, $ttl);
        } else {
            return discuz_process::_process_cmd_db($cmd, $name, $ttl);
        }
    }

    function _process_cmd_memory($cmd, $name, $ttl = 0) {
        return memory($cmd, 'process_lock_'.$name, time(), $ttl);
    }

    function _process_cmd_db($cmd, $name, $ttl = 0) {
        $ret = '';
        switch ($cmd) {
            case 'set':
                $ret = DB::insert('common_process', array('processid' => $name, 'expiry' => time() + $ttl), false, true);
                break;
            case 'get':
                $ret = DB::fetch_first("SELECT * FROM ".DB::table('common_process')." WHERE processid='$name'");
                if(empty($ret) || $ret['expiry'] < time()) {
                    $ret = false;
                } else {
                    $ret = true;
                }
                break;
            case 'rm':
                $ret = DB::delete('common_process', "processid='$name' OR expiry<".time());
                break;
        }
        return $ret;
    }
}

class discuz_memory
{
    var $config;
    var $extension = array();
    var $memory;
    var $prefix;
    var $type;
    var $keys;
    var $enable = false;

    function discuz_memory() {
        $this->extension['eaccelerator'] = function_exists('eaccelerator_get');
        $this->extension['apc'] = function_exists('apc_fetch');
        $this->extension['xcache'] = function_exists('xcache_get');
        $this->extension['memcache'] = extension_loaded('memcache');
    }

    function init($config) {

        $this->config = $config;
        $this->prefix = empty($config['prefix']) ? substr(md5($_SERVER['HTTP_HOST']), 0, 6).'_' : $config['prefix'];
        $this->keys = array();

        if($this->extension['memcache'] && !empty($config['memcache']['server'])) {
            require_once libfile('class/memcache');
            $this->memory = new discuz_memcache();
            $this->memory->init($this->config['memcache']);
            if(!$this->memory->enable) {
                $this->memory = null;
            }
        }

        if(!is_object($this->memory) && $this->extension['eaccelerator'] && $this->config['eaccelerator']) {
            require_once libfile('class/eaccelerator');
            $this->memory = new discuz_eaccelerator();
            $this->memory->init(null);
        }

        if(!is_object($this->memory) && $this->extension['xcache'] && $this->config['xcache']) {
            require_once libfile('class/xcache');
            $this->memory = new discuz_xcache();
            $this->memory->init(null);
        }

        if(!is_object($this->memory) && $this->extension['apc'] && $this->config['apc']) {
            require_once libfile('class/apc');
            $this->memory = new discuz_apc();
            $this->memory->init(null);
        }

        if(is_object($this->memory)) {
            $this->enable = true;
            $this->type = str_replace('discuz_', '', get_class($this->memory));
            $this->keys = $this->get('memory_system_keys');
            $this->keys = !is_array($this->keys) ? array() : $this->keys;
        }

    }

    function get($key) {
        $ret = null;
        if($this->enable) {
            $ret = $this->memory->get($this->_key($key));
            if(!is_array($ret)) {
                $ret = null;
                if(array_key_exists($key, $this->keys)) {
                    unset($this->keys[$key]);
                    $this->memory->set($this->_key('memory_system_keys'), array($this->keys));
                }
            } else {
                return $ret[0];
            }
        }
        return $ret;
    }

    function set($key, $value, $ttl = 0) {

        $ret = null;
        if($this->enable) {
            $ret = $this->memory->set($this->_key($key), array($value), $ttl);
            if($ret) {
                $this->keys[$key] = true;
                $this->memory->set($this->_key('memory_system_keys'), array($this->keys));
            }
        }
        return $ret;
    }

    function rm($key) {
        $ret = null;
        if($this->enable) {
            $ret = $this->memory->rm($this->_key($key));
            unset($this->keys[$key]);
            $this->memory->set($this->_key('memory_system_keys'), array($this->keys));
        }
        return $ret;
    }

    function clear() {
        if($this->enable && is_array($this->keys)) {
            if(method_exists($this->memory, 'clear')) {
                $this->memory->clear();
            } else {
                $this->keys['memory_system_keys'] = true;
                foreach ($this->keys as $k => $v) {
                    $this->memory->rm($this->_key($k));
                }
            }
        }
        $this->keys = array();
        return true;
    }

    function _key($str) {
        return ($this->prefix).$str;
    }

}

Yii::registerAutoloader(array('core', 'autoload'));

class discuz_application extends discuz_core {}
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
                $this->var['member'] = $user;
            } else {
                $user = array();
                $this->_initGuest();
            }

            if($user && $user['groupexpiry'] > 0 && $user['groupexpiry'] < TIMESTAMP && getgpc('mod') != 'spacecp' && getgpc('do') != 'expiry' && CURSCRIPT != 'home') {
                dheader('location: home.php?mod=spacecp&ac=usergroup&do=expiry');
            }

            $this->cachelist[] = 'usergroup_'.$this->var['member']['groupid'];
            if($user && $user['adminid'] > 0 && $user['groupid'] != $user['adminid']) {
                $this->cachelist[] = 'admingroup_'.$this->var['member']['adminid'];
            }

        } else {
            $this->_initGuest();
        }

        if(empty($this->var['cookie']['lastvisit'])) {
            $this->var['member']['lastvisit'] = TIMESTAMP - 3600;
            dsetcookie('lastvisit', TIMESTAMP - 3600, 86400 * 30);
        } else {
            $this->var['member']['lastvisit'] = $this->var['cookie']['lastvisit'];
        }
        setglobal('uid', getglobal('uid', 'member'));
        setglobal('username', addslashes(getglobal('username', 'member')));
        setglobal('adminid', getglobal('adminid', 'member'));
        setglobal('groupid', getglobal('groupid', 'member'));
    }

    protected function _initGuest() {
        setglobal('member', array( 'uid' => 0, 'username' => '', 'adminid' => 0, 'groupid' => 7, 'credits' => 0, 'timeoffset' => 9999));
    }

    protected function _initEnv() {
        global $_G;

        $this->var = & $_G;
    }
}

class C extends core {}
