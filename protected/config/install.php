<?php return array(
    'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name' => 'CiiMS Installer',
    'defaultController' => 'default',
    'preload' => array(
        'bootstrap',
    ),
    'import' => array(
        'application.components.*',
        'application.modules.install.*'
    ),
    'modules' => array(
        'install',
    ),
    'components' => array(
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CWebLogRoute',
                    'levels'=>'trace,error,warning,notice',
                )       
            ),
        ),
        'bootstrap' => array(
            'class' => 'ext.bootstrap.components.Bootstrap',
            'responsiveCss' => false
        ),
        'errorHandler' => array(
            'errorAction' => '/install/default/error',
        ),
        'session' => array(
            'autoStart' => true,
        ),
        'db' => array(
            'class' => 'CDbConnection',
            'connectionString' => 'mysql:host=localhost;dbname=ciims',
            'emulatePrepare' => true,
            'username' => 'ciims',
            'password' => 'YWj4NHRs9veEczUe',
            'charset' => 'utf8',
            'schemaCachingDuration' => '3600',
            'enableProfiling' => true,
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => array(
                '' => '/install/default/index',
                '/migrate' => '/install/default/migrate',
            ),
        ),
    ),
    'params' => array(
        'yiiVersionPath' => 'yii-1.1.13',
        'yiiDownloadPath' => 'https://github.com/yiisoft/yii/archive/1.1.13.zip', // 1.1.13 is the latest version of the framework
        'yiiPath' => '/opt/frameworks/php/yii/framework/',
        'stage' => 0
    ),
);

