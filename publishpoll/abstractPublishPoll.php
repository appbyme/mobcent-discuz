<?php
abstract class abstractPublishPoll {
	abstract function getPublishPollObj();
	function transfer($array){
		echo echo_json($array);
	}
}

?>