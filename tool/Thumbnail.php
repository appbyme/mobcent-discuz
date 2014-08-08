<?php
class 	Thumbnail
{
	var $PICTURE_URL; 
	var $DEST_URL; 
	var $path;
	var $PICTURE_CREATE; 
	var $TURE_COLOR; 

	var $PICTURE_WIDTH; 
	var $PICTURE_HEIGHT; 


	var $MARK_TYPE=1;
	var $WORD; 
	var $WORD_X; 
	var $WORD_Y; 
	var $FONT_TYPE;
	var $FONT_SIZE="12"; 
	var $FONT_WORD; 
	var $ANGLE=0; 
	var $FONT_COLOR="#ffffff"; 
	var $FONT_PATH="22.ttf"; 


	var $FORCE_URL; 
	var $FORCE_X=0; 
	var $FORCE_Y=0; 
	var $FORCE_START_X=0; 
	var $FORCE_START_Y=0; 

	var $PICTURE_TYPE; 
	var $PICTURE_MIME; 


	var $ZOOM=0; 
	var $ZOOM_MULTIPLE; 
	var $ZOOM_WIDTH; 
	var $ZOOM_HEIGHT; 

	var $CUT_TYPE=1; 
	var $CUT_X=0; 
	var $CUT_Y=0; 
	var $CUT_WIDTH=100; 
	var $CUT_HEIGHT=100; 
 
	var $SHARP="5.0"; 

	 
	var $ALPHA='100'; 
	var $ALPHA_X="90";
	var $ALPHA_Y="50";

	 
	var $CIRCUMROTATE="90.0"; 

	 
	var $ERROR=array(
	'unalviable'=>"Didn't find related pictures!"
	);

	 
	function __construct($PICTURE_URL){
		$this->get_info($PICTURE_URL);
	}
	function get_info($PICTURE_URL){
		@$SIZE=getimagesize($PICTURE_URL);
		if(!$SIZE){
		}
		$this->PICTURE_MIME=$SIZE['mime'];
		$this->PICTURE_WIDTH=$SIZE[0];
		$this->PICTURE_HEIGHT=$SIZE[1];
		switch($SIZE[2]){
		   case 1:
				$this->PICTURE_CREATE=imagecreatefromgif($PICTURE_URL);
				$this->PICTURE_TYPE="imagejpeg";
				$this->PICTURE_EXT="jpg";
		   break;
		   case 2:
				$this->PICTURE_CREATE=imagecreatefromjpeg($PICTURE_URL);
				$this->PICTURE_TYPE="imagegif";
				$this->PICTURE_EXT="gif";
		   break;
		   case 3:
				$this->PICTURE_CREATE=imagecreatefrompng($PICTURE_URL);
				$this->PICTURE_TYPE="imagepng";
				$this->PICTURE_EXT="png";
		   break;
		}
 
		preg_match_all("/([0-f]){2,2}/i",$this->FONT_COLOR,$MATCHES);
		if(count($MATCHES)==3){
		   $this->RED=hexdec($MATCHES[0][0]);
		   $this->GREEN=hexdec($MATCHES[0][1]);
		   $this->BLUE=hexdec($MATCHES[0][2]);
		}
	}
	 
	function hex2dec(){
		preg_match_all("/([0-f]{2,2})/i",$this->FONT_COLOR,$MATCHES);
		if(count($MATCHES[0])==3){
			$this->RED=hexdec($MATCHES[0][0]);
			$this->GREEN=hexdec($MATCHES[0][1]);
			$this->BLUE=hexdec($MATCHES[0][2]);
		}else{
			exit('The wrong color format');
		}
	}
	function zoom_type($ZOOM_TYPE){
		$this->ZOOM=$ZOOM_TYPE;
	}
	
