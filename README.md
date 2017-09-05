# CiiMS
[![Latest Version](https://img.shields.io/packagist/v/charlesportwoodii/CiiMS.svg?style=flat-square)]()
[![TravisCI](https://img.shields.io/travis/charlesportwoodii/CiiMS.svg?style=flat-square "TravisCI")](https://travis-ci.org/charlesportwoodii/CiiMS)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/charlesportwoodii/ciims.svg?style=flat-square)](https://scrutinizer-ci.com/g/charlesportwoodii/CiiMS/)
[![Downloads](https://img.shields.io/packagist/dt/charlesportwoodii/ciims.svg?style=flat-square)](https://packagist.org/packages/charlesportwoodii/ciims)
[![Gittip](https://img.shields.io/gittip/charlesportwoodii.svg?style=flat-square "Gittip")](https://www.gittip.com/charlesportwoodii/)
[![License](https://img.shields.io/github/license/charlesportwoodii/CiiMS.svg?style=flat-square)](https://github.com/charlesportwoodii/CiiMS/blob/master/LICENSE.md)
[![Yii](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat-square)](http://www.yiiframework.com/)

![CiiMS Logo Banner](	
https://s3.amazonaws.com/ciims-s3-us-01/63E6ADC1A1BF41ABDE55B4BA810F187DBF1C9E696A90713D8A1F38B69E1071CE.png)

CiiMS is a fast, simple, and easy to use, full feature blogging platform and content management system built in Yii. CiiMS is, and will always be free to use and open-source.

## News

__CiiMS 2.0.2 Released__

CiiMS 2.0.2 is now available. Checkout the [release notes](https://github.com/charlesportwoodii/CiiMS/releases/tag/2.0.2) for more information.

## Installation

CiiMS can be quickly installed through composer. For more information about installing CiiMS, checkout the [installation guide](https://docs.ciims.io/installation.html).

```
composer create-project --prefer-dist --stability=dev charlesportwoodii/ciims {path} 2.0.2
cd {path}
composer dump-autoload -o
```

## Benchmarks

How fast is CiiMS? In a post-installation comparison, using out of the box (non-debug) configurations over SSL, CiiMS outperforms Ghost, Wordpress, Bolt, and OctoberCMS in a 100 user, 10 minute siege test. CiiMS s nearly 30% faster than Ghost, and over 130% faster than Wordpress.

```
- DigitalOcean 512MB Box
- Ubuntu 14.04 x64 LTS
- Percona Server 5.6
- PHP 5.6.6
	- Zend Opcache 	
- Nginx 1.7.9
- Node 0.10.36
```

![Performance Comparison](https://s3.amazonaws.com/ciims-s3-us-01/ZSTDTTNMSIDLDFDAQMNITKMRKSIWMSHNWUQRSJZKWREYHXODVYEWLYGNRIBWTLQX+.png)

## Screenshots

__A beautiful default theme__

![Default Theme](	
https://s3-us-west-2.amazonaws.com/cdn.ciims.io/5A2ED631D493E053774C31C513F2C60FF5208B1B3AB8193D7D8D351251E2207D.png)

__A powerful dashboard with custom JavaScript cards__

![CiiMS Dashboard](	
https://s3-us-west-2.amazonaws.com/cdn.ciims.io/949A73802B6E17CD3FA04B8FA14E76AF7EDF51736A3904E2EF7358049C51D8D2.png)

__Easily browse all content entries__

![Content List View](	
https://s3-us-west-2.amazonaws.com/cdn.ciims.io/6F679654A4F7F3B9A3DD72DA32540B2CD12843C7DACB88A257372386C7325A80.png)

__Powerful content editor__

![CiiMS Editor](	
https://s3-us-west-2.amazonaws.com/cdn.ciims.io/BBAD9AA513B05052FF83DC8B4F4E22CD9AFC1A5701EB33E60CA7C4A0DD32C04A.png)

## Documentation

Full documents for CiiMS can be found at [docs.ciims.io](https://docs.ciims.io). Please refer to this guide for installation instructions, development guidelines, and a list of available CLI commands

## Features

* Based on Yii Framework
* Installs in 30 seconds
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

## Requirements

As modern blogging platform meant for the next 10 years of writing, CiiMS' requirements are high. To ensure you have all the necessary requirements, CiiMS has a dedicated requirements checker that will run after installing the necessary composer dependencies and will notify you of any missing dependencies.

The requirements checker can be run manually by running: ```php ./vendor/ciims/requirements/index.php```

### Recommendations

The following extensions/applications are recommended to improve performance.

* [phpredis](https://github.com/nicolasff/phpredis) + [Redis](redis.io)
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

BSD-3--Clause. See [LICENSE.md](LICENSE.md) for more details.
