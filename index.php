<?php
// change the following paths if necessary
$config=dirname(__FILE__).'/protected/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',false);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',0);

if (file_exists('install.php') && !file_exists($config)) 
{
	require('install.php');
	exit();
}

// Allow full debug mode for development. Otherwise disable error_reporting at the file level
error_reporting(0);
ini_set('display_errors', 0);

if (YII_DEBUG && YII_TRACE_LEVEL == 3) 
{
	error_reporting(-1);
	ini_set('display_errors', 1);
}

$ciimsConfig = require($config);
require((string)$ciimsConfig['params']['yiiPath'].'yii.php');
Yii::createWebApplication($config)->run();
