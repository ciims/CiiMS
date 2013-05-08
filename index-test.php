<?php
// change the following paths if necessary
$config=dirname(__FILE__).'/protected/config/test.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

$config = require($config);
require((string)$config['params']['yiiPath'].'yii.php');
$config = CMap::mergeArray(require(dirname(__FILE__).'/protected/config/main.default.php'), $config);
Yii::createWebApplication($config)->run();