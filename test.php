<?php

// Set Error Reporting Levels
error_reporting(-1);
ini_set('display_errors', 'true');

// Definitions
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('YII_DEBUG') or define('YII_DEBUG',true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

if (!isset($_SERVER['CIIMS_ENV']))
        $_SERVER['CIIMS_ENV'] = 'main';
else if (isset($_SERVER['TRAVIS']) && $_SERVER['TRAVIS'] == true)
        $_SERVER['CIIMS_ENV'] = 'travis';

$config = require __DIR__.DS.'protected'.DS.'config'.DS.$_SERVER['CIIMS_ENV'].'.php';
$defaultConfig = require __DIR__.DS.'protected'.DS.'config'.DS.'main.default.php';

// Load Yii and Composer extensions
require_once __DIR__.DS.'vendor'.DS.'yiisoft'.DS.'yii'.DS.'framework'.DS.'yii.php';
require_once __DIR__.DS.'vendor'.DS.'autoload.php';
Yii::setPathOfAlias('vendor', __DIR__.DS.'vendor');

$config = CMap::mergeArray($defaultConfig, $config);

$_SERVER['SERVER_NAME'] = 'localhost';

// Return for Codeception
return array(
    'class' => 'CWebApplication',
    'config' => $config,
);
