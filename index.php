<?php
/**
 * This is the primary bootstrapper for CiiMS. Almost every option is derived from the
 * configuration file or the default configuration file. This bootstrapper also disables error
 * reporting so that E_NOTICES and non-fatal errors don't crash it.
 *
 * You should _never_ have to change _anything_ in this file _ever_
 *
 * @package    CiiMS Content Management System
 * @author     Charles R. Portwood II <charlesportwoodii@ethreal.net>
 * @copyright  Charles R. Portwood II <https://www.erianna.com> 2012-2014
 * @license    http://opensource.org/licenses/MIT  MIT LICENSE
 * @link       https://github.com/charlesportwoodii/CiiMS
 */

// Disable Error Reporting
error_reporting(0);
ini_set('display_errors', 0);

// This is the configuration file
$config=dirname(__FILE__).'/protected/config/main.php';

// If we don't have a configuration file, run the installer.
if (!file_exists($config) && file_exists('install.php')) 
{
	require('install.php');
	exit();
}

// Load the config file
$config = require($config);

// Determine if we should enable debugging and call stack if debug and trace are set in our config file.
// By default this disabled
defined('YII_DEBUG') or define('YII_DEBUG',isset($config['params']['debug']) ? $config['params']['debug'] : false);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',isset($config['params']['trace']) ? $config['params']['trace'] : 0);

// Load the configuration file
require((string)$config['params']['yiiPath']. (YII_DEBUG ? 'yii.php' : 'yiilite.php'));

// Merge it with our default config file
$config = CMap::mergeArray(require(dirname(__FILE__).'/protected/config/main.default.php'), $config);

// Include the ClassMap for enhanced performance
require(dirname(__FILE__) . '/protected/config/classmap.php');

$config['components']['db']['enableProfiling'] = YII_DEBUG;
$config['components']['db']['enableParamLogging'] = YII_DEBUG;

// If debug mode is enabled, show us every possible error anywhere.
if (YII_DEBUG && YII_TRACE_LEVEL == 3) 
{
	error_reporting(-1);
	ini_set('display_errors', true);

	// Enable WebLogRouteLogging
	$config['preload'][] = 'log';
	$config['components']['log']['routes'][0]['enabled'] = YII_DEBUG;
}

// Run the Yii application instance
Yii::createWebApplication($config)->run();