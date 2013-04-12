<?php return array(
    'basePath' => dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name' => NULL,
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
    'components' => array(
        'cii' => array(
            'class' => 'ext.cii.components.CiiBase'
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
            'errorAction' => 'site/error',
        ),
        'session' => array(
            'autoStart' => true,
            'sessionName' => 'CiiMS'
        ),
        'urlManager' => array(
            'class' => 'SlugURLManager',
            'cache' => true,
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => array(
                '/sitemap.xml' => '/site/sitemap',
                '/search/<page:\d+>' => '/site/mysqlsearch',
                '/search' => '/site/mysqlsearch',
                '/blog.rss' => '/content/rss',
                '/blog/<page:\d+>' => '/content/list',
                '/' => '/content/list',
                '/blog' => '/content/list',
                '/activation/<email:\w+>/<id:\w+>' => '/site/activation',
                '/activation' => '/site/activation',
                '/forgot/<id:\w+>' => '/site/forgot',
                '/forgot' => '/site/forgot',
                '/register' => '/site/register',
                '/register-success' => '/site/registersuccess',
                '/login' => '/site/login',
                '/logout' => '/site/logout',
                '/admin' => '/admin'
            ),
        ),
        'db' => array(
            'class' => 'CDbConnection',
            'connectionString' => NULL,
            'emulatePrepare' => true,
            'username' => NULL,
            'password' => NULL,
            'charset' => 'utf8',
            'schemaCachingDuration' => '3600',
            'enableProfiling' => true,
        ),
        'cache' => array(
            'class' => 'CFileCache',
        ),
    ),
    'params' => array(
        'yiiPath' => NULL,
        'encryptionKey' => NULL,
    ),
);

