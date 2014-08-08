<?php
class platSystem {
	private static $operatingSystem = 'Linux';
	private static $instanse;
	private static $code = 'UCS-2BE';
	public static function init(){
		if(!(self::$instanse instanceof self)){
			self::$instanse = new self;
		}
		return self::$instanse;
	}
	public function platform(){
		$platForm = php_uname('s');
		$system = ucfirst($platForm);
		if ($system == self::$operatingSystem){
			return self::$code;
		}else {
			return self::$code = 'UCS-2';
		}
	}
}

?>