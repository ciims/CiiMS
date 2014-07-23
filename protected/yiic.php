<?php
error_reporting(-1);
ini_set('display_errors', 'true');
defined('DS') or define('DS', DIRECTORY_SEPARATOR);

defined('YII_DEBUG') or define('YII_DEBUG', true);

// Include the composer dependencies
require(__DIR__.DS.'..'.DS.'vendor'.DS.'autoload.php');
require(__DIR__.DS.'..'.DS.'vendor'.DS.'yiisoft'.DS.'yii'.DS.'framework'.DS.'yii.php');

Yii::setPathOfAlias('vendor', __DIR__.DS.'..'.DS.'vendor'.DS);

print_r($_SERVER);
print_r($_ENV);
if (!isset($_SERVER['CIIMS_ENV']))
	$_SERVER['CIIMS_ENV'] = 'main';
else if (isset($_SERVER['TRAVIS']) && $_SERVER['TRAVIS'] == true)
	$_SERVER['CIIMS_ENV'] = 'travis';

$config = require __DIR__.DS.'config'.DS.$_SERVER['CIIMS_ENV'].'.php';
$defaultConfig = require __DIR__.DS.'config'.DS.'main.default.php';

$config = CMap::mergeArray($defaultConfig, $config);

unset($config['components']['user']);

$app=Yii::createConsoleApplication($config);
$app->commandRunner->addCommands(YII_PATH.'/cli/commands');
$env=@getenv('YII_CONSOLE_COMMANDS');
if(!empty($env))
   $app->commandRunner->addCommands($env);

$app->run();
