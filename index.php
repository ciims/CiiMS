<?php
// change the following paths if necessary
$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

if (file_exists('install.php') && !file_exists($config)) 
{
	require_once('install.php');
	exit();
}

$ciimsConfig = require_once($config);
require_once((string)$ciimsConfig['params']['yiiPath'].'yiilite.php');
Yii::createWebApplication($config)->run();