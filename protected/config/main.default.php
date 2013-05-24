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
 * @copyright  Charles R. Portwood II <https://www.erianna.com> 2012-2013
 * @license    http://opensource.org/licenses/MIT  MIT LICENSE
 * @link       https://github.com/charlesportwoodii/CiiMS
 */
return array(
    'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name' => NULL,
    'sourceLanguage' => 'en_US',
    'language' => 'en_US',
    'preload' => array(
        'cii',
        'bootstrap',
    ),
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.modules.*',
    ),
    'modules' => array(
        'admin',
    ),
    'behaviors' => array(
        'onBeginRequest' => array(
             'class' => 'ext.yii-newrelic.behaviors.YiiNewRelicWebAppBehavior',
        ),
    ),
    'components' => array(
        'newRelic' => array(
            'class' => 'ext.yii-newrelic.YiiNewRelic',
        ),
        'cii' => array(
            'class' => 'ext.cii.components.CiiBase'
        ),
        'bootstrap' => array(
            'class' => 'ext.bootstrap.components.Bootstrap',
            'responsiveCss' => true
        ),
        'clientScript' => array(
            'class' => 'ext.minify.EClientScript',
            'combineScriptFiles'    => true,
            'combineCssFiles'       => true,
            'optimizeCssFiles'      => true,
            'optimizeScriptFiles'   => true,
            'compressHTML'          => true
        ),
        'errorHandler' => array(
            'errorAction' => 'site/error',
        ),
        'session' => array(
            'autoStart'     => true,
            'sessionName'   => 'CiiMS',
            'cookieMode'    => 'only', 
        ),
        'urlManager' => array(
            'class'          => 'CiiURLManager',
            'cache'          => true,
            'urlFormat'      => 'path',
            'showScriptName' => false
        ),
        'db' => array(
            'class'                 => 'CDbConnection',
            'connectionString'      => NULL,
            'emulatePrepare'        => true,
            'username'              => NULL,
            'password'              => NULL,
            'charset'               => 'utf8',
            'schemaCachingDuration' => '3600',
            'enableProfiling'       => true,
        ),
        'cache' => array(
            'class' => 'CFileCache',
        ),
    ),
    'params' => array(
        'yiiPath'       => NULL,
        'encryptionKey' => NULL,
        'debug'         => false,
        'trace'         => 0
    ),
);