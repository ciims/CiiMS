<?php
/**
 * This is the base installer for CiiMS
 * By default, this bootstraps protected/modules/install/installer.php for the initial installation 
 * and configuration for Yii. After that, it passes controller to Yii::createWebApplication so that
 * We can run a better install (migrations, config writing, etc...)
 * 
 * If you want to make any changes to the PRE YiiPath install, edit /protected/modules/install/installer.php
 * Otherwise, this is a basic Yii App running only the install module 
 */
error_reporting(-1);

// change the following paths if necessary
$config=dirname(__FILE__).'/protected/config/install.php';
$mainConfig = dirname(__FILE__).'/protected/config/main.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',2);

$ciimsConfig = require_once($config);
if (!isset($_SESSION['config']))
    $_SESSION['config'] = array();

$ciimsConfig = array_merge($ciimsConfig, $_SESSION['config']);

if (!file_exists($mainConfig) && $ciimsConfig['params']['stage'] <= 5) 
{
    require_once(dirname(__FILE__).'/protected/modules/install/installer.php');
    exit();
}

$ciimsConfig = require_once($mainConfig);
require_once((string)$ciimsConfig['params']['yiiPath'].'yii.php');
Yii::createWebApplication($config)->run();