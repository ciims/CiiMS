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
$ciimsConfig = require_once($config);

// Session Management check for CMS Config
session_start();
if (!isset($_SESSION['config']))
    $_SESSION['config'] = array();

// Session
$ciimsConfig = array_merge($ciimsConfig, $_SESSION['config']);
session_write_close();

if (!file_exists($mainConfig) && $ciimsConfig['params']['yiiPath'] == "") 
{
    require_once(dirname(__FILE__).'/protected/modules/install/installer.php');
    exit();
}

require_once($ciimsConfig['params']['yiiPath'].'yii.php');

// If YiiBootstrap throws a CException becausae of permissions, catch the error, route to back to the installer, and display it within pre-bootstrap for the user to correct.
try {
    Yii::createWebApplication($config)->run();
} catch (Exception $e) {
    require_once(dirname(__FILE__).'/protected/modules/install/installer.php');
    exit();
}