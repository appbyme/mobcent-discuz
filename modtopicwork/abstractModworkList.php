<?php
abstract class abstractModworkList {
	abstract function getModworkListObj();
	function transfer($array){
		echo echo_json($array);
	}
}

?>