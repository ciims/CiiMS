## CiiMS
[![TravisCI](https://api.travis-ci.org/charlesportwoodii/CiiMS.png?branch=master,develop, "TravisCI")](https://travis-ci.org/charlesportwoodii/CiiMS)
[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/charlesportwoodii/CiiMS/trend.png)](# "Bitdeli Badge")

#### What is CiiMS?

CiiMS is a high performance CMS blogging platform designed to be easy to use, fun to work with, and easy to develop with. CiiMS is fast, powerful, extendable, and flexible, and is optimized to run with a combination of tools such as Memcache, Redis, APC, and Sphinx - but can run in other configurations. The _intent_ is to have a easy to use CMS platform that runs on Yii that is _fast_, _user friendly_, _easy to use_, _effecient_ and _not resource intensive_.

#### Upgrading CiiMS?
#### The One Rule/Suggestion/Request
Yup, I'm stealing this idea from [Syte](https://github.com/rigoneri/syte) because I think it is awesome. If you use and love CiiMS create a pull request that modifies this readme and adds a 60x60 avatar image as a link to your site. If you want to a border color that's fine too.

[![Erianna by Charles R. Portwood II](https://secure.gravatar.com/avatar/7ea3ae65556979b64ba8cde5cd51c667?s=60, "Erianna by Charles R. Portwood II")](http://www.erianna.com)

#### Just Show Me the Demo
Sure thing boss: A demo of CiiMS can be found at:

    Site: http://ciims.erianna.com
    Admin Panel: http://ciims.erianna.com/admin
    
You may use the following credentials to login and manage the site.

    Email: admin@erianna.com
    Pasword: admin

Please note that this demo is not monitored, and is reset at an unspecified interval and at my discretion. Please be nice. If you find a bug please report it via a [Github Issue](https://github.com/charlesportwoodii/CiiMS/issues).

#### Features

* Based on Yii Framework
* Installs in under 5 Minutes (Will Install Yii for you too!)
* Beautiful Default Theme
* Based in [Twitter Boostrap](http://yii-booster.clevertech.biz/) For Easy Development
* Content Support for both [Markdown Extra](http://daringfireball.net/projects/markdown/) _and_ [Imperavi Redactor](http://imperavi.com/redactor/) (via Yii License)
* SEO Optimized (Sitemap XML, URL Slugs, SEO Meta Tags)
* Password Protected Content
* Sitewide and Category Specific RSS Feeds
* Multiple Content Type Support
* Interchangable Caching Systems (Redis, APC, Memcache, Files)
* Low Memory Footprint
* Themable
* Social Integration (Social Signon, Social Sharing)
* Extendable with custom modules/extensions
* _And a bunch of other things!_

------------------

#### Requirements

* Yii Framework 1.1+ (Consequently Basic Yii Requirements) (The installer and download and install Yii for you automatically if you don't have it installed already).
* PHP 5.1+ (Strong recommend 5.3+)
* MySQL 5.5+ (CiiMS can run on Postgres/SQLite, but you have to do a manual installation which isn't covered in this readme.
* __mcrypt/crypt__ library. This is required for bcrpt hashing.

#### Recomendations
The following extensions/applications are recommended to improve performance.

* Memcache/Redis
* APC Cache/ZendOpcache+
* Sphinx Search Server

------------------

#### Setup Notes and Installation
CiiMS comes with a built in installer which will walk you through the setup process and provide you with information in the event it can't do something. The installer should be fairly straightforward. If you run into issues during the installation, it's most likely a permission issues with /assets, /protected/runtime, or /protected/config. The installer has built in error support, and by default will recommend you make a few directories writable. Any others are most likely a _setup_ issue rather than an issue with the installer.

#### Support
If you require support at any time, submit a Github issue and I'll look into it as soon as I can. 

#### Project Management.
I use a combination of tools to manage this project. I'll track most bugs through Github Issues. New features and testing require a some more testing, so I've been using [Trello](https://trello.com/board/ciims/501db2c1467ee918041c158e) to manage new items.
Please only use Trello if you intend to participate in making CiiMS better I'd prefer to leave it open. If Trello starts to get abused, I'll lock it down.

#### Extensions & Options
CiiMS uses a key => value options system which is managed through the _settings_ tab of the admin panel. A details list of all available options is available at [OPTIONS.md](OPTIONS.md).

#### What Still Needs to be Done?
The current roadmap will be added to this soon. For now:

* Unit & Functional Testing (We're currently hooked into TravisCI, but we don't have a lot of tests running at the moment)
* i18n Language Support (PHP Support. [see #5](https://github.com/charlesportwoodii/CiiMS/issues/5)
* Make the Dashboard Awesome

#### License

[MIT LICENSE](http://opensource.org/licenses/MIT)
Copyright (c) 2012 Charles R. Portwood II

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

###### TL;DR
Free to use, modify, and do whatever the heck you want it so long as maintain this notice and don't sue me. (Though if you make a lot of money off of it, _at least_ let me know.) =)
