<?php return array_merge(require_once(dirname(__FILE__) .'/main.default.php'),
    array(
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
        ),
        'params' => array(
            'yiipath' => dirname(__FILE__) . '/../../runtime/yii/framework/',
            'encryptionKey' => 'ag93ba23r'
        )
    )
);