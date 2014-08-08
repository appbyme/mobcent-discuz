<?php

require_once '../../source/class/class_core.php';

class mobcent_core extends discuz_core
{
	private static $_tables;
	private static $_imports;
	private static $_app;
	private static $_memory;
	
	public static function import($name, $folder = '', $force = true) {
		$key = $folder.$name;
		if(!isset(self::$_imports[$key])) {
			if(strpos($name, '/') !== false) {
				$pre = basename(dirname($name));
				$filename = dirname(__FILE__).'/'.dirname($name).'/table_'.basename($name).'.php';
			} else {
				$filename = $name.'.php';
			}
			if(file_exists($filename)) {
				self::$_imports[$key] = true;
				return include_once $filename;
			} elseif(!$force) {
				return false;
			} else {
				throw new Exception('Oops! System file lost ERROR: '.$filename);
			}
		}
		return true;
	}
	public static function t($name)
	{
		$classname = 'table_'.$name;
		
		if(!class_exists($classname))
		{
			throw new Exception('No! class file is not exists: '.$classname);
		}
		else 
		{
			$path = 'table/x20/'.$name;
 			self::import($path);
			self::$_tables = new $classname;
			return self::$_tables;
		}
	}
	public static function  autoload($class)
	{
		$class = strtolower($class);
		list($folder) = explode('_',$class);
		$path =$folder.'/x20/'.substr($class,strlen($folder)+1);
		try
		{
			self::import($path);
			return true;
		}
		catch(Exception $e)
		{
			print $e->getMessage();
			exit();
		}
	}
}
class C extends mobcent_core{}
if(function_exists('spl_autoload_register'))
{
	spl_autoload_register(array('mobcent_core','autoload'));
}
else
{
	function _autoload($class)
	{
		return mobcent_core::autoload($class);
	}
}