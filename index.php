<?php
/**
 * This is the primary bootstrapper for CiiMS. Almost every option is derived from the
 * configuration file or the default configuration file. This bootstrapper also disables error
 * reporting so that E_NOTICES and non-fatal errors don't crash it.
 *
 * You should _never_ have to change _anything_ in this file _ever_
 */

defined('DS') or define('DS', DIRECTORY_SEPARATOR);

// Bypass Yiic entirely and use this instead as the cli bootstrapper
if (php_sapi_name() === 'cli')
	return require __DIR__.DS.'protected'.DS.'yiic.php';

// Disable Error Reporting and set some constants
error_reporting(0);
ini_set('display_errors', 'false');
date_default_timezone_set ('UTC');


// This is the configuration file
if (!isset($_SERVER['CIIMS_ENV']))
        $_SERVER['CIIMS_ENV'] = 'main';

$config = __DIR__.DS.'protected'.DS.'config'.DS.$_SERVER['CIIMS_ENV'].'.php';
$defaultConfig=__DIR__.DS.'protected'.DS.'config'.DS.'main.default.php';

// If we don't have a configuration file, run the installer.
if (!file_exists($config) && file_exists('install.php'))
{
	require('install.php');
	exit();
}

$config = require($config);
defined('YII_DEBUG') or define('YII_DEBUG',isset($config['params']['debug']) ? $config['params']['debug'] : false);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',isset($config['params']['trace']) ? $config['params']['trace'] : 0);

// Load the config file
$defaultConfig = require($defaultConfig);

// Include the composer dependencies
require_once __DIR__.DS.'vendor'.DS.'autoload.php';
require_once __DIR__.DS.'vendor'.DS.'yiisoft'.DS.'yii'.DS.'framework'.DS.(YII_DEBUG ? 'yii.php' : 'yiilite.php');

Yii::setPathOfAlias('vendor', __DIR__.DS.'vendor');

// Merge it with our default config file
$config = CMap::mergeArray($defaultConfig, $config);

// Include the ClassMap for enhanced performance if we're not in debug mode
if (!YII_DEBUG)
	require_once __DIR__.DS.'protected'.DS.'config'.DS.'classmap.php';

$config['components']['db']['enableProfiling'] = YII_DEBUG;
$config['components']['db']['enableParamLogging'] = YII_DEBUG;

// If debug mode is enabled, show us every possible error anywhere.
if (YII_DEBUG && YII_TRACE_LEVEL == 3)
{
	error_reporting(-1);
	ini_set('display_errors', 'true');

	// Enable WebLogRouteLogging
	$config['preload'][] = 'log';

	// Enable all the logging routes
	foreach ($config['components']['log']['routes'] as $k=>$v)
		$config['components']['log']['routes'][$k]['enabled'] = YII_DEBUG;
}

// Run the Yii application instance
Yii::createWebApplication($config)->run();
