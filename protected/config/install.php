<?php return array(
    'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name' => 'CiiMS Installer',
    'preload' => array(
        'log'
    ),
    'import' => array(
        'application.components.*',
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
            ),
        ),
    ),
    'params' => array(
        'yiiPath' => '',
        'stage' => 0
    ),
);

