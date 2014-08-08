<?php
require_once '../Config/dynamicobject.php';
require_once './'.dynamicobject :: getShortDymanicObject('modworkListImpl').'.php';
$className = dynamicobject :: getShortDymanicObject('modworkListImpl');
$class=new ReflectionClass($className);
$classObj = $class->newInstance();
$subBoardInfo = $classObj->getModworkListObj();
$classObj->transfer($subBoardInfo);
?>

