<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This is the main configuration file for CiiMS. Options for CiiMS extend from this base
 * configuration file, and should be written to protected/config/main.php. They will override _any_
 * configuration variable listed in here via CMap::mergeArray()
 *
 * The reason for doing this is to reduce the number of options written directly to the main.php file,
 * so that we're only writing out what is _needed_ in that config file. Additionally, if we want to
 * introduce new functionality in the future, we can safely add it to this config file without having
 * to make sure the end user adds it to their config file. The point is to make the main.php config file
 * a write once file so the end user never has to deal with it after the install.
 *
 * @package    CiiMS Content Management System
 * @author     Charles R. Portwood II <charlesportwoodii@ethreal.net>
 * @copyright  Charles R. Portwood II <https://www.erianna.com> 2012-2014
 * @license    http://opensource.org/licenses/MIT  MIT LICENSE
 * @link       https://github.com/charlesportwoodii/CiiMS
 */
return array(
    'basePath' => __DIR__.DS.'..',
    'name' => NULL,
    'sourceLanguage' => 'en_US',
    'preload' => array('cii', 'analytics'),
    'import' => array(
        'application.modules.*',
        'application.models.*',
        'application.models.forms.*',
        'application.models.settings.*',
    ),
    'modules' => require_once __DIR__ . DS . 'modules.php',
    'behaviors' => array(
        'onBeginRequest' => array(
             'class' => 'vendor.gtcode.yii-newrelic.behaviors.YiiNewRelicWebAppBehavior',
        ),
    ),
    'components' => array(
        'messages' => array(
            'class' => 'ext.cii.components.CiiPHPMessageSource'
        ),
        'newRelic' => array(
            'class' => 'vendor.gtcode.yii-newrelic.YiiNewRelic',
        ),
        'cii' => array(
            'class' => 'ext.cii.components.CiiBase'
        ),
        'analytics' => array(
            'class' => 'ext.cii.components.CiiAnalytics',
            'lowerBounceRate' => true,
            'options' => array(),
        ),
        'assetManager' => array(
            'class' => 'ext.cii.components.CiiAssetManager',
        ),
        'clientScript' => array(
            'class' => 'ext.cii.components.CiiClientScript',
        ),
        'errorHandler' => array(
            'errorAction' => 'site/error',
        ),
        'session' => array(
            'autoStart'     => true,
            'sessionName'   => '_ciims',
            'cookieMode'    => 'only',
	        'cookieParams'  => array(
    	        'httponly' => true,
    		    'secure' => ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443))
    	    )
        ),
        'urlManager' => array(
            'class'          => 'ext.cii.components.CiiURLManager',
            'urlFormat'      => 'path',
            'showScriptName' => false
        ),
        'user' => array(
            'allowAutoLogin' => true,
            'authTimeout'    => 900,
            'autoRenewCookie'=> true
        ),
        'db' => array(
            'class'                 => 'CDbConnection',
            'connectionString'      => NULL,
            'emulatePrepare'        => true,
            'username'              => NULL,
            'password'              => NULL,
            'charset'               => 'utf8',
            'schemaCachingDuration' => 3600,
            'enableProfiling'       => false,
            'enableParamLogging'    => false
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CWebLogRoute',
                    'levels' => 'error, warning, trace, info',
                    'enabled' => false
                ),
                array( 
                    'class'=>'CProfileLogRoute', 
                    'report'=>'summary',
                    'enabled' => false
                )
            )
        ),
        'cache' => array(
            'class' => 'CFileCache',
        ),
    ),
    'params' => array(
        // The hash CiiMS should use for user data
        'encryptionKey'       => NULL,
        // Run in debug mode or not
        'debug'               => false,
        // The number of stack traces CiiMS should return
        'trace'               => 0,
        // Whether CiiMS should run in Demo Mode (which fixes some analytics values)
	    'demo' 		          => 0,
        // Defines the maximum filesize that CiiMS will allowed to be uploaded
        // Must not exceed php.ini post_max_size and upload_max_filesize
        'max_fileupload_size' => (10 * 1024 * 1024),
        // Cards endpoint
        'cards' => 'https://cards.ciims.io/1.0.0',
    ),
);
