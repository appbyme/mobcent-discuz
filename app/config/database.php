<?php

// discuz database config
$dzDbConfig = $discuzParams['globals']['config']['db'][1];

list($dzDbHost, $dzDbPort) = explode(':', $dzDbConfig['dbhost']);
$dzDbPort = $dzDbPort === null ? 3306 : $dzDbPort;

// discuz ucenter database config
list($dzUcDbHost, $dzUcDbPort) = explode(':', UC_DBHOST);
$dzUcDbPort = $dzUcDbPort === null ? 3306 : $dzUcDbPort;

return array(
    'default' => array(
        'class' => 'CDbConnection',
        'autoConnect' => false,
        'connectionString' =>  sprintf('mysql:host=%s;port=%d;dbname=%s', 
            $dzDbHost, $dzDbPort, $dzDbConfig['dbname']
        ),
        'username' => $dzDbConfig['dbuser'],
        'password' => $dzDbConfig['dbpw'],
        'charset' => $dzDbConfig['dbcharset'],
        'tablePrefix' => $dzDbConfig['tablepre'],
        'emulatePrepare' => true,
        'enableProfiling' => YII_DEBUG,
    ),

    // discuz
    'discuz' => array(
        'class' => 'CDbConnection',
        'autoConnect' => false,
        'connectionString' =>  sprintf('mysql:host=%s;port=%d;dbname=%s', 
            $dzDbHost, $dzDbPort, $dzDbConfig['dbname']
        ),
        'username' => $dzDbConfig['dbuser'],
        'password' => $dzDbConfig['dbpw'],
        'charset' => $dzDbConfig['dbcharset'],
        'tablePrefix' => $dzDbConfig['tablepre'],
        'emulatePrepare' => true,
        'enableProfiling' => YII_DEBUG, 
    ),

    // discuz ucenter
    'discuzUcenter' => array(
    	'class' => 'CDbConnection',
        'autoConnect' => false,
    	'connectionString' =>  sprintf('mysql:host=%s;port=%d;dbname=%s',
            $dzUcDbHost, $dzUcDbPort, UC_DBNAME
        ),
        'username' => UC_DBUSER,
        'password' => UC_DBPW,
        'charset' => UC_DBCHARSET,
        'tablePrefix' => ltrim(ltrim(UC_DBTABLEPRE, sprintf('`%s`', UC_DBNAME)), '.'),
        'emulatePrepare' => true,
        'enableProfiling' => YII_DEBUG,
    ),

    'mobcentDiscuz' => array(
        'server' => $dzDbHost,
        'port' => $dzDbPort,
        'username' => $dzDbConfig['dbuser'],
        'password' => $dzDbConfig['dbpw'],
        'dbName' => $dzDbConfig['dbname'],
        'charset' => $dzDbConfig['dbcharset'],
        'tablePrefix' => $dzDbConfig['tablepre'],
    ),

    'mobcentDiscuzUCenter' => array(
        'server' => $dzUcDbHost,
        'port' => $dzUcDbPort,
        'username' => UC_DBUSER,
        'password' => UC_DBPW,
        'dbName' => UC_DBNAME,
        'charset' => UC_DBCHARSET,
        'tablePrefix' => ltrim(ltrim(UC_DBTABLEPRE, sprintf('`%s`', UC_DBNAME)), '.'),
    ),
    
);