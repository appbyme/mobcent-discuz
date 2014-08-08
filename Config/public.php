<?php
class Common
{
	private static $_instance = '';
	private static $_path = '';
	private static $_projectNmae = '';
	private static $_documentRoot = '';
	private static $_filePath = '';
	private static $operatingSystem = 'Linux';
	private static $instanse;
	private static $code = 'UCS-2BE';
	 
	private function __construct()
	{
		self::getServerName();
	}
	 
	 private function __clone()
	 {
	 	trigger_error('Clone is not allow',E_USER_ERROR);
	 }
	 
	 
	 public static function getInstance()
	 {
	 	if(!self::$_instance instanceof self)
	 	{
	 		self::$_instance = new self();
	 	}
	 	return self::$_instance;
	 }
	 public function platform(){
	 	if(!function_exists(php_uname))
	 	{
	 		return self::$code;
	 	}
	 	$platForm = php_uname('s');
	 	$system = ucfirst($platForm);
	 	if ($system == self::$operatingSystem){
	 		return self::$code;
	 	}else {
	 		return self::$code = 'UCS-2';
	 	}
	 }
	 
	 
	 public static function getServerName() {
	 	$ServerName = strtolower ( $_SERVER ['SERVER_NAME'] ? $_SERVER ['SERVER_NAME'] : $_SERVER ['HTTP_HOST'] );
	 	if (strpos ( $ServerName, 'http://' )) {
	 		self::$_path = str_replace ( 'http://', '', $ServerName );
	 	}
	 	self::$_path = 'http://' . $ServerName . "/";
	 	
	 	return self::$_path;
	 }
	 
	 public static function getProjectName() 
	 {
		$projectName = strtolower ( $_SERVER ['PHP_SELF'] );
		$index = strpos ( $projectName, '/mobcent/' );
		if ($index >= 0) {
			self::$_projectNmae = substr ( $projectName, 0, $index );
		}
		return self::$_projectNmae;
	}
	 
	public static function getDocumentRoot() {
		self::$_documentRoot = $_SERVER ['DOCUMENT_ROOT'];
		return self::$_documentRoot;
	}
	 
	public static function getSelfFolder() {
	$fileName = $_SERVER ['DOCUMENT_ROOT'] . $_SERVER ['PHP_SELF'];
	self::$_filePath = dirname ( $fileName );
	return self::$_filePath;
	}
	 
	public static function get_unicode_charset($str,$encode = UC_DBCHARSET){
		{
			$temp = explode('\u',$str);
			$rslt = array();
			array_shift($temp);
			foreach($temp as $k => $v) {
				$v = hexdec($v);
				$rslt[] = '&#' . $v . ';';
			}
			$rslt = implode('',$rslt);
			if($encode == 'utf8')
				return  self::unicode_decode($rslt);
			else{
				return  iconv('utf-8','gbk',self::unicode_decode($rslt));
			}
		}
	}
	
	public static function get_web_unicode_charset($str,$encode = UC_DBCHARSET)
	{
		$temp = explode('\u',$str);
		$rslt = array();
	 	array_shift($temp);
		foreach($temp as $k => $v) { 
	 		$v = hexdec($v);
			$rslt[] = '&#' . $v . ';';
	 	}
 		$rslt = implode('',$rslt);
		return  $rslt;
	}
	
	
	
	function unicode_encode($str, $encoding = 'UTF-8', $prefix = '&#', $postfix = ';') {
		$system = Common::getInstance();
		$unicode = $system -> platform();
		$name = iconv('UTF-8', $unicode, $str);
		$len = strlen($name);
		$str = '';
		for($i=0;$i<$len-1;$i=$i+2){
			$c = $name[$i];
			$c2 = $name[$i + 1];
			if (ord($c) > 0){  
				
				if(strlen('\u'.base_convert(ord($c), 10, 16).base_convert(ord($c2), 10, 16))==5){
					$str .= '\u'.base_convert(ord($c), 10, 16).base_convert(ord($c2), 10, 16).'0';
				}else{
					$str .= '\u'.base_convert(ord($c), 10, 16).base_convert(ord($c2), 10, 16);
				}
			}else{
				$str .= $c2;
			}
		}
		return $str;
 
	}
	public static function unicode_decode($unistr, $encoding = 'UTF-8', $prefix = '&#', $postfix = ';') {
		$arruni = explode($prefix, $unistr);
		$unistr = '';
		for($i = 1, $len = count($arruni); $i < $len; $i++) {
			if (strlen($postfix) > 0) {
				$arruni[$i] = substr($arruni[$i], 0, strlen($arruni[$i]) - strlen($postfix));
			}
			$temp = intval($arruni[$i]);
			$unistr .= ($temp < 256) ? chr(0) . chr($temp) : chr($temp / 256) . chr($temp % 256);
		}
			$system = Common::getInstance();
			$unicode = $system -> platform();
			return iconv($unicode, $encoding, $unistr);
	}
	
	function randomkeys($length) {
		$returnStr='';
		$pattern ='1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$size = strlen($pattern);
		for($i= 0; $i < $length; $i ++) {
			$returnStr .= $pattern {mt_rand ( 0,$size-1 )}; 
		}
		return $returnStr;
	}
}
$db = Common::getInstance();
define ( 'DISCUZSERVERURL', Common ::getServerName() );
define ( 'PROJECT_NAME', Common ::getProjectName() );
define ( 'DOCUMENT_ROOT', Common ::getDocumentRoot() );
define ( 'SELF_FOLDER', Common ::getSelfFolder() );
define ( 'DBCHARSET', 'gb2312' );
define ( 'MOBCENBTYPE2', '||' );

define ( 'DISCUZIMGURLBIG', '../data/attachment/forum/mobcentBigPreview/' );
define ( 'DISCUZIMGURLSMALL', '../data/attachment/forum/mobcentSmallPreview/' );