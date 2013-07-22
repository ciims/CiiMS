#### Available Options
CiiMS has a bunch of options that are available through the _settings_ module in the admin panel. All options that require additional information have been outlined in this document

As of CiiMS 1.8 most settings can be fully managed through the dashboard, however there are some settings that require modifying your main.php config file.

------------------------

## Configuration File Options

##### Enabling YII_DEBUG (Debug Mode + Full Stack Trace On Error)

By default, CiiMS runs yiilite rather than yii to improve performance. However if you're trying to debug an issue this can be extremely problematic. To run using Yii rather than YiiLite, and to get more verbode debugging errors, open up
    
    protected/config/main.php

And in the _params_ section, add the following,

    'debug' => true,

Debug mode can be disabled by either removing the line, or changing it to false.

###### Enabling Stack Traces

To enable the default Yii Stack Trace option YII_TRACE, enable YII_DEBUG, then add the following to your _params_ section of your config file.

    'trace' => 3
    
Valid values for trace are 0, 1, 2, and 3.

##### HybridAuth

HybridAuth is a plugin which allows visitors to signin and comment using their social network identity. CiiMS automatically integrates social identies with existing user records if they exist.

To enable you will need key and secret key by the network provider (Twitter/Facebook/Google). Information about how to obtain these can be found [here](http://hybridauth.sourceforge.net/userguide.html#index).

As of 1.8, values for Google+, Facebook, and Twitter can be managed through the dashboard. Until more providors are added, you will need to manually add additional providors to the HybridAuth config file.

Once you have the key and secret, add the following to the "modules" section of

    protected/config/main.php

PROVIDER_NAME, and keys will need to be changed for each provider. Make sure you provide only what is necessary. If your provider doesn't require a component, leave it blank.

```php
    'hybridauth' => array(
        'providers'=> array(
            'PROVIDER_NAME_UC_WORDS' => array(
                'enabled' => true
                'keys' => array('id' => '', 'key' => '', 'secret'=>''),
                'scope' => ''
            )
        )
    )
```

The callback URL is http://your-site-domain.tld/hybridauth/provider. Assuming you have configured CiiMS with the appropriate config, and setup the provider everything should fire right up. If you run into issues make sure your provider config is setup properly and that the provider config on the providers site is setup properly.

Additional HybridAuth providers can be installed by copying the provider file to protected/modules/hybridauth/hybrid/providers/

Additional information can be found on [Hybridauth's Website](http://hybridauth.sourceforge.net/userguide.html#index)

##### Enable Memcache/Redis/APC Caching Support

By default CiiMS will run with CFileCache enabled. Performance can be _greatly_ improved by using CiiMemCache, CiiAPCCache, or CiiRedisCache instead.
You can modify the behavior by updating the _cache_ section of your protected/config/main.php file.

My recommendation is to use CiiMemCache or CiiRedisCache for the best performance.

###### CiiMemCache

```php
    'cache'=>array(
        'class'=>'application.components.CiiMemCache',
        'servers'=>array(
            array(
                'host'=>'127.0.0.1',
                'port'=>11211,
                'weight'=>60
            ),
       ),
     ),
```

###### CiiAPCCache

```php
    cache' => array(
        'class' => 'system.caching.CApcCache',
    ),
```

###### CiiRedisCache
CiiRedisCache is configured to use [phpredis](https://github.com/nicolasff/phpredis) as the Redis driver. You'll need to install this package to your system _before_ configuring your Redis cache. Please note that the "servers" array is misleading - only one sserver can be adde to the configuration.

```php
    'cache' => array(
        'class' => 'CiiRedisCache',
        'servers' => array(
            'host' => '127.0.0.1',
            'port' => 6379
        )
    ),
```

Edit the host, port and weight as you see fit for each of the examples provided above

##### Sphinx Search

CiiMS has built in support for Sphinx Server, allowing it to quickly index and search documents. By default this is disabled for MySQLSearch, which isn't nearly as accurate or fast.

To activate Sphinx, please view the "Search" section at the bottom of General Settings, enable Sphinx search and provide the following fields.

    sphinxHost
    sphinxPort
    sphinxSource

While configuring Sphinx is beyond the scope of this document, your datasource should be configured as followed. This will ensure you get accurate search results.

~~~
    source src
    {
            type                    = mysql
            sql_host                = DBHOST
            sql_user                = DBUSER
            sql_pass                = DBPASS
            sql_db                  = DBNAME
            sql_port                = 3306

            sql_sock               = /var/lib/mysql/mysql.sock
            sql_query               = \
                    SELECT id, title, content, extract FROM content AS t WHERE vid = (SELECT MAX(vid) FROM content WHERE id = t.id) AND status = 1;
    }

    index index_name
    {
            source = src
            path   = /var/lib/sphinx/data/index_name
    }
~~~

I recommend that you put Sphinx on a cronjob to reindex hourly (or as frequently as you desire).

------------------

## Key Value Options (DEPRECATED)

All key=>value options have been migrated to the new dashboard for management. Please review the new dashboard to manage these settings.