	function zoom($zoomvalue){
		if($this->ZOOM==0){
			if($this->PICTURE_WIDTH > $zoomvalue || $this->PICTURE_HEIGHT >$zoomvalue){
				
				$this->ZOOM_WIDTH=$this->PICTURE_WIDTH * $this->ZOOM_MULTIPLE;
				$this->ZOOM_HEIGHT=$this->PICTURE_HEIGHT * $this->ZOOM_MULTIPLE;
			}else{
				$this->ZOOM_WIDTH=$this->PICTURE_WIDTH;
				$this->ZOOM_HEIGHT=$this->PICTURE_HEIGHT;
			}
		}
		$this->TRUE_COLOR=imagecreatetruecolor($this->ZOOM_WIDTH,$this->ZOOM_HEIGHT);
		$WHITE=imagecolorallocate($this->TRUE_COLOR,255,255,255);
		imagefilledrectangle($this->TRUE_COLOR,0,0,$this->ZOOM_WIDTH,$this->ZOOM_HEIGHT,$WHITE);
		imagecopyresized($this->TRUE_COLOR,$this->PICTURE_CREATE,0,0,0,0,$this->ZOOM_WIDTH,$this->ZOOM_HEIGHT,$this->PICTURE_WIDTH,$this->PICTURE_HEIGHT);
		
	}
	function cut($zoom=0){
		$this->TRUE_COLOR=imagecreatetruecolor($this->CUT_WIDTH,$this->CUT_WIDTH);
		if(!$zoom){
			imagecopy($this->TRUE_COLOR,$this->PICTURE_CREATE, 0, 0, $this->CUT_X, $this->CUT_Y,$this->CUT_WIDTH,$this->CUT_HEIGHT);
		}else{
			$w=$this->PICTURE_WIDTH;
			$h=$this->PICTURE_HEIGHT;
			if(min($w,$h,$this->CUT_WIDTH,$this->CUT_HEIGHT)==0)exit('Cut out size is zero, or obtaining image size');
			$bl=$this->CUT_WIDTH/$this->CUT_HEIGHT;
			$bl1=$w/$h;
			if($bl>$bl1){
				$h=floor($w*$bl);
			}elseif($bl<$bl1){
				$w=floor($h/$bl);
			}
			imagecopyresampled($this->TRUE_COLOR,$this->PICTURE_CREATE,0, 0,$this->CUT_X, $this->CUT_Y,$this->CUT_WIDTH,$this->CUT_HEIGHT,$w, $h);
		}
	}
	 
	function _mark_text(){
		$this->TRUE_COLOR=imagecreatetruecolor($this->PICTURE_WIDTH,$this->PICTURE_HEIGHT);
		$this->WORD=iconv('gb2312','utf-8',$this->FONT_WORD);
		$TEMP = imagettfbbox($this->FONT_SIZE,0,$this->FONT_PATH,$this->WORD);
		$WORD_LENGTH=strlen($this->WORD);
		$WORD_WIDTH =$TEMP[2] - $TEMP[6];
		$WORD_HEIGHT =$TEMP[3] - $TEMP[7];
		 
		if($this->WORD_X==""){
			$this->WORD_X=$this->PICTURE_WIDTH-$WORD_WIDTH;
		}
		if($this->WORD_Y==""){
			$this->WORD_Y=$this->PICTURE_HEIGHT-$WORD_HEIGHT;
		}
		imagesettile($this->TRUE_COLOR,$this->PICTURE_CREATE);
		imagefilledrectangle($this->TRUE_COLOR,0,0,$this->PICTURE_WIDTH,$this->PICTURE_HEIGHT,IMG_COLOR_TILED);
		$TEXT2=imagecolorallocate($this->TRUE_COLOR,$this->RED,$this->GREEN,$this->BLUE);
		imagettftext($this->TRUE_COLOR,$this->FONT_SIZE,$this->ANGLE,$this->WORD_X,$this->WORD_Y,$TEXT2,$this->FONT_PATH,$this->WORD);
	}
	 
