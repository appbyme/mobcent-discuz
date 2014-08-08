<?php

// include discuz params
$discuzParams = require_once($currentDir . '/discuz.php');

// include constant define
require_once($currentDir . '/constant.php');

// 包含版本控制
require_once($currentDir . '/mobcent_version.php');

// include database config
global $dbConfig;
$dbConfig = require_once($currentDir . '/database.php');

$mobcentConfig = Mobcent::import($currentDir . '/mobcent.php', 'my_', true);

if (YII_DEBUG) {
    ini_set('display_errors', 1);
    // error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_ERROR);
    // error_reporting(E_ALL & ~E_DEPRECATED & E_STRICT);
    error_reporting(E_ALL & ~E_DEPRECATED & E_STRICT & ~E_ERROR & ~E_NOTICE);
    // error_reporting(E_ALL);
}