<?php
require_once ('../../source/class/class_core.php');
require_once ('../../source/discuz_version.php');
define ( 'ROOT_PATH', dirname ( __FILE__ ) . '/../' );
define ( 'CONFIG', '../config/config_global.php' );
class dynamicobject {
	public static $flag = '';
	public static function getDymanicObject($className) {
			if (file_exists ( ROOT_PATH . CONFIG )) {
				include ROOT_PATH . CONFIG;
			} else {
				$_config = $default_config;
			}
			$version = 'x25';
			if (DISCUZ_VERSION == 'X2')
				$version = 'x20';
			
			$charset = 'gbk';
			if($_config ['output'] ['charset'] == 'utf-8')
				$charset = 'utf8';
			
		return $className . '_' .  $version . '_' . $charset;
	}
	
	public static function getShortDymanicObject($className) {
		$version = 'x25';
		if (DISCUZ_VERSION == 'X2')
			$version = 'x20';
		return $className . '_' .  $version;
	}

}

?>