	function _mark_picture(){
		@$SIZE=getimagesize($this->FORCE_URL);
		if(!$SIZE){
		}
		$FORCE_PICTURE_WIDTH=$SIZE[0];
		$FORCE_PICTURE_HEIGHT=$SIZE[1];
		switch($SIZE[2]){
			case 1:
				$FORCE_PICTURE_CREATE=imagecreatefromgif($this->FORCE_URL);
				$FORCE_PICTURE_TYPE="gif";
				break;
			case 2:
				$FORCE_PICTURE_CREATE=imagecreatefromjpeg($this->FORCE_URL);
				$FORCE_PICTURE_TYPE="jpg";
				break;
			case 3:
				$FORCE_PICTURE_CREATE=imagecreatefrompng($this->FORCE_URL);
				$FORCE_PICTURE_TYPE="png";
				break;
		}
		 
		$this->NEW_PICTURE=$this->PICTURE_CREATE;
		if($FORCE_PICTURE_WIDTH>$this->PICTURE_WIDTH){
			$CREATE_WIDTH=$FORCE_PICTURE_WIDTH-$this->FORCE_START_X;
		}else{
			$CREATE_WIDTH=$this->PICTURE_WIDTH;
		}
		if($FORCE_PICTURE_HEIGHT>$this->PICTURE_HEIGHT){
			$CREATE_HEIGHT=$FORCE_PICTURE_HEIGHT-$this->FORCE_START_Y;
		}else{
			$CREATE_HEIGHT=$this->PICTURE_HEIGHT;
		}
		 
		$NEW_PICTURE_CREATE=imagecreatetruecolor($CREATE_WIDTH,$CREATE_HEIGHT);
		$WHITE=imagecolorallocate($NEW_PICTURE_CREATE,255,255,255);
		imagecopy($NEW_PICTURE_CREATE, $this->PICTURE_CREATE, 0, 0, 0, 0,$this->PICTURE_WIDTH,$this->PICTURE_HEIGHT);
		imagecopy($NEW_PICTURE_CREATE, $FORCE_PICTURE_CREATE, $this->FORCE_X, $this->FORCE_Y, $this->FORCE_START_X, $this->FORCE_START_Y,$FORCE_PICTURE_WIDTH,$FORCE_PICTURE_HEIGHT);
		$this->TRUE_COLOR=$NEW_PICTURE_CREATE;
	}
	function alpha_(){
		$this->TRUE_COLOR=imagecreatetruecolor($this->PICTURE_WIDTH,$this->PICTURE_HEIGHT);
		$rgb="#CDCDCD";
		$tran_color="#000000";
		for($j=0;$j<=$this->PICTURE_HEIGHT-1;$j++){
			for ($i=0;$i<=$this->PICTURE_WIDTH-1;$i++)
			{
			$rgb = imagecolorat($this->PICTURE_CREATE,$i,$j);
			$r = ($rgb >> 16) & 0xFF;
			$g = ($rgb >> 8) & 0xFF;
			$b = $rgb & 0xFF;
			$now_color=imagecolorallocate($this->PICTURE_CREATE,$r,$g,$b);
			if ($now_color==$tran_color)
			{
			continue;
			}
			else
			{
			$color=imagecolorallocatealpha($this->PICTURE_CREATE,$r,$g,$b,$ALPHA);
			imagesetpixel($this->PICTURE_CREATE,$ALPHA_X+$i,$ALPHA_Y+$j,$color);
			}
			$this->TRUE_COLOR=$this->PICTURE_CREATE;
	
	}
	}
	}
	 
	function turn_y(){
		$this->TRUE_COLOR=imagecreatetruecolor($this->PICTURE_WIDTH,$this->PICTURE_HEIGHT);
		for ($x = 0; $x < $this->PICTURE_WIDTH; $x++)
		{
		imagecopy($this->TRUE_COLOR, $this->PICTURE_CREATE, $this->PICTURE_WIDTH - $x - 1, 0, $x, 0, 1, $this->PICTURE_HEIGHT);
		}
		}
		
		function turn_r1(){
			$this->TRUE_COLOR=imagecreatetruecolor($this->PICTURE_HEIGHT,$this->PICTURE_WIDTH);
			for ($x = 0; $x < $this->PICTURE_WIDTH; $x+=1)
			{
				for($y = 0; $y < $this->PICTURE_HEIGHT; $y+=1){
					imagecopy($this->TRUE_COLOR, $this->PICTURE_CREATE, $y, $x, $this->PICTURE_WIDTH-$x, $this->PICTURE_HEIGHT-$y, 1,1);
				}
			}
		}
		function turn_r2(){
			$this->TRUE_COLOR=imagecreatetruecolor($this->PICTURE_HEIGHT,$this->PICTURE_WIDTH);
			for ($x = 0; $x < $this->PICTURE_WIDTH; $x+=1)
			{
				for($y = $this->PICTURE_HEIGHT; $y >0; $y-=1){
					imagecopy($this->TRUE_COLOR, $this->PICTURE_CREATE, $y, $x, $x, $y, 1,1);
				}
			}
		}
		 
