<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('userVoteImpl').'.php'; 
$className = dynamicobject :: getShortDymanicObject('userVoteImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getUserVoteObj();
$obj->transfer($retObj);
?>