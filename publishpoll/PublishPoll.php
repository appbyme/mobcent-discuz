<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('PublishPollImpl').'.php'; 
$className = dynamicobject :: getShortDymanicObject('PublishPollImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getPublishPollObj();
$obj->transfer($retObj);
?>