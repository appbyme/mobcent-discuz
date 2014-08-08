<?php 

class topic {
// 	const SMALL = '/data/attachment/forum/mobcentSmallPreview/';
// 	const BIG = '/data/attachment/forum/mobcentBigPreview/';
// 	const XGSIZE = '/data/attachment/forum/xgsize/';
// 	const SMALLFTP = '/forum/mobcentSmallPreview/';
// 	const BIGFTP = '/forum/mobcentBigPreview/';
// 	const XGSIZEFTP = '/forum/xgsize/';

	public $small = '/data/attachment/forum/mobcentSmallPreview/';
	public $big = '/data/attachment/forum/mobcentBigPreview/';
	public $xgsize = '/data/attachment/forum/xgsize/';
	
	function checkRemoteFileExists($file) {
		return (bool)fopen($file, 'rb');
	}
	
	/*rx 20130909*/
	public function commonTopicFtp(){
		$setting_list = DB::query("SELECT * FROM ".DB::table('common_setting'));
		while($setting_value = DB::fetch($setting_list)) {
			$setting[] = $setting_value;
		}
		foreach($setting as $st){
			if($st[skey]=='ftp'){
				$myval=unserialize($st[svalue]);
				$ftp_attachurl=$myval[attachurl];
			}
		}
		return $ftp_attachurl;
	}
	public function commonTopicPic($path,$ret_suffix_xgsize,$ret_suffix,$gan_path,$ret_path_small,$ret_path_big,$sizeBig=''){
		$size = 200;
		if($sizeBig==''){
			$sizeBig = 640;
		}
		$ret_graph_small_picture_path = ($this->small);
		$ret_graph_big_picture_path = ($this->big);
		$ret_graph_xgsize_picture_path = ($this->xgsize); 
		$fileName = $path['filename'].'.'.$path['extension'];
		$Url = explode('/',$this->getRootFolder(SELF_FOLDER));
		unset($Url[count($Url)-1]);
		$Url = implode('/',$Url); 
		$ret_path_attachmentImg = ($this->getPath()).'/..'.$ret_suffix; 
		if(file_exists($ret_path_attachmentImg)){//print_r($ret_path_attachmentImg);exit;
			$filename = '/mobcent' . $ret_suffix_xgsize; 
		}else{
			$pic = new Thumbnail($gan_path);
			if($pic->zoomcutPic($gan_path,$ret_path_small ,$fileName ,$size) && $pic->zoomcutPic($gan_path,$ret_path_big,$fileName ,$sizeBig)){ 
				$filename = '/mobcent' . $ret_graph_xgsize_picture_path . $path['dirname'].'/'.$path['filename'].'.'.$path['extension'];
			}
		}  
		return $filename;
	}
	public function commonTopicImg($group,$mk){ 
		$origan_path = ($this->getPath()) . "/../../data/attachment/forum/" .$group ['attachment']; 
		$origan = explode('/',$group ['attachment']);	 
		$origan_path_date =($this->getPath()) . "/../../data/attachment/forum/".date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1];
		if(file_exists($origan_path)){
			$path=pathinfo( $group ['attachment']);
			$ret_suffix_xgsize = ($this->xgsize).$group ['attachment']; 
			$ret_suffix = ($this->big).$group ['attachment'];
			$gan_path=$origan_path;
			$ret_path_small=($this->getPath()). '/..'.($mk==1?($this->big) : ($this->small)).$path['dirname'].'/';
			$ret_path_big=($this->getPath()). '/..'. ($this->big).$path['dirname'].'/';
			return $this->commonTopicPic($path,$ret_suffix_xgsize,$ret_suffix,$gan_path,$ret_path_small,$ret_path_big); 
		}else if(file_exists($origan_path_date)){
			$path=pathinfo( date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1]);
			$ret_suffix_xgsize =($this->xgsize) . '/'.date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1];
			$ret_suffix = ($this->small). '/'.date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1];
			$gan_path=$origan_path_date;
			$ret_path_small=($this->getPath()). '/..'.($this->small).$path['dirname'].'/';
			$ret_path_big=($this->getPath()). '/..'.($this->big).$path['dirname'].'/';
			return $this->commonTopicPic($path,$ret_suffix_xgsize,$ret_suffix,$gan_path,$ret_path_small,$ret_path_big);
		}else{
			return "";
		}
	}
	public function commonTopicImgFTP($group,$mk){
// 		print_r($group);exit;
		$origan_path = $this->commonTopicFtp()."/forum/".$group ['attachment'];
		$origan = explode('/',$group ['attachment']);
		$origan_path_date = $this->commonTopicFtp()."/forum/".date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1];
		//pan duan wang luo tu pian shi fou cun zai
		if ($this->checkRemoteFileExists($origan_path)){
			$path=pathinfo( $group ['attachment']);
			$ret_suffix_xgsize = ($this->xgsize).$group ['attachment'];
			$ret_suffix = ($this->big).$group ['attachment'];
			$gan_path=$origan_path;
			$ret_path_small=($this->getPath()). '/..'.($mk==1?($this->big) : ($this->small)).$path['dirname'].'/';
			$ret_path_big=($this->getPath()). '/..'. ($this->big).$path['dirname'].'/';
			return $this->commonTopicPic($path,$ret_suffix_xgsize,$ret_suffix,$gan_path,$ret_path_small,$ret_path_big); 
		}else if($this->checkRemoteFileExists($origan_path_date)){
			$path=pathinfo( date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1]);
			$ret_suffix_xgsize = ($this->xgsize) . '/'.date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1];
			$ret_suffix = ($this->small) . '/'.date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1];
			$gan_path=$origan_path_date;
			$ret_path_small=($this->getPath()). '/..'. ($this->small).$path['dirname'].'/';
			$ret_path_big=($this->getPath()). '/..'.($this->big).$path['dirname'].'/';
			return $this->commonTopicPic($path,$ret_suffix_xgsize,$ret_suffix,$gan_path,$ret_path_small,$ret_path_big);
		}else{
			return "";
		}
	}
	/*end rx 20130909*/
	
	/*-----------------------------S---T---A---R---T---------------------------------*/
	public function parseTradeTopicImg($group){
		$mk=0;
		$this->commonTopicImg($group,$mk);
	}
	public function parseBigTradeTopicImg($group){
		$mk=0;
		$this->commonTopicImg($group,$mk);
	}
	function parseBigTargeImage($group){
		$mk=1;
		$this->commonTopicImg($group,$mk);
	}
	//used:topiclist/
	public function parseTargeImage($group){ 
		$mk=0;
		if($_GET['sdkVersion']!="1.0.0"){
			$this->xgsize=$this->small;
		}
		if($group[remote]==1){ //yuan cheng tu pian
			return $this->commonTopicImgFTP($group,$mk);
		}else{
			return $this->commonTopicImg($group,$mk);
		}
	}
	function parseTargeThumbImage($pic){
		$mk=0;
		if($_GET['sdkVersion']!="1.0.0"){
			$this->xgsize=$this->small;
		}
		if($pic[remote]==1){ 
			return $this->commonTopicImgFTP($pic,$mk);
		}else{
			$ret_graph_small_picture_path = ($this->small);
			$ret_graph_big_picture_path = ($this->big);
			$ret_graph_xgsize_picture_path = ($this->xgsize);
			$origan = explode('/',$pic ['attachment']);
			$origan_path_date =($this->getPath()). "/../../data/attachment/forum/".date('Ym/d',$pic['dateline']) .'/'.$origan[count($origan)-1];
			$origan_path = ($this->getPath()) . "/../../data/attachment/forum/" .$pic ['attachment'];
			$path=pathinfo( $pic ['attachment']);
			$path['filename']=$path['filename'].'_240';
			$fileName = $path['filename'].'.'.$path['extension'];
			$ret_suffix_xgsize = '/data/attachment/forum/thumbnail/'.$path['dirname'].'/'. $fileName;
			$ret_suffix = '/data/attachment/forum/thumbnail/'.$path['dirname'].'/'. $fileName;
			$ret_path_attachmentImg = ($this->getPath()) .'/..' . $ret_suffix;
			$ret_path = ($this->getPath()) . '/../data/attachment/forum/thumbnail/'.$path['dirname'].'/';
			$size = 240;
			if(file_exists($origan_path)){
				if(file_exists($ret_path_attachmentImg)){
					$pic_path = '/mobcent'. $ret_suffix_xgsize;
				}else{
					$thumbnail = new Thumbnail($origan_path);
					if($thumbnail->zoomcutPic($origan_path,$ret_path ,$fileName ,$size)){
						$pic_path = '/mobcent/data/attachment/forum/thumbnail/'.$path['dirname'].'/'.$path['filename'].'.'.$path['extension'];
					} else {
						$pic_path = '/data/attachment/forum' . $pic['attachment'];
					}
				}
			}else if(file_exists($origan_path_date)){
				$path=pathinfo( date('Ym/d',$pic['dateline']) .'/'.$origan[count($origan)-1]);
				$fileName = $path['filename'].'.'.$path['extension'];
				$Url = explode('/',$this->getRootFolder(SELF_FOLDER));
				unset($Url[count($Url)-1]);
				$Url = implode('/',$Url);
				$ret_suffix_xgsize = $ret_graph_small_picture_path . '/'.date('Ym/d',$pic['dateline']) .'/'.$origan[count($origan)-1];
				$ret_suffix = $ret_graph_small_picture_path . '/'.date('Ym/d',$pic['dateline']) .'/'.$origan[count($origan)-1];
				$ret_path_attachmentImg = ($this->getPath()) .'/..' . $ret_suffix;
				$ret_path_small = ($this->getPath()). '/..'. $ret_graph_small_picture_path . $path['dirname'].'/';
				$ret_path_big = ($this->getPath()). '/..'. $ret_graph_big_picture_path . $path['dirname'].'/';
				$size = 240;
				if(file_exists($ret_path_attachmentImg)){
					$pic_path = '/mobcent' . $ret_suffix_xgsize;
				}else{
					if($pic->zoomcutPic($origan_path_date,$ret_path ,$fileName ,$size)){
						$pic_path = '/mobcent/data/attachment/forum/thumbnail/'.$path['dirname'].'/'.$path['filename'].'.'.$path['extension'];
					}
				}
			}
			return $pic_path;
		}
	}
	
	
	
	
	public function parseTradeTopicImgFTP($group){
		$origan_path = $this->commonTopicFtp()."forum/".$group ['attachment'];
		$origan = explode('/',$group ['attachment']);
		$origan_path_date = $this->commonTopicFtp()."forum/".date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1];
		//pan duan wang luo tu pian shi fou cun zai
		if ($this->checkRemoteFileExists($origan_path)){
			$path=pathinfo( $group ['attachment']);
			$ret_suffix_xgsize =($this->xgsize).$group ['attachment'];
			$ret_suffix = ($this->big).$group ['attachment'];
			$gan_path=$origan_path;
			$this->commonTopicPic($path,$ret_suffix_xgsize,$ret_suffix,$gan_path);
		}else if($this->checkRemoteFileExists($origan_path_date)){
			$path=pathinfo( date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1]);
			$ret_suffix_xgsize = ($this->xgsize). '/'.date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1];
			$ret_suffix = ($this->small). '/'.date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1];
			$gan_path=$origan_path_date;
			$this->commonTopicPic($path,$ret_suffix_xgsize,$ret_suffix,$gan_path);
		}
	}
	
	
	/*-----------------------------E-----N------D----------------------------------*/
	public function parseTradeTopicImg_old($group){ 
		$ret_graph_small_picture_path = '/data/attachment/forum/mobcentSmallPreview/';
		$ret_graph_big_picture_path = '/data/attachment/forum/mobcentBigPreview/';
		$ret_graph_xgsize_picture_path = '/data/attachment/forum/xgsize/';
		$origan_path = ($this->getPath()) . "/../../data/attachment/forum/" .$group ['attachment'];
		$origan = explode('/',$group ['attachment']);
		$origan_path_date =($this->getPath()) . "/../../data/attachment/forum/".date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1];
		$path=pathinfo( $group ['attachment']);
		$fileName = $path['filename'].'.'.$path['extension'];
		$Url = explode('/',$this->getRootFolder(SELF_FOLDER));
		unset($Url[count($Url)-1]);
		$Url = implode('/',$Url);
		$ret_suffix = $ret_graph_small_picture_path . $group ['attachment'];
		$ret_suffix_xgsize = $ret_graph_xgsize_picture_path . $group ['attachment'];
		$ret_path_attachmentImg = ($this->getPath()) .'/..' . $ret_suffix;
		$ret_path_small = ($this->getPath()) . '/..'. $ret_graph_small_picture_path . $path['dirname'].'/';
		$ret_path_big = ($this->getPath()) . '/..'. $ret_graph_big_picture_path . $path['dirname'].'/';
		$size = 160;
		$sizeBig = 240;
		if(file_exists($origan_path)){
			if(file_exists($ret_path_attachmentImg)){
				$filename = '/mobcent' . $ret_suffix_xgsize;
			}else{
				$pic = new Thumbnail($origan_path);
				if($pic->zoomcutPic($origan_path,$ret_path_small ,$fileName ,$size) && $pic->zoomcutPic($origan_path,$ret_path_big,$fileName ,$sizeBig)){
					$filename = '/mobcent' . $ret_graph_xgsize_picture_path . $path['dirname'].'/'.$path['filename'].'.'.$path['extension'];
				}
			}
		}else if(file_exists($origan_path_date)){
			$path=pathinfo( date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1]);
			$fileName = $path['filename'].'.'.$path['extension'];
			$Url = explode('/',$this->getRootFolder(SELF_FOLDER));
			unset($Url[count($Url)-1]);
			$Url = implode('/',$Url);
			$ret_suffix_xgsize = $ret_graph_xgsize_picture_path . '/'.date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1];
			$ret_suffix = $ret_graph_small_picture_path . '/'.date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1];
			$ret_path_attachmentImg = ($this->getPath()) .'/..' . $ret_suffix;
			$ret_path_small = ($this->getPath()) . '/..'. $ret_graph_small_picture_path . $path['dirname'].'/';
			$ret_path_big = ($this->getPath()). '/..'. $ret_graph_big_picture_path . $path['dirname'].'/';
			if(file_exists($ret_path_attachmentImg)){
				$filename = '/mobcent' . $ret_suffix_xgsize;
			}else{
				$pic = new Thumbnail($origan_path_date);
				if($pic->zoomcutPic($origan_path_date,$ret_path_small ,$fileName ,$size) && $pic->zoomcutPic($origan_path_date,$ret_path_big,$fileName ,$sizeBig)){
					$filename = '/mobcent' . $ret_graph_xgsize_picture_path . $path['dirname'].'/'.$path['filename'].'.'.$path['extension'];
				}
			}
		}
		return $filename;
	}
	public function parseBigTradeTopicImg_old($group){
		$ret_graph_small_picture_path = '/data/attachment/forum/mobcentSmallPreview/';
		$ret_graph_big_picture_path = '/data/attachment/forum/mobcentBigPreview/';
		$ret_graph_xgsize_picture_path = '/data/attachment/forum/xgsize/';
		$origan_path = ($this->getPath()) . "/../../data/attachment/forum/" .$group ['attachment'];
		$origan = explode('/',$group ['attachment']);
		$origan_path_date =($this->getPath()) . "/../../data/attachment/forum/".date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1];
		$path=pathinfo( $group ['attachment']);
		$fileName = $path['filename'].'.'.$path['extension'];
		$Url = explode('/',$this->getRootFolder(SELF_FOLDER));
		unset($Url[count($Url)-1]);
		$Url = implode('/',$Url);
		$ret_suffix_xgsize = $ret_graph_xgsize_picture_path . $group ['attachment'];
		$ret_suffix = $ret_graph_big_picture_path . $group ['attachment'];
		$ret_path_attachmentImg = ($this->getPath()).'/..' . $ret_suffix;
		$ret_path_small = ($this->getPath()) . '/..'. $ret_graph_small_picture_path . $path['dirname'].'/';
		$ret_path_big = ($this->getPath()). '/..'. $ret_graph_big_picture_path . $path['dirname'].'/';
		$size = 160;
		$sizeBig = 240;
		if(file_exists($origan_path)){
			if(file_exists($ret_path_attachmentImg)){
				$filename = '/mobcent' . $ret_suffix_xgsize;
			}else{
				$pic = new Thumbnail($origan_path);
				if($pic->zoomcutPic($origan_path,$ret_path_small ,$fileName ,$size) && $pic->zoomcutPic($origan_path,$ret_path_big,$fileName ,$sizeBig)){
					$filename = '/mobcent' . $ret_graph_xgsize_picture_path . $path['dirname'].'/'.$path['filename'].'.'.$path['extension'];
				}
			}
		}else if(file_exists($origan_path_date)){
			$path=pathinfo( date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1]);
			$fileName = $path['filename'].'.'.$path['extension'];
			$Url = explode('/',$this->getRootFolder(SELF_FOLDER));
			unset($Url[count($Url)-1]);
			$Url = implode('/',$Url);
			$ret_suffix_xgsize = $ret_graph_xgsize_picture_path . '/'.date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1];
			$ret_suffix = $ret_graph_small_picture_path . '/'.date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1];
			$ret_path_attachmentImg = ($this->getPath()).'/..' . $ret_suffix;
			$ret_path_small = ($this->getPath()). '/..'. $ret_graph_small_picture_path . $path['dirname'].'/';
			$ret_path_big = ($this->getPath()). '/..'. $ret_graph_big_picture_path . $path['dirname'].'/';
			if(file_exists($ret_path_attachmentImg)){
				$filename = '/mobcent' . $ret_suffix_xgsize;
			}else{
				$pic = new Thumbnail($origan_path_date);
				if($pic->zoomcutPic($origan_path_date,$ret_path_small ,$fileName ,$size) && $pic->zoomcutPic($origan_path_date,$ret_path_big,$fileName ,$sizeBig)){
					$filename = '/mobcent' . $ret_graph_xgsize_picture_path . $path['dirname'].'/'.$path['filename'].'.'.$path['extension'];
				}
			}
		}
		return $filename;
	}
	
	
	
	/*rx new added 20130827*/
	public function parseTradeTopicImgFTP_old($group){ 
		$setting_list = DB::query("SELECT * FROM ".DB::table('common_setting'));
		while($setting_value = DB::fetch($setting_list)) {
			$setting[] = $setting_value;
		}
		foreach($setting as $st){
			if($st[skey]=='ftp'){
				$myval=unserialize($st[svalue]);
				$ftp_attachurl=$myval[attachurl];
			}
		}
		$ret_graph_small_picture_path = '/data/attachment/forum/mobcentSmallPreview/';
		$ret_graph_big_picture_path = '/data/attachment/forum/mobcentBigPreview/';
		$ret_graph_xgsize_picture_path = '/data/attachment/forum/xgsize/';
		$origan = explode('/',$group ['attachment']);
		$origan_path = $this->commonTopicFtp()."forum/".$group ['attachment'];
		$origan_path_date = $this->commonTopicFtp()."forum/".date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1];
		$path=pathinfo( $group ['attachment']);
		$fileName = $path['filename'].'.'.$path['extension'];
		$Url = explode('/',$this->getRootFolder(SELF_FOLDER));
		unset($Url[count($Url)-1]);
		$Url = implode('/',$Url);
		$ret_suffix_xgsize = $ret_graph_xgsize_picture_path . $group ['attachment'];
		$ret_suffix = $ret_graph_small_picture_path . $group ['attachment'];
		$ret_path_attachmentImg = ($this->getPath()) .'/..' . $ret_suffix;
		$ret_path_small = ($this->getPath()) . '/..'. $ret_graph_small_picture_path . $path['dirname'].'/';
		$ret_path_big = ($this->getPath()) . '/..'. $ret_graph_big_picture_path . $path['dirname'].'/';
		$size = 160;
		$sizeBig = 240;
		$fileExists = @file_get_contents($origan_path, null, null, -1, 1) ? true : false;
		$fileExists_date = @file_get_contents($origan_path_date, null, null, -1, 1) ? true : false;
		if ($fileExists){
			if(file_exists($ret_path_attachmentImg)){
				$filename = '/mobcent' . $ret_suffix_xgsize;
			}else{
				$pic = new Thumbnail($origan_path);
				if($pic->zoomcutPic($origan_path,$ret_path_small ,$fileName ,$size) && $pic->zoomcutPic($origan_path,$ret_path_big,$fileName ,$sizeBig)){
					$filename = '/mobcent' . $ret_graph_xgsize_picture_path . $path['dirname'].'/'.$path['filename'].'.'.$path['extension'];
				}
			}
		}else if($fileExists_date){
			$path=pathinfo( date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1]);
			$fileName = $path['filename'].'.'.$path['extension'];
			$Url = explode('/',$this->getRootFolder(SELF_FOLDER));
			unset($Url[count($Url)-1]);
			$Url = implode('/',$Url);
			$ret_suffix_xgsize = $ret_graph_xgsize_picture_path . '/'.date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1];
			$ret_suffix = $ret_graph_small_picture_path . '/'.date('Ym/d',$group['dateline']) .'/'.$origan[count($origan)-1];
			$ret_path_attachmentImg = ($this->getPath()) .'/..' . $ret_suffix;
			$ret_path_small = ($this->getPath()) . '/..'. $ret_graph_small_picture_path . $path['dirname'].'/';
			$ret_path_big = ($this->getPath()). '/..'. $ret_graph_big_picture_path . $path['dirname'].'/';
			if(file_exists($ret_path_attachmentImg)){
				$filename = '/mobcent' . $ret_suffix_xgsize;
			}else{
				$pic = new Thumbnail($origan_path_date);
				if($pic->zoomcutPic($origan_path_date,$ret_path_small ,$fileName ,$size) && $pic->zoomcutPic($origan_path_date,$ret_path_big,$fileName ,$sizeBig)){
					$filename = '/mobcent' . $ret_graph_xgsize_picture_path . $path['dirname'].'/'.$path['filename'].'.'.$path['extension'];
				}
			}
		}
		return $filename;
	}
	/*end rx 20130827*/
	
	function parseBigTargeImage_old($picPath){
		$ret_graph_small_picture_path = '/data/attachment/forum/mobcentSmallPreview/';
		$ret_graph_big_picture_path = '/data/attachment/forum/mobcentBigPreview/';
		$ret_graph_xgsize_picture_path = '/data/attachment/forum/xgsize/';
		$origan_path = ($this->getPath()) . "/../../data/attachment/forum/" . $picPath['attachment'];
		$origan = explode('/',$picPath ['attachment']);
		$origan_path_date =($this->getPath()) . "/../../data/attachment/forum/".date('Ym/d',$picPath['dateline']) .'/'.$origan[count($origan)-1];
		$path=pathinfo( $picPath['attachment']);
		$fileName = $path['filename'].'.'.$path['extension'];
		$Url = explode('/',$this->getRootFolder(SELF_FOLDER));
		unset($Url[count($Url)-1]);
		$Url = implode('/',$Url);
		$ret_suffix_xgsize = $ret_graph_xgsize_picture_path . $picPath['attachment'];
		$ret_suffix = $ret_graph_big_picture_path . $picPath['attachment'];
		$ret_path_attachmentImg = ($this->getPath()) .'/..' . $ret_suffix;
		$ret_path_small = ($this->getPath()). '/..'. $ret_graph_big_picture_path . $path['dirname'].'/';
		$ret_path_big = ($this->getPath()). '/..'. $ret_graph_big_picture_path . $path['dirname'].'/';
		$size = 160;
		$sizeBig = 480;
		if(file_exists($origan_path)){
			if(file_exists($ret_path_attachmentImg)){
				$pic_path = '/mobcent'. $ret_suffix_xgsize;
			}else{
				$pic = new Thumbnail($origan_path);
				if($pic->zoomcutPic($origan_path,$ret_path_small ,$fileName ,$size) && $pic->zoomcutPic($origan_path,$ret_path_big,$fileName ,$sizeBig)){
					$pic_path = '/mobcent'.$ret_graph_xgsize_picture_path . $path['dirname'].'/'.$path['filename'].'.'.$path['extension'];
				}
			}
		}else if(file_exists($origan_path_date)){
			$path=pathinfo( date('Ym/d',$picPath['dateline']) .'/'.$origan[count($origan)-1]);
			$fileName = $path['filename'].'.'.$path['extension'];
			$Url = explode('/',$this->getRootFolder(SELF_FOLDER));
			unset($Url[count($Url)-1]);
			$Url = implode('/',$Url);
			$ret_suffix_xgsize = $ret_graph_xgsize_picture_path . '/'.date('Ym/d',$picPath['dateline']) .'/'.$origan[count($origan)-1];
			$ret_suffix = $ret_graph_big_picture_path . '/'.date('Ym/d',$picPath['dateline']) .'/'.$origan[count($origan)-1];
			$ret_path_attachmentImg = ($this->getPath()).'/..' . $ret_suffix;
			$ret_path_small = ($this->getPath()) . '/..'. $ret_graph_small_picture_path . $path['dirname'].'/';
			$ret_path_big = ($this->getPath()). '/..'. $ret_graph_big_picture_path . $path['dirname'].'/';
			$size = 160;
			if(file_exists($ret_path_attachmentImg)){
				$pic_path = '/mobcent' . $ret_suffix_xgsize;
			}else{
				$pic = new Thumbnail($origan_path_date);
				if($pic->zoomcutPic($origan_path_date,$ret_path_small ,$fileName ,$size) && $pic->zoomcutPic($origan_path_date,$ret_path_big,$fileName ,$sizeBig)){
					$pic_path = '/mobcent' . $ret_graph_xgsize_picture_path . $path['dirname'].'/'.$path['filename'].'.'.$path['extension'];
				}
			}
		}
		return $pic_path;
	}
	
	
	function parseTargeImage_old($picPath){
		$getrxpath=dirname(__FILE__).'/../..';
		$ret_graph_small_picture_path = '/data/attachment/forum/mobcentSmallPreview/';
		$ret_graph_big_picture_path = '/data/attachment/forum/mobcentBigPreview/';
		$ret_graph_xgsize_picture_path = '/data/attachment/forum/xgsize/';
		$origan_path = $getrxpath. "/../../data/attachment/forum/" . $picPath['attachment'];
		$origan = explode('/',$picPath ['attachment']);
		$origan_path_date = $getrxpath. "/../../data/attachment/forum/".date('Ym/d',$picPath['dateline']) .'/'.$origan[count($origan)-1];
		$path=pathinfo( $picPath['attachment']);
		$fileName = $path['filename'].'.'.$path['extension'];
		$Url = explode('/',$this->getRootFolder(SELF_FOLDER));
		unset($Url[count($Url)-1]);
		$Url = implode('/',$Url);
		$ret_suffix_xgsize = $ret_graph_xgsize_picture_path . $picPath['attachment'];
		$ret_suffix = $ret_graph_small_picture_path . $picPath['attachment'];
		$ret_path_attachmentImg = $getrxpath .'/..' . $ret_suffix;
		$ret_path_small = $getrxpath. '/..'. $ret_graph_small_picture_path . $path['dirname'].'/';
		$ret_path_big = $getrxpath. '/..'. $ret_graph_big_picture_path . $path['dirname'].'/';
		$size = 160;
		$sizeBig = 480;
		if(file_exists($origan_path)){
			if(file_exists($ret_path_attachmentImg)){
				$pic_path = '/mobcent'. $ret_suffix_xgsize;  
			}else{
				$pic = new Thumbnail($origan_path);
				if($pic->zoomcutPic($origan_path,$ret_path_small ,$fileName ,$size) && $pic->zoomcutPic($origan_path,$ret_path_big,$fileName ,$sizeBig)){
					$pic_path = '/mobcent'.$ret_graph_xgsize_picture_path . $path['dirname'].'/'.$path['filename'].'.'.$path['extension'];
				}
			}
		}else if(file_exists($origan_path_date)){
			$path=pathinfo( date('Ym/d',$picPath['dateline']) .'/'.$origan[count($origan)-1]);
			$fileName = $path['filename'].'.'.$path['extension'];
			$Url = explode('/',$this->getRootFolder(SELF_FOLDER));
			unset($Url[count($Url)-1]);
			$Url = implode('/',$Url);
			$ret_suffix_xgsize = $ret_graph_xgsize_picture_path . '/'.date('Ym/d',$picPath['dateline']) .'/'.$origan[count($origan)-1];
			$ret_suffix = $ret_graph_small_picture_path . '/'.date('Ym/d',$picPath['dateline']) .'/'.$origan[count($origan)-1];
			$ret_path_attachmentImg = ($this->getPath()).'/..' . $ret_suffix;
			$ret_path_small = ($this->getPath()). '/..'. $ret_graph_small_picture_path . $path['dirname'].'/';
			$ret_path_big = ($this->getPath()). '/..'. $ret_graph_big_picture_path . $path['dirname'].'/';
			$size = 160;
			if(file_exists($ret_path_attachmentImg)){
				$pic_path = '/mobcent' . $ret_suffix_xgsize;  
			}else{
				$pic = new Thumbnail($origan_path_date);
				if($pic->zoomcutPic($origan_path_date,$ret_path_small ,$fileName ,$size) && $pic->zoomcutPic($origan_path_date,$ret_path_big,$fileName ,$sizeBig)){
					$pic_path = '/mobcent' . $ret_graph_xgsize_picture_path . $path['dirname'].'/'.$path['filename'].'.'.$path['extension'];
				}
			}
		}
		return $pic_path;
	}
	
	
	public function getRootFolder($path){
		$path = str_replace("\\", "/", $path);
		if(strpos($path, 'model/table/x25') > 0){
			$path = substr($path, 0 , strlen($path) -4 );
		}
		return $path;
	}
	public function is_gif($path)
	{
		$arr_path = explode('/',$path);
		
		if(in_array('mobcent', $arr_path) && substr($path, -1,4) == '.gif')
		{
			$info = str_replace('/mobcent/data/attachment/forum/mobcentSmallPreview/', '/data/attachment/forum', $path);
		}else {
			$info = str_replace('/mobcent/data/attachment/forum/mobcentSmallPreview/', '/mobcent/data/attachment/forum/mobcentBigPreview/', $path);
		}
		echo  $info;
	}
	
	

	public function struct_to_array($item){
		if(!is_string($item)){
			$item =(array)$item;
			foreach($item as $key=>$val){
				$item[$key]  = $this->struct_to_array($val);
			}
			return $item;
		}else{
			$arr[]=$item;
			return $arr;
		}
		
	}
	
	public function xml_to_array($xml)
	{
		$array =(array)(simplexml_load_string($xml));
		foreach ($array as $key=>$item){
			$array[$key]  = $this->struct_to_array((array)$item);
		}
		return $array;
	}
	public function SetAttachment($aid_Img,$aid,$tid,$pid,$_G,$threadimage)
	{
		if(empty($aid_Img))
		{
			$threadimageaid = $aid;
		
			if ($aid) {
				$tableid = getattachtableid ( $tid );
				$query = get_forum_attachment_unused($aid);
				while ( $attach = DB::fetch ( $query ) ) {
					$aids = $attach ['aid'];
					$data = $attach;
				}
			
				$uid = $_G['uid'];
				update_forum_attachment($tid, $tableid,$uid, $pid, $aids);
				$data ['uid'] = 1;
				$data ['tid'] = $tid;
				$data ['pid'] = $pid;
				C::t ( 'forum_attachment_n' )->insert ( $tableid, $data );
			}
		
			$values = array (
					'fid' => $_G ['fid'],
					'tid' => $tid,
					'pid' => $pid,
					'coverimg' => ''
			);
			$param = array ();
			if ($_G ['forum'] ['picstyle']) {
				if (! setthreadcover ( $pid, 0, $threadimageaid )) {
					preg_match_all ( "/(\[img\]|\[img=\d{1,4}[x|\,]\d{1,4}\])\s*([^\[\<\r\n]+?)\s*\[\/img\]/is", $message, $imglist, PREG_SET_ORDER );
					$values ['coverimg'] = "<p id=\"showsetcover\">" . lang ( 'message', 'post_newthread_set_cover' ) . "<span id=\"setcoverwait\"></span></p><script>if($('forward_a')){\$('forward_a').style.display='none';setTimeout(\"$('forward_a').style.display=''\", 5000);};ajaxget('forum.php?mod=ajax&action=setthreadcover&tid=$tid&pid=$pid&fid=$_G[fid]&imgurl={$imglist[0][2]}&newthread=1', 'showsetcover', 'setcoverwait')</script>";
					$param ['clean_msgforward'] = 1;
					$param ['timeout'] = $param ['refreshtime'] = 15;
				}
			}
		
			if ($threadimageaid) {
				if (! $threadimage) {
					$threadimage = C::t ( 'forum_attachment_n' )->fetch ( 'tid:' . $tid, $threadimageaid );
				}
				$threadimage = daddslashes ( $threadimage );
				C::t ( 'forum_threadimage' )->insert ( array (
				'tid' => $tid,
				'attachment' => $threadimage ['attachment'],
				'remote' => $threadimage ['remote']
				) );
			}
		
		}
		else
		{
			$isInsertForumImage = false;
			foreach($aid_Img as $key=>$val)
			{
				$threadimageaid = $val;
		
				if ($val) {
					$tableid = getattachtableid ( $tid );
					$query = DB::query ( "SELECT * FROM %t WHERE aid=%d", array (
							'forum_attachment_unused',
							$val
					) );
					while ( $attach = DB::fetch ( $query ) ) {
						$aids = $attach ['aid'];
						$data = $attach;
					}
					DB::query ( "UPDATE %t SET tid=%d,tableid=%d,uid=%d,pid=%d WHERE aid IN (%n)", array (
					'forum_attachment',
					$tid,
					getattachtableid ( $tid ),
					$_G ['uid'],
					$pid,
					$aids
					) );
					$data ['uid'] = 1;
					$data ['tid'] = $tid;
					$data ['pid'] = $pid;
					C::t ( 'forum_attachment_n' )->insert ( $tableid, $data );
				}
		
				$values = array (
						'fid' => $_G ['fid'],
						'tid' => $tid,
						'pid' => $pid,
						'coverimg' => ''
				);
				$param = array ();
				if ($_G ['forum'] ['picstyle']) {
					if (! setthreadcover ( $pid, 0, $threadimageaid )) {
						preg_match_all ( "/(\[img\]|\[img=\d{1,4}[x|\,]\d{1,4}\])\s*([^\[\<\r\n]+?)\s*\[\/img\]/is", $message, $imglist, PREG_SET_ORDER );
						$values ['coverimg'] = "<p id=\"showsetcover\">" . lang ( 'message', 'post_newthread_set_cover' ) . "<span id=\"setcoverwait\"></span></p><script>if($('forward_a')){\$('forward_a').style.display='none';setTimeout(\"$('forward_a').style.display=''\", 5000);};ajaxget('forum.php?mod=ajax&action=setthreadcover&tid=$tid&pid=$pid&fid=$_G[fid]&imgurl={$imglist[0][2]}&newthread=1', 'showsetcover', 'setcoverwait')</script>";
						$param ['clean_msgforward'] = 1;
						$param ['timeout'] = $param ['refreshtime'] = 15;
					}
				}
		
				if (!$isInsertForumImage && $threadimageaid) {
					if (! $threadimage) {
						$threadimage = C::t ( 'forum_attachment_n' )->fetch ( 'tid:' . $tid, $threadimageaid );
					}
					$threadimage = daddslashes ( $threadimage );
					C::t ( 'forum_threadimage' )->insert ( array (
					'tid' => $tid,
					'attachment' => $threadimage ['attachment'],
					'remote' => $threadimage ['remote']
					) );
					$isInsertForumImage = true;
				}
					
			}
		
		
		}
		
	}
	public function common_reply_oneself_fenlei($tid)
	{
		$sortid = C::t ( 'forum_typeoptionvar' )->fetch_all_by_tid_optionid ($tid,null);
		global $_G;
		if(!empty($sortid)){
			$threadsortshow = threadsortshow($sortid[0]['sortid'],$tid);
			$fenlei="";
			foreach($threadsortshow[optionlist] as $k1=>$v1){
				if($v1['type']=="image"){
					$fenlei.=$v1['title'].":\r\n";
				}else{
					$fenlei.=$v1['title'].':'.$v1['value']."\r\n";
				}
			}
			$fenlei=str_replace("&raquo;", "", $fenlei);
			$fenlei=str_replace("&nbsp;", " ", $fenlei);
			$fenlei = preg_replace("/\<(?!br).(.*?)\>/is",'',$fenlei);
				
			foreach($threadsortshow[optionlist] as $opt){
				$opt_arr[]=$opt;
			}
			$fenlei="";
			foreach($opt_arr as $v1){
				$fenlei.=$v1['title'].':'.$v1['value']."\r\n";
			}
			$fenlei=str_replace("&raquo;", "", $fenlei);
			$fenlei=str_replace("&nbsp;", " ", $fenlei);
			$fenlei=str_replace('onload="thumbImg(this)"', '', $fenlei);
			$fenlei=str_replace('data/attachment/forum', '/mobcent/data/attachment/forum/mobcentSmallPreview', $fenlei);
			$fenlei=trim(str_replace('border="0"', 'width="2" height="4" /', $fenlei));
				
			$fenlei1 = doContent ($fenlei);
			$fenlei2 = getContentFont ($fenlei);
				
			foreach($fenlei1 as $k=>$v){
				if($v['type']==0){
					unset($fenlei1[$k]);
				}else{
						
				}
			}
				
			$fenlei_array2 = explode('|~|', $fenlei2);
			foreach($fenlei_array2 as $k=>$v){
				if(!empty($v)){
					$fenlei_arr[]=array("infor" =>str_replace('<hr class="l" />','',preg_replace("#(\w*)\<.*?\>(\w*)#","$1$2", $v)),"type"	=>0,);
				}
					
				if(preg_replace("#(\w*)\<.*?\>(\w*)#","$1$2",$fenlei1[$k]["infor"])){
					$fenlei_arr[]=$fenlei1[$k];
				}
			}
				return $fenlei_arr;
		}
	}
	function getdirname($path=null){
		if (!empty($path)) {
			if (strpos($path,'\\')!==false) {
				return substr($path,0,strrpos($path,'\\')).'/';
			} elseif (strpos($path,'/')!==false) {
				return substr($path,0,strrpos($path,'/')).'/';
			}
		}
		return './';
	}
	public function getPath()
	{
		/*$file_path = $this->getdirname ( __FILE__ );
		$pos = stripos($file_path,'mobcent');
		$str1 = substr($file_path,0,$pos);
		$pos1 = stripos($_SERVER ['PHP_SELF'],'mobcent');
		$str2 = substr($_SERVER ['PHP_SELF'],$pos1);
		$str = dirname($str1.$str2);
		$str = str_replace('\\','/',$str);*/
		return dirname(__FILE__).'/../..';
	}
	
	public function replaceHtmlAndJs($document)
	{
		$document = trim($document);
		if (strlen($document) <= 0) {
			return $document;
		}
		$search = array ("'<script[^>]*?>.*?</script>'si",   
				"'<[\/\!]*?[^<>]*?>'si",         
				 
				"'&(quot|#34);'i",              
				"'&(amp|#38);'i",
				"'&(lt|#60);'i",
				"'&(gt|#62);'i",
				"'&(nbsp|#160);'i"
		);                    
		$replace = array ("",
				"",
			 
				"\"",
				"&",
				"<",
				">",
				" "
		);
		return @preg_replace ($search, $replace, $document);
	}
	
	public function board_topic_list($stamp,$fid,$start_limit,$limit,$topicInstance,$info,$page){
		if ($stamp && empty ( $fid )) {
			$digest =" AND (t.icon = 10 or t.stamp = 1)";
			$parameter = array (
					'forum_thread',
					$stamp ='stamp ='.$stamp.$digest
			);
	
			$threadlist = DB::fetch_all ( "SELECT * FROM %t WHERE ".$stamp . DB::limit ( $start_limit, $limit ), $parameter, 'tid' );
		} elseif ($stamp && ! empty ( $fid )) {
			$parameter = array (
					'forum_thread',
			);
			$tids =$info ->forum_display($fid,$topicInstance);
			$threadlist = DB::fetch_all ( "SELECT * FROM %t t WHERE  t.displayorder >'-1'  AND (t.icon =10 or stamp =1) AND t.fid in(".$tids.") AND t.fid = ".$fid." ORDER BY t.tid desc" . DB::limit ( $start_limit, $limit ), $parameter, 'tid' );
	
		} else {
			$threadlist = C::t ( 'forum_thread' )->fetch_all_search ( $filterarr1, $tableid, $start_limit, $limit, $order, $sort, $forceindex = '' );
		}
		if($page ==1 && empty($_GET ['sortby']))
		{
			$threadlist =array_merge($threadlist_top,$threadlist);
		}
	}
	public function getQuoteImg($_G,$postarr){
		foreach ( $postarr as $uid => $post ) {
			$postusers[$post['authorid']] = array();
			$quotemessage = '';
			if (strstr ( $post ['message'], '[quote]' ) != '') {
				$res = preg_match ( '\[color=#\d+\](.*)\[/color\]', $post ['message'], $quote );
				$postarr [$uid] ['quote_pid'] = $quote ['1'] [0];
				$postarr [$uid] ['is_quote'] = ( bool ) true;
				$postarr [$uid] ['message'] = preg_replace ( '#\[quote\][.\n\S\s]+\[/quote\]#', '', $post ['message'] );
				$quotemessage = $this->parseQuoteMessage($post ['message']);
			} else {
				$postarr [$uid] ['is_quote'] = ( bool ) false;
			}
			global $_G;
				
				
			$post = array_merge ( $postarr [$uid], ( array ) $postusers [$post ['authorid']] );
			foreach($_G['cache']['smilies']['searcharray'] as $key=>$val)
			{
				$post ['message'] = preg_replace($val, "[$key]", $post ['message']);
			}
			$post ['message'] = discuzcode ( $post ['message'], $post ['smileyoff'], $post ['bbcodeoff'] );
			$post ['message'] = str_replace('<img src="static/', '<img src="/static/', $post ['message']);
			$post ['authortitle'] = $_G ['cache'] ['usergroups'] [$post ['groupid']] ['grouptitle'];
			$post ['quotemessage'] = $quotemessage;
			if($post ['attachment'] == 2)
				$pids [] = $post ['pid'];
				
				
			$postlist [$post ['pid']] = $post;
		}
	
		return $postlist;
	}
	
	public function parseTopic($_G,$postlist,$forumclass,$uid,$temp){
		foreach ( $postlist as $pid => $post ) { //print_r($postlist);exit;
			if(!empty($post['tid'])){
				$topicRemote = DB::fetch(DB::query("SELECT remote FROM ".DB::table('forum_threadimage')." where tid=".$post['tid']));
				//print_r($topicRemote);exit;
			}
			$uids[]=$post ['authorid'];
			preg_match_all ( "/\[attach\](\d+)\[\/attach\]/i", $postlist [$pid] ['message'], $matches );
			$aids = $matches['1'];
			$matches = array_filter($matches);
			foreach(C::t('forum_attachment_n')->fetch_all_by_id('tid:'.$post['tid'], 'pid', $post['pid']) as $attach) {
				$Allaids[] = $attach['aid'];
			}
			
			if(count($aids) !== count($Allaids))
			{
				$tableid = getattachtableid ( $post['tid'] );
				$attachmentAid = C::t ( 'forum_attachment' )->fetch_all_by_id('pid', $post['pid']);
				foreach($attachmentAid as $key=>$val)
				{
					if(in_array($key, $aids))
					{
						continue;
					}
					$path =  DB::fetch_first("SELECT * FROM %t WHERE %i AND isimage IN ('1', '-1') ", array('forum_attachment_'.$tableid, DB::field('aid', $val['aid'])));
						
					if(!empty($path))
					{
						$filename = $this->parseTargeImage($path);
						$imgs [] = '<img src="' .$filename. '" />';
						$attachmentId = $key;
						$attachmentAId[]= $path['aid'];
						$postlist [$pid] ['message'] = $postlist [$pid] ['message'].'[attach]'.$key.'[/attach]';
						$pattern [] = "#\[attach\]" . $key . "\[\/attach\]#i";
					}
						
				}
	
			} 
			if(!empty($matches) && is_array($matches))
			{ 
				foreach ( $matches [1] as $k => $v ) {
					$dataImg['attachment'] = $post ['attachments'] [$v] ['attachment'];
					$dataImg['dateline'] = $post ['dateline'];
					$dataImg['remote'] = $post ['attachments'] [$v] ['remote']; 
					$filename = $this->parseTargeImage($dataImg);
					$imgs [] = '<img src="' .$filename. '" />';
					$attachmentId = $v;
				}
			}
			
			foreach ( $matches [1] as $k => $v ) {
				$pattern [] = "#\[attach\]" . $v . "\[\/attach\]#i";
			}
			$message_string = preg_replace ( $pattern, $imgs, $postlist [$pid] ['message'] );
				
			if ($message_string) {
				$postlist [$pid] ['message'] = $message_string;
			}
	
			$tempPostlist = $forumclass->viewthread_procpost($postlist [$pid], $_G['member']['lastvisit'], 2, 2);
			/*$ti1=Common::get_web_unicode_charset('\u6e38\u5ba2');
				$tempPostlist[message]=str_replace($ti1,'',$tempPostlist[message]);
			echo ($tempPostlist[message]);exit;*/
				
			$topicContent = text_replace($tempPostlist ['message']);
			$topicContent = discuzcode ( $topicContent, $post ['smileyoff'], $post ['bbcodeoff'] );
			$topicContent = str_replace('<img src="static/', '<img src="/static/', $topicContent);
			$postlist [$pid] ['message'] = text_replace($topicContent);
				
			$postlist [$pid] ['message'] = preg_replace( "/\<font class=\"jammer\">.+\<\/font>/i",'' ,$postlist [$pid] ['message']);
			$postlist [$pid] ['message'] = preg_replace( "/\<span style=\"display:none\">.+\<\/span>/i",'' ,$postlist [$pid] ['message']);
			$img_url = $post ['attachments'] [$attachmentId] ['url'];
	
			$font [$pid] ['quote_content'] = getContent ( $post ['quotemessage'] );
			$font [$pid] ['quote_content'] = preg_replace( "/\[attach\]\d+\[\/attach\]/i",'' ,$font [$pid] ['quote_content']);
			$tempPostlist_quote_content = $forumclass->viewthread_procpost($post, $_G['member']['lastvisit'], 2, 2);
			$topicContent_quote_content = text_replace($tempPostlist_quote_content ['quotemessage']);
	
			$topicContent_quote_content = discuzcode ( $topicContent_quote_content, $post ['smileyoff'], $post ['bbcodeoff'] );
			$topicContent_quote_content = str_replace('<img src="static/', '<img src="/static/', $topicContent_quote_content);
			$font [$pid] ['quote_content'] = text_replace($topicContent_quote_content);
	
		}  
	
		$uids = array_unique($uids);
		empty($uid)?0:$uid;
		$uidsql = ' AND '.DB::field('uid', $uid);
		$fav = DB::fetch_first("SELECT * FROM %t WHERE id=%d AND idtype=%s $uidsql", array('home_favorite', $_G ['tid'], 'tid'));
		if ($fav) {
			$is_favor = 1;
		} else {
			$is_favor = 0;
		}
		$profile = new memberProfile ();
		$profile_list = $profile->get_profile_by_uid ( $uids, 'gender' );
	
		foreach ( $profile_list as $k => $v ) {
			$data_profile [$v['uid']] ['gender'] = $v ['gender'];
			$data_profile [$v['uid']] ['level'] =(int) $v['stars'];
		}
		$member = commonMember::getUserStatus($uids);
		$special = get_special_by_tid($_G ['tid']);
		if($special == 2)
		{
			$i =0;
			foreach ( $postlist as $key => $val ) {
				if($i ==1)
				{
					$key1 =$key;
				}
				$i ++;
			}
		}
		unset($postlist[$key1]);
		$_user=Anonymous_User($_G ['tid']);
		$content =$this->parseTradeTopic($_G, $post); 
		foreach ( $postlist as $key => $val ) {
			$post = $tags = array ();
			$thread = C::t ( 'forum_thread' )->fetch ( $val ['tid'] );
			$post['gender'] = $data_profile [$val['authorid']] ['gender'];
			$post['level'] = $data_profile [$val['authorid']] ['level'];
			$tags = explode ( ',', $val ['tags'] );
			global $_G;
			preg_match_all( "/\[(\d+)\]+/i",$val ['message'],$smailyArr);
			$thisUrl=dirname('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']).'/../../';
			foreach($smailyArr[1] as $key =>$Sval)
			{
				/*rx 20131016 tie zi xiang qing biao qing chu li*/
				$smArr = DB::fetch(DB::query("SELECT * FROM ".DB::table('common_smiley')." where id=".$Sval));
				$smTypeArr = DB::fetch(DB::query("SELECT directory FROM ".DB::table('forum_imagetype')." where typeid=".$smArr['typeid']));
				$smUrl = $thisUrl.'static/image/'.$smArr['type'].'/'.$smTypeArr['directory'].'/'.$smArr['url'];
				$smile .= "[mobcent_phiz=".$smUrl."]";
				/*end rx 20131016*/
				$_G['cache']['smilies']['searcharray'][$Sval] = str_replace('/', "",$_G['cache']['smilies']['searcharray'][$Sval]);
				$val ['message'] = str_replace($smailyArr[0][$key], $smile,$val ['message']);
				unset($smile);
			} 
			if ($tags [0] > 0 || $val ['position'] == 1) {
	
				$message1 = doContent ( $val ['message'],$val[imagelist]);
				$message2 = getContentFont ( $val ['message'] );
	
				foreach($message1 as $k=>$v){
					if($v['type']==0){
						unset($message1[$k]);
					}else{
	
					}
				}
	
				$message_array2 = explode('|~|', $message2);
	
				$message2 = str_replace('[', '1', $message2);
				$message2 = str_replace(']', '1', $message2);
				if(is_array($message_array2) && count($message_array2)>0){
	
					foreach($message_array2 as $k=>$v){
						$message[]=array("infor" =>$v,"originalInfo" =>'',"type"=>0);
						if($message1[$k]["infor"] && !empty($message1)){
							$message[]=$message1[$k];
						}
					}
				}else{
					$message =getContentFont($val ['message']);
				}
	
				$post ['hits'] = ( int ) $thread ['views'];
				$post ['replies'] = ( int ) $thread ['replies'];
				$post ['essence'] = ( int ) $thread ['digest'] >0 || ( int ) $thread ['icon'] ==9 || (int ) $thread ['stamp'] ==0? 1 : 0;
				$post ['content'] = $message;
				$post ['create_date'] = $val ['dateline'] . "000";
				$post ['icon']		=userIconImg($val ['authorid']);
				$post ['is_favor'] = ( int ) $is_favor;
	
				if($val ['invisible'] == -5 || $val ['invisible'] == -1)
				{
					$post ['status'] = ( int ) 0;
					$arr['rs'] =0;
					$arr['errcode'] ='01040007';
					return $arr;exit();
				}
				else if($thread['closed'] == 1)
				{
					$post ['status'] =2;
				}
				else
				{
					$post ['status'] =1;
				}
				$post ['title'] = $val ['subject'];
				$post ['topic_id'] = ( int ) $val ['tid'];
				$post ['user_id'] = ( int ) $val ['authorid'];
				if(empty($val ['author']) && isset($val['authorid']) && !empty($val['authorid']))
				{
					$post ['reply_status'] = (int)'-1 ';
					$post ['user_nick_name'] = Common::get_unicode_charset('\u8be5\u7528\u6237\u5df2\u88ab\u5220\u9664');
				}
				else if(empty($val['author']) && empty($val['authorid']))
				{
					$post ['reply_status'] = (int)'0 ';
					$post ['user_nick_name'] = Common::get_unicode_charset('\u533f\u540d\u7528\u6237');
				}
				else
				{
					$post ['reply_status'] = (int)'1 ';
					$post ['user_nick_name'] = $val['author'];
				}
				$post ['reply_posts_id'] = ( int ) $val ['pid'];
				$info = surround_user ::fetch_all_by_pid($val ['tid']);
				if(empty($info))
				{
					$post ['location'] ='';
	
				}
				else
				{
					$post ['location'] = $info ['location'];
				}
	
				$data_post ["topic"] = $post;
			} else {
				$message1 = doContent ( $val ['message'],$val[imagelist]);
				$message2 = getContentFont ( $val ['message'] );
				
				foreach($message1 as $k=>$v){
					if($v['type']==0){
						unset($message1[$k]);
					}else{
	
					}
				}
	
				$message_array2 = explode('|~|', $message2);
		
				
				
				$message2 = str_replace('[', '1', $message2);
				$message2 = str_replace(']', '1', $message2);
				if(is_array($message_array2) && count($message_array2)>0){
	
					foreach($message_array2 as $k=>$v){
						$message[]=array("infor" =>$v,"originalInfo" =>'',"type"=>0);
						if($message1[$k]["infor"] && !empty($message1)){
							$message[]=$message1[$k];
						}
					}
				}else{
					$message =getContentFont($val ['message']);
				}
	
				$post ['location'] = "";  
				$post ['icon']		=userIconImg($val['authorid']);
				$post ['posts_date'] = $val ['dateline'] . "000";
				$post ['reply_content'] = $message;
				$post ['reply_id'] = ( int ) $val ['authorid'];
				if(empty($val ['author']) && isset($val['authorid']) && !empty($val['authorid']))
				{
					$post ['reply_status'] = (int)'-1 ';
					$post ['reply_name'] = Common::get_unicode_charset('\u8be5\u7528\u6237\u5df2\u88ab\u5220\u9664');
				}
				else if(empty($val ['author']) && empty($val ['authorid']))
				{
					$post ['reply_status'] = (int)'0 ';
					$post ['reply_name'] = Common::get_unicode_charset('\u533f\u540d\u7528\u6237');
				}
				else
				{
					$post ['reply_status'] = (int)'1 ';
					$post ['reply_name'] = $val ['author'];
				}
				$post ['reply_posts_id'] = ( int ) $val ['pid'];
				if($val ['invisible'] == -5)
				{
					$post ['status'] = ( int ) 0;
				}
				else if($thread['closed'] == 1)
				{
					$post ['status'] =2;
				}
				else
				{
					$post ['status'] =1;
				}
				$post ['title'] = preg_replace("#(\w*)\<.*?\>(\w*)#","$1$2",$val ['subject']);
				$post ['role_num'] = $val ['groupid'];
				$post ['is_quote'] = ( bool ) $val ['is_quote'];
				$post ['quote_pid'] = $val ['quote_pid'];
				if((bool) $val ['is_quote'] != false){
					$post ["quote_content"] =preg_replace( "/\[attach\]\d+\[\/attach\]/i",'',preg_replace("#(\w*)\<.*?\>(\w*)#","$1$2", $font [$post ['reply_posts_id']]['quote_content']));
				}else
					$post ["quote_content"] = '';
	
				$post ["quote_user_name"] = null;
				$post ['position'] = $val ['position'];
				$info = surround_user ::fetch_all_by_pid($val['tid']);
				if(empty($info))
				{
					$post ['location'] ='';
	
				}
				else
				{
					$post ['location'] = $info ['location'];
				}
				$data_post ['list'] [] = $post;
				unset ( $quote );
				unset ( $pid );
				$temp++;
			}
			unset ( $post );
			unset($message);
				
		}
		return $data_post;
	}
	
	private function parseQuoteMessage($message){
		$position = strpos($message, '[color');
		if($position > 0){
			$position2 = strpos($message, ']' , $position + 1);
			$position3 = strpos($message, '[/color]' , $position2 + 1);
			$str1 = substr($message, $position2 + 1, ($position3 - $position2 - 1) );
				
			$position4 = strpos($message, '[/size]') + 6;
			$position5 = strpos($message, '[/quote]' , $position4 + 1);
			$str2 = substr($message, $position4 + 1, ($position5 - $position4 - 1) );
			return $str1 .''. $str2;
		}else {
			return $message;
		}
	}
	
	public function parseTradeTopic($_G,$post)
	{
		$rows = C::t('forum_thread')->fetch_all_by_tid($_G['tid']);
		$tpids = array();
		if($rows[$_G['tid']]['special'] == 2) {
			$query = C::t('forum_trade')->fetch_all_thread_goods($_G['tid']);
			foreach($query as $trade) {
				$tradesaids[] = $trade['aid'];
				$tradespids[] = $trade['pid'];
			}
			$specialadd2 = 1;
			if($tradespids) {
				foreach(C::t('forum_attachment_n')->fetch_all_by_id('tid:'.$_G['tid'], 'pid', $tradespids) as $attach) {
					if($attach['isimage'] && is_array($tradesaids) && in_array($attach['aid'], $tradesaids)) {
						$trades[$attach['pid']]['attachurl'] = ($attach['remote'] ? $_G['setting']['ftp']['attachurl'] : $_G['setting']['attachurl']).'forum/'.$attach['attachment'];
						$trades[$attach['pid']]['thumb'] = $attach['thumb'] ? getimgthumbname($trades[$attach['pid']]['attachurl']) : $trades[$attach['pid']]['attachurl'];
						$trades[$attach['pid']]['width'] = $attach['thumb'] && $_G['setting']['thumbwidth'] < $attach['width'] ? $_G['setting']['thumbwidth'] : $attach['width'];
						$trades[$attach['pid']]['thumb'] = str_replace('forum/', '', $trades[$attach['pid']]['thumb']);
						$filename = $this->parseTradeTopicImg($trades[$attach['pid']]);
						$info[]= array('infor' => $filename,'type' => 1);
					}
				}
			}
			$tradepostlist = C::t('forum_post')->fetch_all('tid:'.$_G['tid'], $tradespids);
			foreach($query as $trade) {
				$quality = $trade['quality']==1 ? Common::get_unicode_charset('\u5168\u65b0\u5546\u54c1'):Common::get_unicode_charset('\u4e8c\u624b\u5546\u54c1');
				$transport =  $trade['transport'];
				switch ($trade['transport'])
				{
					case 3:
						$transport = Common::get_unicode_charset('\u865a\u62df\u5546\u54c1');
						break;
					case 2:
						$transport = '';
						break;
					case 0:
						$transport = Common::get_unicode_charset('\u7ebf\u4e0b\u4ea4\u6613');
						break;
				}
				$time = $trade['expiration'] - time();
				$time = explode('.',($time/3600/24));
				$time = intval($time[0]).Common::get_unicode_charset('\u5929').(intval(('0.'.$time[1])*24)).Common::get_unicode_charset('\u5c0f\u65f6');
				$message[]= array('infor' => Common::get_unicode_charset('\u5546\u54c1\u7c7b\u578b\u003a').$quality,'type' => 0);
				$message[]= array('infor' => Common::get_unicode_charset('\u8fd0\u8d39\u003a').$transport,'type' => 0);
				$message[]= array('infor' => Common::get_unicode_charset('\u5269\u4f59\u65f6\u95f4\u003a').$time,'type' => 0);
				$message[]= array('infor' => Common::get_unicode_charset('\u5546\u54c1\u6570\u91cf\u003a').$trade['amount'],'type' => 0);
				$message[]= array('infor' => Common::get_unicode_charset('\u5730\u70b9\u003a').$trade['locus'],'type' => 0);
				$message[]= array('infor' => Common::get_unicode_charset('\u7d2f\u8ba1\u552e\u51fa\u003a').$trade['totalitems'],'type' => 0);
			}
				
			foreach($tradepostlist as $val)
			{
	
				$topicContent = text_replace($val ['message']);
	
				$topicContent = discuzcode ( $topicContent, $val ['smileyoff'], $val ['bbcodeoff'] );
				$topicContent = str_replace('<img src="static/', '<img src="/static/', $topicContent);
				$val['message'] = text_replace($topicContent);
	
				$message1 = doContent ( $val ['message'],$tradesaids);
				$message2 = getContentFont ( $val ['message'] );
	
				foreach($message1 as $k=>$v){
					if($v['type']==0){
						unset($message1[$k]);
					}else{
	
					}
				}
	
				$message_array2 = explode('|~|', $message2);
	
				$message2 = str_replace('[', '1', $message2);
				$message2 = str_replace(']', '1', $message2);
				if(is_array($message_array2) && count($message_array2)>0){
	
					foreach($message_array2 as $k=>$v){
						$message[]=array("infor" =>$v,"type"=>0);
						if($message1[$k]["infor"] && !empty($message1)){
							$message[]=$message1[$k];
						}
					}
				}else{
					$message =getContentFont($val ['message']);
				}
			}
		}
		return $message;
	}
	
}

?>