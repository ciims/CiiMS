<?php

// Set Error Reporting Levels
error_reporting(-1);
ini_set('display_errors', 'true');

// Definitions
defined('DS') or define('DS', DIRECTORY_SEPARATOR);
defined('YII_DEBUG') or define('YII_DEBUG',true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

// Load the config files
$config = require __DIR__.DS.'protected'.DS.'config'.DS.'test.php';
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
