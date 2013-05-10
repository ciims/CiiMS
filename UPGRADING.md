#### UPDATING CIIMS
The _easiest_ way to upgrade CiiMS is to use git, and manually run the migrations as such:

~~~~
    cd _repo dir_
    git pull origin master
    php protected/yiic.php migrate --interactive=0
~~~~

This will update the repository with the latest code and perform any database migrations. You can automate migrations by adding the following to your *.git/hooks/post-receive* file

~~~~
#!/bin/bash
    cd ../../protected/
    PHP=$(which php)

    $PHP yiic.php migrate --interactive=0
~~~~

Please read the rest of this document for specific upgrade instructions. CDbMigration _will not_ automatically perform these for you. You need to do it manually.

#### UPGRADING FROM 1.2 to 1.7+
If you're currently running 1.2 and would like to upgrade to 1.7, you _must_ make some modifications to your configuration file before proceeding. Instead of having to define _everything_ in your config file, you only have to define and override what you use. This allows you to _never_ have to update your configuration file again for future updates. Changing is only required if you want to alter the default behavior of a particular element. This should be the _last_ time we need to make this kind of change again. Of course, you can keep your current config if you like, just know you're overriding all the defaults, and stuff might not work in the future. That's why I minimizing your config.


1) Copy your current database configuration from protected/config/main.php. Yours will look something like follows:
~~~
     'db' => array(
        'class' => 'CDbConnection',
        'connectionString' => 'mysql:host=<host>;dbname=<dbname>',
        'emulatePrepare' => true,
        'username' => '<username>',
        'password' => '<password>',
        'charset' => 'utf8',
        'schemaCachingDuration' => '3600',
        'enableProfiling' => true,
    ),
~~~

2) Setup your config file so it looks as follows, replacing __host__, __dbname__, __username__, and __password__ to whatever yours is.
~~~
    <?php return array(
        'basePath' => '/path/to/protected/',
        'components' => array(
             'db' => array(
                'class' => 'CDbConnection',
                'connectionString' => 'mysql:host=<host>;dbname=<dbname>',
                'emulatePrepare' => true,
                'username' => '<username>',
                'password' => '<password>',
                'charset' => 'utf8',
                'schemaCachingDuration' => '3600',
                'enableProfiling' => true,
            ),
        ),
        'params' => array(
            'yiiPath' => '/path/to/yii/framework/',
            'encryptionKey' => '',
        ),
    );
~~~

3) Proceed the upgrade as usual
4) Run migrations

~~~
    php protected/yiic.php migrate --interactive=0
~~~

#### UPGRADING FROM 1.x to 1.2+
I have restructed the way that CiiMS derives certain paths in order to make future development easier, and to support many new features. After upgrading you _*MUST*_ add the following to your config file. Your blog will be unavailable until this is corrected. If you are upgrading from versions older than 1.1.2, it is recommended that you add this to your config file, then run the upgrade instructions listed above.

~~~~
    'params' => array(
	    'yiiPath'=>'/opt/frameworks/php/yii/framework/',
     )
~~~~