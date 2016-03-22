# Advanced Options & Configuration

CiiMS provides several advanced options and configurations that increase performance and make CiiMS more versatile.

## Multi-Site Configuration

CiiMS can be easily configured to be run in a multi-site configuration. While in multi-site mode, CiiMS can serve multiple sites from the same codebase with each site having it's own dedicated database.

### Configuring Multi-Site

Multi-Site mode can be turned on with a single flag, and a small adjustment to the ```protected/config/main.php``` file. At it's core, this option is enabled by a single flag within the main config file.

```
define('CII_CONFIG', $_SERVER['CIIMS_ENV']);
```

The ```CIIMS_ENV``` env flag needs to be passed either from the command line, or from your web server on every request so that CiiMS knows what site to operate with. From the CLI this can be done via the ```export``` command.

```
export CIIMS_ENV="domain.example.tld"
```

And from your web-server this parameter should be passed as either an environment variable, or a fast_cgi param.

With this option configured, you can then rewrite your ```protected/config/main.php``` file as follows:

```
<?php
define('CII_CONFIG', $_SERVER['CIIMS_ENV']);

return require __DIR__ . DS . CII_CONFIG . '.php';
```

Then in your ```protected/config``` directory redefine the ```main.php``` file for each ```CII_CONFIG.php```.

```
[...]
protected/
	config/
		main.php
		site.example.com.php
		site2.example.com.php
[...]

```

Alternatively you can expand upon this and configure your ```main.php``` file to fetch this data from a cache, or a database. In this configuration each CiiMS instance will share the same codebase, work with different databases, and run exactly the same as if they were two completely different sites.*

### Multi-Site Limitations *

When running CiiMS in multi-site mode, there are a couple limitations imposed to prevent cross-domain access.

##### Themes

The first limitation is that themes cannot be installed via the dashboard. This is to prevent one user from abusing the system disk space, or upgrading a theme to an unspoorted version. All themes listed in the ```themes``` folder will be shared across all instances. If you want to install new themes for all users, simply install the theme via ```composer```, or copy the new theme to the ```themes``` directory.

##### CLI Commands

When running CiiMS in multi-site mode, you __MUST__ specify the site that you want to work with. On *nix systems running ```bash``` or ```sh```, this generally means prefacing each CLI command with the following:

```
export CIIMS_ENV="<env>" && php web/index.php {task}
```

The CLI installer also needs to be run with 2 extra parameters, and a custom INSTALL flag:

```
php web/index.php installer index [...]
	--force=1
	--writeConfig=0
```

Making the full installer command as follows:

```
export CIIMS_INSTALL=true && php web/index.php installer index --dbHost=value --dbName=value --dbUsername=value --dbPassword=value --adminEmail=value --adminPassword=value --adminUsername=value --siteName=value --force=1 --writeConfig=0
```

This modified command will force CiiMS to build a new site without overwriting the ```protected/config/main.php``` file. After running this command you will need to _manually_ generate the configuration file for the site using the configuration listed above.

## Improving Performance

Out of the box, CiiMS is pretty fast, and will take advantage of several caching strategies to render pages and data quickly. There are several optimizations you can make to improve the performance even more

### Caching

Out of the box, CiiMS will use a file-cache solutuion to store cached data. The cache can be sped up by using Redis, or Memcached. The recommended cache to use for CiiMS is __Redis__, specifically with [phpredis](https://github.com/phpredis/phpredis), a performant Redis C extension for PHP.


```
'cache' => array(
    'class' => 'CiiRedisCache',
    'server' => array(
    	'host' => '127.0.0.1',
    	'port' => 6379,
    	'db' => <n>
        'timeout' => 3 // in seconds
    )
)
```
Alternatively you can use one of the default Yii caches, or one of the more advanced cached bundled with CiiMS that accept the same parameters as the Yii caches. All caches can be found [on github](https://github.com/charlesportwoodii/cii/tree/master/cache). A detailed list of cached bundled with Yii framework can be found in the [offical guide](http://www.yiiframework.com/doc/guide/1.1/en/caching.overview).

### Enable Opcache

CiiMS has been throughly tested with ```zend_opcache``` enabled. The following configuration may be used:

```
zend_extension=opcache.so
opcache.enable_cli = true
opcache.error_log = /var/log/php.log
opcache.save_comments = false
opcache.enable_file_override = true
```

When Zend Opcache is enabled, you may need to restart your web server, or your PHP process for updates to take effect.