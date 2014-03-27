<?php

error_reporting(-1);
ini_set('display_errors', true);
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

// change the following paths if necessary
$yiit=__DIR__.DS.'..'.DS.'..'.DS.'vendor'.DS.'yiisoft'.DS.'yii'.DS.'framework'.DS.'yiit.php';
$config=dirname(__FILE__).'/../config/test.php';

$CiiMSTestConfig = require_once($config);
require_once($yiit);
require_once(dirname(__FILE__).'/WebTestCase.php');

Yii::createWebApplication($CiiMSTestConfig);