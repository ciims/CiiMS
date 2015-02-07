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
            'class' => 'vendor.charlesportwoodii.cii.components.CiiBase'
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
         'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CWebLogRoute',
                    'levels' => 'error, warning, trace, info',
                    'enabled' => true
                ),
                array( 
                    'class'=>'CProfileLogRoute', 
                    'report'=>'summary',
                    'enabled' => true
                )
            )
        ),
    ),
    'params' => array(
        'stage' => 0,
        'debug' => true,
        'trace' => 3
    ),
);

