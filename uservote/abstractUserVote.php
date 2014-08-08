<?php
abstract class abstractUserVote {
	abstract function getUserVoteObj();
	function transfer($array){
		echo echo_json($array);
	}
}

?>