<?php
abstract class abstractPostList {
	abstract function getPostListObj();
	function transfer($array){
		echo echo_json($array);
	}
}

?>