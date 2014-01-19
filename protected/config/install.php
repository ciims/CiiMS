<?php return array(
    'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name' => 'CiiMS Installer',
    'preload' => array(
        'cii',
    ),
    'import' => array(
        'application.components.*',
        'application.modules.install.*'
    ),
    'modules' => array(
        'install',
    ),
    'components' => array(
        'cii' => array(
            'class' => 'ext.cii.components.CiiBase'
        ),
        'errorHandler' => array(
            'errorAction' => '/install/default/error',
        ),
        'session' => array(
            'autoStart' => true,
            'sessionName'   => 'CiiMS_Installer',
            'cookieMode'    => 'only', 
        ),
        'cache' => array(
            'class' => 'CFileCache'
        ),
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => array(
                '' => '/install/default/index',
                '/migrate' => '/install/default/migrate',
                '/runmigrations' => '/install/default/runmigrations',
                '/createadmin' => '/install/default/createadmin',
                '/admin' => '/install/default/admin'
            ),
        ),
    ),
    'params' => array(
        'yiiVersionPath' => 'yii-1.1.14',
        'yiiDownloadPath' => 'https://github.com/yiisoft/yii/archive/1.1.14.zip', // 1.1.13 is the latest version of the framework
        'stage' => 0,
        'debug' => true,
        'trace' => 3
    ),
);

