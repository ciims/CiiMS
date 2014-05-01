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
ini_set('display_errors', 'true');

// Determine if we should enable debugging and call stack if debug and trace are set in our config file.
// By default this disabled
defined('YII_DEBUG') or define('YII_DEBUG',isset($ciimsConfig['params']['debug']) ? $ciimsConfig['params']['debug'] : false);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',isset($ciimsConfig['params']['trace']) ? $ciimsConfig['params']['trace'] : 0);

$yiiPath = __DIR__.DS.'vendor'.DS.'yiisoft'.DS.'yii'.DS.'framework'.DS.(YII_DEBUG ? 'yii.php' : 'yiilite.php');

// change the following paths if necessary
$config=dirname(__FILE__).'/protected/config/install.php';
$mainConfig = dirname(__FILE__).'/protected/config/main.php';
$ciimsConfig = require($config);

if (!file_exists($mainConfig) && !file_exists($yiiPath)) 
{
    require(dirname(__FILE__).'/protected/modules/install/init.php');
    exit();
}

require_once($yiiPath);
Yii::setPathOfAlias('vendor', __DIR__.DS.'vendor');
// If YiiBootstrap throws a CException becausae of permissions, catch the error, route to back to the installer, and display it within pre-bootstrap for the user to correct.
Yii::createWebApplication($config)->run();