<?php

define('IN_DISCUZ', true);
define('DISCUZ_ROOT', substr(dirname(__FILE__), 0, -29));

require_once(DISCUZ_ROOT . '/source/discuz_version.php');
require_once(DISCUZ_ROOT . '/config/config_ucenter.php');

Mobcent::import(sprintf('%s/discuz_core_%s.php', dirname(__FILE__), MobcentDiscuz::getMobcentDiscuzVersion()));

Mobcent::import(MOBCENT_APP_ROOT . '/components/discuz/source/function/function_core.php');

class MobcentDiscuz {

    const VERSION_X20 = 'x20';
    const VERSION_X25 = 'x25';
    const VERSION_X30 = 'x30';
    const VERSION_X31 = 'x30';

    private static $_discuzVersions = array(
        'X2' => self::VERSION_X20,
        'X2.5' => self::VERSION_X25,
        'X3' => self::VERSION_X30,
        'X3.1' => self::VERSION_X30,
    );

    public static function getDiscuzVersions() {
        return self::$_discuzVersions;
    }

    public static function getDiscuzVersion() {
        return DISCUZ_VERSION;
    }

    public static function getMobcentDiscuzVersion() {
        if (isset(self::$_discuzVersions[self::getDiscuzVersion()]))
            return self::$_discuzVersions[self::getDiscuzVersion()];
        else {
            $version = end(self::$_discuzVersions);
            reset(self::$_discuzVersions);
            return $version;
        }
    }

    public static function getFuncNameWithVersion($funcName) {
        return sprintf("%s_%s", $funcName, self::getMobcentDiscuzVersion());
    }

    public static function getDiscuzCommonSetting($key='') {
        global $_G;
        $config = $_G['setting'];
        
        if ($key == '') {
            return $config;
        } else {
            return isset($config[$key]) ? $config[$key] : false;
        }   
    }
}

// xss debug fixed
$tempMethod = $_SERVER['REQUEST_METHOD'];
$_SERVER['REQUEST_METHOD'] = 'POST';
define('DISABLEXSSCHECK', 1);

// cc 攻击防御
define('DISABLEDEFENSE', 1);

C::setconstant();
C::creatapp();
C::app()->init();

$_SERVER['REQUEST_METHOD'] = $tempMethod;

runhooks();