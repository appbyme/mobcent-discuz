<?php

// 修复 iis 没有 REQUEST_URI，QUERY_STRING
// if (!isset($_SERVER['REQUEST_URI'])) {
//     $_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'], 1);
//     if (isset($_SERVER['QUERY_STRING'])) {
//         $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
//     }
// }

// var_dump(xdebug_time_index());
// xhprof_sample_enable();
// xhprof_enable(XHPROF_FLAGS_CPU |XHPROF_FLAGS_MEMORY);

$yii=dirname(__FILE__).'/../../framework/mobcent/yii.php';
$mobcent=dirname(__FILE__).'/../components/Mobcent.php';
$discuz = dirname(__FILE__).'/../components/discuz/discuz.php';

// remove the following lines when in production mode
// defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
// defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
require_once($mobcent);
require_once($discuz);

$config=dirname(__FILE__).'/../config/main.php';
Yii::createWebApplication($config)->run();

// var_dump(xdebug_time_index());
// var_dump(xhprof_sample_disable());
// $xhprof_data = xhprof_disable();

// $XHPROF_ROOT = '/home/xjp/projects/web/xhprof';

// include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_lib.php";
// include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_runs.php";

// $xhprof_runs = new XHProfRuns_Default();
// $run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_testing");

// echo "http://localhost/xhprof/xhprof_html/index.php?run={$run_id}&source=xhprof_testing\n";
