<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('PostListImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('PostListImpl');
$class=new ReflectionClass($className);
$obj = $class->newInstance();
$retObj = $obj->getPostListObj();
$obj->transfer($retObj);
?>