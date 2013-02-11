<?php return array(
    'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name' => 'CiiMS Installer',
    'defaultController' => 'default',
    
    'preload' => array(
        'bootstrap',
        'log'
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
            'responsiveCss' => true
        ),
        'clientScript' => array(
            'class' => 'ext.minify.EClientScript',
            'combineScriptFiles' => false,
            'combineCssFiles' => false,
            'optimizeCssFiles' => false,
            'optimizeScriptFiles' => false,
            'compressHTML'        => false
        ),
        'errorHandler' => array(
            'errorAction' => '/install/default/error',
        ),
        'session' => array(
            'autoStart' => true,
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => array(
                '' => '/install/default/index'
            ),
        ),
    ),
    'params' => array(
        'yiiVersionPath' => 'yii-1.1.13',
        'yiiDownloadPath' => 'https://github.com/yiisoft/yii/archive/1.1.13.zip', // 1.1.13 is the latest version of the framework
        'yiiPath' => ''
    ),
);

