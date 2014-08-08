<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.

$currentDir = dirname(__FILE__);

require_once($currentDir . '/main_include.php');

return array(
	'basePath'=> $currentDir.DIRECTORY_SEPARATOR.'..',
	'name'=>'Mobcent App',
	'runtimePath' => MOBCENT_RUNTIME_PATH,
	'defaultController' => 'index',
	'language' => 'zh_cn',
	'charset' => $discuzParams['globals']['charset'],

	'preload'=>array(
		'log', 
		'dbDz', 
		'dbDzUc',
	),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.components.db.*',
		'application.components.web.*',
		
		'ext.mobcent.components.*',
		'ext.mobcent.components.db.*',
		'ext.mobcent.components.utils.*',
		'ext.mobcent.components.web.*',

		'ext.mobile_detect.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		/*
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'Enter Your Password Here',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
		*/
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),

		// uncomment the following to enable URLs in path-format
		'urlManager'=>array(
			// 'urlFormat'=>'path',
			// 'urlFormat'=>'get',
			'urlFormat'=> isset($_GET['sdkVersion']) && ($_GET['sdkVersion'] > '1.0.0') ? 'get' : 'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		
		'db' => $dbConfig['default'],
		'dbDz' => $dbConfig['discuz'],
		'dbDzUc' => $dbConfig['discuzUcenter'],

		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'index/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				// array(
				// 	'class'=>'CFileLogRoute',
				// 	'levels'=>'error, warning',
				// ),
				// uncomment the following to show log messages on web pages
				
				// array(
				// 	'class'=>'CWebLogRoute',
				// ),
				
				// array(
				// 	'class' => 'CProfileLogRoute',
				// )
			),
		),
		'cache' => array(
			// file cache
			'class' => 'CFileCache',
			'cachePath' => MOBCENT_CACHE_PATH,
			// memcache
			// 'class' => 'CMemCache',
			// 'servers' => array(
			// 	array(
			// 		'host' => 'server1',
			// 		'port' => 11211,
			// 	),
			// ),
		),
		'messages'=>array(
			'class' => 'CPhpMessageSource',
			'basePath' => dirname(__FILE__).'/../data/messages',
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',

		'discuz' => $discuzParams,
		'mobcent' => $mobcentConfig,
	),
);