#### Available Options
CiiMS has a bunch of options that are available through the _settings_ module in the admin panel. All available options and how to use/config them are documented in this file.

_Many_ options are available as key=>value settings, however there are some settings that require modifying your main.php config file.

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

The following table contains a list of all available options that CiiMS currently supports. This table has been sorted alphabetically for ease of searchability. Please note, I am using set notation for a lot of the valid options. Just a reminder.

```
Z is the set of all integers
n is any given integer given a condition
| means such that
```
<table width="100%">
    <tr>
        <td width="20%"><em>Option Key</em></td>
        <td><em>Description</em></td>
        <td><em>Default Value</em></td>
        <td><em>Valid Options</em></td>
    </tr>
    <tr>
        <td><strong>addThisExtension</strong></td>
        <td>Whether or not the [AddThis](http://www.addthis.com) extention should be enabled. This should _always_ be used with __addThisAccount__</td>
        <td>0</td>
        <td>0,1</td>
    </tr>
    <tr>
        <td><strong>addThisAccount</strong></td>
        <td>The account associated with [AddThis](http://www.addthis.com). This should _always_ be used with __addThisExtension__, as the extension will not render unless it is enabled</td>
        <td>0</td>
        <td>0,1</td>
    </tr>
    <tr>
        <td><strong>bcrypt_cost</strong></td>
        <td>This is the total work effort that the bcrypt hashing algorithm should use to calculating password hashes. The larger the number, the more time it takes to calculate a password hash. As a security precaution, CiiMS prevents any number less than 12. It is recommended that you monitor this number every 18 months or so. Faster computers will be able to run bcrypt faster, so as computers become more powerful you'll need to adjust this. <br />Changing the number will require a hash recalculation the next time the user logs in, allowing you to arbitarily set the cost without any consequence to the end user. You can read more about bcrypt and its implementation [here](http://en.wikipedia.org/wiki/Bcrypt)</td>
        <td>12</td>
        <td>n >= 12</td>
    </tr>
    <tr>
        <td><strong>categoryPaginationSize</strong></td>
        <td>This is the number of blog posts that should be shown on a category page.</td>
        <td>10</td>
        <td>n &sub; Z | n > 0</td>
    </tr>
    <tr>
        <td><strong>contentPaginationSize</strong></td>
        <td>This is the number of blog posts that should be shown on a content page.</td>
        <td>10</td>
        <td>n &sub; Z | n > 0</td>
    </tr>
    <tr>
        <td><strong>gaAccount</strong></td>
        <td>The account number found in Google Analytics. Use this on conjunction with __gaExtension__</td>
        <td>NULL</td>
        <td>Google Analytics Account Number</td>
    </tr>
    <tr>
        <td><strong>gaAddThis</strong> <em>experimental</em></td>
        <td>Whether or not AddThis should try to inject into Google Analytics. See [this article](http://support.addthis.com/customer/portal/articles/381260-google-analytics-integration) for more information</td>
        <td>NULL</td>
        <td>0, 1</td>
    </tr>
    <tr>
        <td><strong>gaAddThisSocial</strong> <em>experimental</em></td>
        <td>Whether or not AddThis should try to inject into Google Analytics. See [this article](http://support.addthis.com/customer/portal/articles/381260-google-analytics-integration) for more information</td>
        <td>NULL</td>
        <td>0, 1</td>
    </tr>
    <tr>
        <td><strong>gaExtension</strong></td>
        <td>Whether or not [Google Analytics](analytics.google.com) tracking code should be injected in the page source code. __gaAccount__ _must_ be set of this option is enabled, otherwise tracking will fail.</td>
        <td>0</td>
        <td>0, 1</td>
    </tr>
    <tr>
        <td><strong>menu</strong></td>
        <td>This option provides _extremely limited_ menu management functionality, and accepts any valid _slug_ as a pipe "|" separated list. Each option will be rendered via Yii::app()->createUrl(), and use menu item as the url _and_ the link title. On the default theme, this renders at the top and bottom of each page.</td>
        <td>blog|admin</td>
        <td>page|page1|page2|page3</td>
    </tr>
    <tr>
        <td><strong>mobileTheme</strong></td>
        <td>This option provides the ability to present a different theme for mobile users. (Eg SmartPhones). While your desktop site may be responsive, it may not be mobile optimized. This option allows you to have a dedicated theme that is more lightweight and more geared towards mobile users.</td>
        <td>NULL</td>
        <td>Any valid theme foldername in the /themes folder</td>
    </tr>
    <tr>
        <td><strong>notifyAuthorOnComment</strong></td>
        <td>This determines whether or not an author should be notified when someone comments on a article they wrote.</td>
        <td>false</td>
        <td>0,1</td>
    </tr>
    <tr>
        <td><strong>notifyEmail</strong></td>
        <td>The email address that should be used for system notifications. This must be used in tandem with notifyName, otherwise it will fall back to the admin user</td>
        <td>Admin User's Email</td>
        <td>user@example.com {string}</td>
    </tr>
    <tr>
        <td><strong>notifyName</strong></td>
        <td>The name that emails will originate from. This must be used in tandem with notifyEmail, otherwise it will fall back to the admin user's display name</td>
        <td>Admin User's name</td>
        <td>{string}</td>
    </tr>
    <tr>
        <td><strong>offline</strong></td>
        <td>Whether or not the site should be put into offline mode. When the site is in offline mode, only the dashboard is accessible. Any other page will return an HTTP 403 error with generic site offline messaging</td>
        <td>0</td>
        <td>0, 1</td>
    </tr>
    <tr>
        <td><strong>piwikBaseUrl</strong></td>
        <td>This is the schema, host, and port which your [Piwik](http://www.piwik.org) instance is running. See __piwikExtension__ for more details.</td>
        <td>NULL</td>
        <td>schema://<domain>:<port></td>
    </tr>
    <tr>
        <td><strong>piwikExtension</strong></td>
        <td>Whether or not [Piwik](http://www.piwik.org) analytics tracking code should be injected in the page source code. This extension uses [EPiwikAnalyticsWidget](https://github.com/charlesportwoodii/EPiwikAnalyticsWidget). For displaying the plugin. If you enable this extension, you must also provide valid values for __piwikBaseUrl__ and __piwikId__.</td>
        <td>0</td>
        <td>0, 1</td>
    </tr>
    <tr>
        <td><strong>piwikId</strong></td>
        <td>This is the instance id of your site in [Piwik](http://www.piwik.org). See the "All Websites" tab in Piwik to determine what your site_id is. See __piwikExtension__ for more details.</td>
        <td>NULL</td>
        <td>Integer ID of piwik site tracking id</td>
    </tr>
    <tr>
        <td><strong>preferMarkdown</strong></td>
        <td>Whether or not Markdown should be used for the default content editor. When set to "1", the default content editor will be a standard text area with Markdown Extra previewing support. When set to "0", [Imperavi Redactor](redactor.imperavi.ru) will be used. instead.</td>
        <td>1</td>
        <td>0, 1</td>
    </tr>
    <tr>
        <td><strong>searchPaginationSize</strong></td>
        <td>This is the number of blog posts that should be shown in the search results.</td>
        <td>10</td>
        <td>n &sub; Z | n > 0</td>
    </tr>
    <tr>
        <td><strong>SMTPHost</strong></td>
        <td>The hostname of the SMTP server you are attempting to connect to. If not provided, the system will attempt to send emails through the local system (eg Postfix)</td>
        <td>localhost</td>
        <td>localhost, 127.0.0.1, smtp.example.com</td>
    </tr>
    <tr>
        <td><strong>SMTPPort</strong></td>
        <td>The integer port number that the SMTP server is listening on. If not provided, this option will not be used</td>
        <td>NULL</td>
        <td>25</td>
    </tr>
    <tr>
        <td><strong>SMTPUser</strong></td>
        <td>The username used to authenticate with the SMTP server</td>
        <td>NULL</td>
        <td>user@example.com</td>
    </tr>
    <tr>
        <td><strong>SMTPPass</strong></td>
        <td>The password associated to the SMTPUser used for authentication</td>
        <td>NULL</td>
        <td>password</td>
    </tr>
    <tr>
        <td><strong>splash-logo</strong></td>
        <td>This is the splash page logo that is used on every page on the default theme. Not all themes support this option.</td>
        <td>/images/splash-logo.jpg</td>
        <td>Any image. Recommended size is 965px x 400px</td>
    </tr>
    <tr>
        <td><strong>theme</strong></td>
        <td>Determines the theme that should be used. "default" will be used if one is not specified, the requested theme doesn't exist, or there was an error in the theme rendering process.</td>
        <td>default</td>
        <td>Any valid theme foldername in the /themes folder</td>
    </tr>
    <tr>
        <td><strong>twitter_username</strong></td>
        <td>On the default theme, this is the twitter handle that is used to display updates in the bottom left corner of the footer. This may provide interconnectivity with other feature on the dashboard in the future.</td>
        <td>charlesportwoodii</td>
        <td>Any valid @twitter handle/td>
    </tr>
    <tr>
        <td><strong></strong></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
</table>
