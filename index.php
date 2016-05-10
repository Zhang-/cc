<?php
session_start();
define('YII_TRACE_LEVEL',10);
date_default_timezone_set('PRC');
set_time_limit(0);
// change the following paths if necessary
//$yii=dirname(__FILE__).'/yi/framework/yii.php';
//$config=dirname(__FILE__).'/protected/config/main.php';

$yii=dirname(__FILE__).'/init.php';

// remove the following lines when in production mode
//上线修改为,false,开发程序时,修改为,true
defined('YII_DEBUG') or define('YII_DEBUG',true);
if(!YII_DEBUG){
	ini_set("display_errors","Off");
	ini_set("memory_limit","800M");
}
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

define('NOW_TIME',$_SERVER['REQUEST_TIME']);
define('NOW_DATE',date('Y-m-d', NOW_TIME));

require_once($yii);
Yii::createWebApplication()->run();