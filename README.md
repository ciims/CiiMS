## CiiMS

#### What is CiiMS?

CiiMS is a high performance CMS designed for both end users and developers. CiiMS is fast, powerful, extendable, and flexible, and is optimized to run with a combination of tools such as Memcache, APC, and Sphinx, but can run in other configurations.

CiiMS is designed to be fast, easy to deploy, and easy to modify.

#### Seeing is Believing
    Forget the details, just let me see it already...

Sure thing boss: A demo of CiiMS can be found at:

    Site: http://ciims.erianna.com
    Admin Panel: http://ciims.erianna.com/admin
    
You may use the following credentials to login and manage the site.

    Email: admin
    Pasword: admin

Please note that this demo is not monitored, and is reset at an unspecified interval and at my discretion. Please be nice.

#### Features
* Full Admin Panel
* Self Installer
* [Bootstrapped](http://twitter.github.com/bootstrap/) for Beauty
* [Markdown Extra](http://daringfireball.net/projects/markdown/) Support
* Auto CSS/Script optimization and compression
* Dynamic URL Slugs for SEO
* Automatic SEO (Metadata, Keywords, URLs)
* Password Protected Feeds
* Integrated RSS
* Multiple Content Types (Posts, Pages)
* Interchangable Caching Systems
* Low Memory Footprint
* Themable
* Social Integration
* Extendable with custom modules/extensions

------------------

#### Requirements

* PHP 5.3.1+
* Yii Framework 1.1+ (Consequently Basic Yii Requirements)
* MySQL 5.5+ (Should be compatible with MySQL 5.0, Postgres, and SQLite manual installations)

#### Recomendations
The following extensions/applications are recommended to improve performance.

* Memcache/d
* APC Cache
* Sphinx Search Server

------------------

#### Setup, Notes and Installation
CiiMS comes with a built in installer which will walk you through the setup process and provide you with information in the event it can't do something. Each step is outlined below.

##### Welcome Page
The welcome page will tell you what you need for your installation.

##### Yii Framework
The yii page will help you in setting up CiiMS with the Yii Framework location.

##### Requirements Check
CiiMS will check that all requirements are met for both Yii and it's internal system. Any issue will be flagged with a red error label. All issues must be resolved before submitting.

Most likely the issues you will be presented with are a missing extension or invalid permissions.

For missing extensions, install the extension manually using:

    pecl install [extension]

For bad permissions

    chmod 755 [dir/file]

##### MySQL Setup
The installer currently only supports MySQL. If you wish to use another database you will have to manually build the config file. The Installer will prompt you for the following information:

* Database Host
* Database Name
* Database User
* Database Password

##### Final Setup
The last page will prompt you to supply the admin user information and the site name.

After completing this last stage you will be prompted to remove the install.php Via FTP or bash, remove the file

    rm install.php

Then refresh the page. If everything went well you should now be presented with you blog front page.

If you encounter any errors, PHP should output those errors to the browser for you to correct.

#### Post Install

One thing you will need to do before users can register on your site is setup your site with ReCaptcha keys. Go to [recaptcha](http://www.recaptcha.com) and register your site and retrieve your keys, then edit the 'params' array in protected/config/main.php with the following

```php
	'params' => array(
		'reCaptchaPrivateKey' => 'YOUR_PK_HERE',
		'reCaptchaPublicKey'  => 'YOUR_PUB_KEY_HERE'
	)
```

------------------

#### Built in Extensions
CiiMS has several built in extensions and modules that are disabled by default, but can easily be enabled by modifying CiiMS configuration file.

##### HybridAuth
HybridAuth is a plugin which allows visitors to signin and comment using their social network identity. CiiMS automatically integrates social identies with existing user records if they exist.

To enable you will need key and secret key by the network provider (Twitter/Facebook/Google). Information about how to obtain these can be found [here](http://hybridauth.sourceforge.net/userguide.html#index)

Once you have the key and secret, add the following to the "modules" section of

    protected/config/main.php

PROVIDER_NAME, and keys will need to be changed for each provider
```php
	'hybridauth' => array(
		'providers'=> array(
			'PROVIDER_NAME' => array(
				'enabled' => true
				'keys' => array('id' => '', 'key' => '', 'secret'=>''),
				'scope' => ''
			)
		)
	)
```

Additional HybridAuth providers can be installed by copying the provider file to protected/modules/hybridauth/hybrid/providers/

Additional information can be found on [hybridauths website](http://hybridauth.sourceforge.net/userguide.html#index)

##### CSS/Script Optimization
By default, CiiMS will optimize and combine any CSS/Script files registered with

```php
    Yii::app()->clientScript->register[Script|Css|ScriptFile|CssFile]
```

By default, CSS compression and combination is on and script combination is on.

Scrpt compression is disabled by default because of some issues that arise when certain scripts are combined together. If you wish to enable this feature, change the following to true

```php
	'components' => array(
		'clientScript' => array(
			'optimizeScriptFiles '=> false
		)
	)
```

##### Enable Memcache Support
By default CiiMS will run with CFileCache enabled. Performance can be improved by using CiiMemCache instead. Memcache support can be enabled by modifying the 'cache' item under 'components':

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

Edit the host, port and weight as you see fit.

##### Sphinx Search
CiiMS has built in support for Sphinx Server, allowing it to quickly index and search documents. By default this is disabled for MySQLSearch, which isn't nearly as accurate or fast.

To enable Sphinx, you need to make server changes to your config file.

First, add the following to your params array

```php
	'sphinxHost'=>'localhost',
         'sphinxPort'=>'9312',
         'sphinxSource'=>'SOURCE_NAME',
```

Second, replace the URLManager->rules->search array with the following

```php
	'search/<page:\d+>'=>'/site/search',
	'search/<id:\d+>'=>'/site/search',
```

While configuring Sphinx is beyond the scope of this document, your datasource should be configured as followed:

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

index erianna
{
        source = src
        path   = /var/lib/sphinx/data/erianna
}
~~~~

I recommend that you put Sphinx on a cronjob to reindex hourly (or as frequently as you desire).

------------------

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
key: piwikID	value: PiwikSiteID // required
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

------------------

#### What If I Need Additional Help?
At the present you can submit a Github issue. If the need arises I'll create a support forum.

#### What Still Needs to be Done?
The current roadmap will be added to this soon. For now:

* Unit Tests

#### License

MIT LICENSE
Copyright (c) 2012 Charles R. Portwood II

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
