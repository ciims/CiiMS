#### Available Options
CiiMS has a bunch of options that are available through the _settings_ module in the admin panel. All available options and how to use/config them are documented in this file.

_Many_ options are available as key=>value settings, however there are some settings that require modifying your main.php config file.

------------------------

## Configuration File Options

##### HybridAuth
HybridAuth is a plugin which allows visitors to signin and comment using their social network identity. CiiMS automatically integrates social identies with existing user records if they exist.

To enable you will need key and secret key by the network provider (Twitter/Facebook/Google). Information about how to obtain these can be found [here](http://hybridauth.sourceforge.net/userguide.html#index)

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
By default CiiMS will run with CFileCache enabled. Performance can be improved by using CiiMemCache, CiiAPCCache, or CiiRedisCache instead.
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
CiiRedisCache is configured to use [phpredis](https://github.com/nicolasff/phpredis) as the Redis driver. You'll need to install this package to your system _before_ configuring your Redis cache.

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

To enable Sphinx, you need to make server changes to your config file.

First, add the following to your params array

```php
    'sphinxHost'=>'localhost',
    'sphinxPort'=>'9312',
    'sphinxSource'=>'SOURCE_NAME',
```

Second, _add_ the URLManager->rules->search array with the following. This will allow your app to connect to the sphinx search action.

```php
    'search/<page:\d+>'=>'/site/search',
    'search/<id:\d+>'=>'/site/search',
```

While configuring Sphinx is beyond the scope of this document, your datasource should be configured as followed. This will ensure you get accurate search results.

~~~~
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
~~~~

I recommend that you put Sphinx on a cronjob to reindex hourly (or as frequently as you desire).

------------------

## Key Value Options

##### Google Analytics Plugin
Google Analytics is disabled by default. To enable, add the following to settings via the admin panel: (without "key", and "value"

~~~~
key: gaExtension
value: 1
~~~~

The Google Analytics Plugin also comes with serveral other options as defined below

~~~~
key: gaAccount    value: GAAccountCode (required)
key: gaAddThis    value: true/false    // Enables Add This in GA
key: gaAddThisShare value: 'true'/'false // note the quotes
~~~~

##### Piwik Analytics
CiiMS also comes with support for Piwik. This can be enabled by adding the following to settings via the admin panel

~~~~
key: piwikExtension
value: 1
~~~~

The Piwik Plugin also has the following options

~~~~
key: piwikID    value: PiwikSiteID // required
key: piwikBaseUrl value: baseUrlOfPiwk // required
~~~~


##### AddThis
CiiMS has integrated support for AddThis. AddThis can be enabled by adding the following to settings via the admin panel

~~~~
key: addThisExtension
value: 1

key: addThisAccount
value: AddThisAccountID [ra-xxxxxxxxxxxxxxxxxxx]
~~~~

#### Comment Notifications

CiiMS supports email notifications when a new comment is created on a post you created. To enable this, add the following to your config. By default it is disabled.
~~~~
key: notifyAuthorOnComment
value: 1
~~~~