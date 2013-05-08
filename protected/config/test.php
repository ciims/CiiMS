<?php 
return array(
    'basePath' => '/home/travis/build/charlesportwoodii/CiiMS/protected/config/..',
    'name' => 'CiiMS',
    'components' => array(
        'db' => array(
            'class' => 'CDbConnection',
                'connectionString' => 'mysql:host=localhost;dbname=ciims_test',
                'emulatePrepare' => true,
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8',
                'schemaCachingDuration' => '3600',
                'enableProfiling' => true,
        ),
        'cache' => array(
            'class' => 'CFileCache',
            /*'servers' => array(
                'host' => '127.0.0.1',
                'port' => 6379
            )*/
        ),
    ),
    'params' => array(
        'yiiPath' => dirname(__FILE__) . '/../runtime/yii/framework/',
        'encryptionKey' => 'ag93ba23r'
    )
);