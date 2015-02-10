# CiiMS
[![TravisCI](http://img.shields.io/travis/charlesportwoodii/CiiMS/2.0.0-dev.svg?style=flat "TravisCI")](https://travis-ci.org/charlesportwoodii/CiiMS)
[![Scrutinizer Code Quality](http://img.shields.io/scrutinizer/g/charlesportwoodii/ciims.svg?style=flat)](https://scrutinizer-ci.com/g/charlesportwoodii/CiiMS/)
[![Downloads](http://img.shields.io/packagist/dt/charlesportwoodii/ciims.svg?style=flat)](https://packagist.org/packages/charlesportwoodii/ciims)
[![Gittip](http://img.shields.io/gittip/charlesportwoodii.svg?style=flat "Gittip")](https://www.gittip.com/charlesportwoodii/)
[![License](http://img.shields.io/badge/license-MIT-orange.svg?style=flat "License")](https://github.com/charlesportwoodii/CiiMS/blob/master/LICENSE.md)

![CiiMS Logo Banner](	
https://s3.amazonaws.com/ciims-s3-us-01/63E6ADC1A1BF41ABDE55B4BA810F187DBF1C9E696A90713D8A1F38B69E1071CE.png)

CiiMS is a fast, simple, and easy to use, full feature blogging platform and content management system built in Yii.

## News

__CiiMS 2.0.0 Beta Released__

CiiMS 2.0.0 Beta is now available. Checkout the [release notes](https://www.erianna.com/ciims-2-0-0-beta-release-announcement) for more information.

__Interested in a Hosted Solution?__

Be sure to checkout [www.ciims.io](http://www.ciims.io) to pre-register for exclusive early access to our hosted platform.

## Who uses CiiMS?

Do you use CiiMS and love it? Want to show your support? Would you like some free advertising for your site? Create a pull request that modifies this readme and adds a 60x60 avatar image as a link to your site and we'll merge it in!

[![Erianna by Charles R. Portwood II](https://secure.gravatar.com/avatar/7ea3ae65556979b64ba8cde5cd51c667?s=60, "Erianna by Charles R. Portwood II")](https://www.erianna.com)
<a href="https://www.ciims.io"><img title="CiiMS.org - Hosted CiiMS Solution" src="https://s3.amazonaws.com/ciims-s3-us-01/ciims-logo-badge.png" /></a>
[![Mosely](https://www.gravatar.com/avatar/dd61d5faf7eb9315960d528fc9ed2367?s=60, "Business as Usual")](https://www.manufactorum.net)

## Installation

CiiMS can be quickly installed through composer. For more information about installing CiiMS, checkoug the [installation guide](https://docs.ciims.io/installation.html).

```
composer create-project --prefer-dist --stability=dev charlesportwoodii/ciims {path} dev-master
cd {path}
composer dump-autoload -o
```

## Screenshots

__A beautitul default theme__

![Default Theme](	
https://s3.amazonaws.com/ciims-s3-us-01/5A2ED631D493E053774C31C513F2C60FF5208B1B3AB8193D7D8D351251E2207D.png)

__A powerful dashboard with custom JavaScript cards__

![CiiMS Dashboard](	
https://s3.amazonaws.com/ciims-s3-us-01/949A73802B6E17CD3FA04B8FA14E76AF7EDF51736A3904E2EF7358049C51D8D2.png)

__Easily browser all content entries__

![Content List View](	
https://s3.amazonaws.com/ciims-s3-us-01/6F679654A4F7F3B9A3DD72DA32540B2CD12843C7DACB88A257372386C7325A80.png)

__Powerful content editor__

![CiiMS Editor](	
https://s3.amazonaws.com/ciims-s3-us-01/BBAD9AA513B05052FF83DC8B4F4E22CD9AFC1A5701EB33E60CA7C4A0DD32C04A.png)

## Demo
A demo of CiiMS can be found at:

    Site: http://demo.ciims.io
    Admin Panel: http://demo.ciims.io/dashboard
    
You may use the following credentials to login and manage the site.

    Email: demo@ciims.io
    Pasword: password

Please note that this demo is not monitored, and is reset at an unspecified interval and at my discretion. Please be nice. If you find a bug please report it via a [Github Issue](https://github.com/charlesportwoodii/CiiMS/issues).

## Documentation

Full documents for CiiMS can be found at [docs.ciims.io](https://docs.ciims.io). Please refer to this guide for installation instructions, development guidelines, and a list of available CLI commands

## Features

* Based on Yii Framework
* Installs in 30 seconds*
* Utilizes Composer
* Beautiful Default Theme
* Supports Markdown Extra
* SEO Optimized (Sitemap XML, URL Slugs, SEO Meta Tags)
* Password Protected Content
* Site wide and Category Specific RSS Feeds
* Multiple Content Type Support
* Interchangeable Caching Systems (Redis, APC, Memcache, Files)
* Low Memory Footprint
* Themable
* Social Integration (Social Signon, Social Sharing)
* Extendable with custom modules, extensions, themes, and cards
* Beautiful _and_ functional dashboard for managing your content and settings.
* i18n files provided for translations
* Fully Customizable
* Easily install new themes and cards from in a single click
* _And a bunch of other things!_

```
* Installation time is based upon [TravisCI](https://travis-ci.org/charlesportwoodii/CiiMS) builds
```

## Requirements

Before installing CiiMS you'll need to have at _minimum_ the following:

* PHP 5.5+
* [Yii1 Requirements](http://www.yiiframework.com/doc/guide/1.1/en/quickstart.installation#requirements)
* PHP CURL Extension
* PHP ZIP Extension
* PHP MCrypt Extension

All the dependencies and other requirements are managed through composer, and will be reported back to you when you run ```composer install```.

## Recommendations
The following extensions/applications are recommended to improve performance.

* [phpredis](https://github.com/nicolasff/phpredis) + [Redis](redis.io)
* [memcached](http://www.php.net//manual/en/book.memcached.php)
* [Zend Opcache](http://www.php.net//manual/en/book.opcache.php)

------------------

## Support
If you require support, submit a Github issue and I'll look into it as soon as I can. 

## How Can I Contribute?

We <3 Contributers!

* Submit a detailed bug report
* Implement a new feature
* Fix a bug
* Write additional unit & functional tests
* Translate CiiMS into your favorite/local language
* Create dashboard cards
* Create themes

# License

[MIT LICENSE](http://opensource.org/licenses/MIT)
Copyright (c) 2011-2015 Charles R. Portwood II

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