		function turn_x(){
			$this->TRUE_COLOR=imagecreatetruecolor($this->PICTURE_WIDTH,$this->PICTURE_HEIGHT);
			for ($y = 0; $y < $this->PICTURE_HEIGHT; $y++)
			{
			imagecopy($this->TRUE_COLOR, $this->PICTURE_CREATE, 0, $this->PICTURE_HEIGHT - $y - 1, 0, $y, $this->PICTURE_WIDTH, 1);
			}
			}
		 
		function turn(){
			$this->TRUE_COLOR=imagecreatetruecolor($this->PICTURE_WIDTH,$this->PICTURE_HEIGHT);
			imageCopyResized($this->TRUE_COLOR,$this->PICTURE_CREATE,0,0,0,0,$this->PICTURE_WIDTH,$this->PICTURE_HEIGHT,$this->PICTURE_WIDTH,$this->PICTURE_HEIGHT);
			$WHITE=imagecolorallocate($this->TRUE_COLOR,255,255,255);
			$this->TRUE_COLOR=imagerotate ($this->TRUE_COLOR, $this->CIRCUMROTATE, $WHITE);
		}
		
		 
		function sharp(){
			$this->TRUE_COLOR=imagecreatetruecolor($this->PICTURE_WIDTH,$this->PICTURE_HEIGHT);
			$cnt=0;
			for ($x=0; $x<$this->PICTURE_WIDTH; $x++){
				for ($y=0; $y<$this->PICTURE_HEIGHT; $y++)
				{
				$src_clr1 = imagecolorsforindex($this->TRUE_COLOR, imagecolorat($this->PICTURE_CREATE, $x-1, $y-1));
				$src_clr2 = imagecolorsforindex($this->TRUE_COLOR, imagecolorat($this->PICTURE_CREATE, $x, $y));
				$r = intval($src_clr2["red"]+$this->SHARP*($src_clr2["red"]-$src_clr1["red"]));
				$g = intval($src_clr2["green"]+$this->SHARP*($src_clr2["green"]-$src_clr1["green"]));
						$b = intval($src_clr2["blue"]+$this->SHARP*($src_clr2["blue"]-$src_clr1["blue"]));
						$r = min(255, max($r, 0));
						$g = min(255, max($g, 0));
						$b = min(255, max($b, 0));
						if (($DST_CLR=imagecolorexact($this->PICTURE_CREATE, $r, $g, $b))==-1)
							$DST_CLR = imagecolorallocate($this->PICTURE_CREATE, $r, $g, $b);
							$cnt++;
							if ($DST_CLR==-1) die("color allocate faile at $x, $y ($cnt).");
							imagesetpixel($this->TRUE_COLOR, $x, $y, $DST_CLR);
				}
			}
		}
		
		function mkrdir($dir){
			
			return is_dir($dir) or ($this->mkrdir(dirname($dir)) and mkdir($dir, 0777,true));
		}
		 
		function save_picture($showpic=0){
			$OUT=$this->PICTURE_TYPE;
			if(function_exists($OUT)){
				if(isset($_SERVER['HTTP_USER_AGENT']))
				{
					$ua = strtoupper($_SERVER['HTTP_USER_AGENT']);
					if(!preg_match('/^.*MSIE.*\)$/i',$ua))
					{
						// header("Content-type:$this->PICTURE_MIME");
					}
				}
				if(!$this->TRUE_COLOR){
					//exit($this->ERROR['unavilable']);
					return false;
				}else{
					$url = $this->mkrdir($this->path);
					return $OUT($this->TRUE_COLOR,$this->DEST_URL);
				}
				
			}
		}
		 
		function __destruct(){
			@imagedestroy($this->TRUE_COLOR);
			@imagedestroy($this->PICTURE_CREATE);
		}
		
		function zoomcutPic($imgurl, $folder ,$fileName , $zoom){
			$this->PICTURE_URL=$imgurl;
			$path = pathinfo($imgurl);
			$filename = $path['filename']; 
			$extension = $path['extension']; 
			$this->ZOOM=0; 
			if($this->PICTURE_WIDTH >= $this->PICTURE_HEIGHT){
				$this->ZOOM_MULTIPLE = $zoom / $this->PICTURE_HEIGHT; 
			}else{
				$this->ZOOM_MULTIPLE = $zoom / $this->PICTURE_WIDTH; 
			}
			$this->path= $folder;
			$this->DEST_URL=$folder .'/' . $fileName;
			$this->zoom($zoom);
			return $this->save_picture(1);
		}
}
?>