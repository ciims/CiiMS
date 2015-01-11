# Installing CiiMS

Installing CiiMS is a very straightforward and simple process and utilizes several tools to ensure that your environment meets all the requirements necessary for getting CiiMS up and running.

## Requirements

Before starting the installation, make sure your environment has the following installed.

- PHP 5.3.10+
- MySQL 5.5+
- mod_php or proxy capable webserver
- PHP CURL Extension
- PHP ZIP Extension
- PHP MCrypt Extension
- [All Yii Framework Requirements](http://www.yiiframework.com/doc/guide/1.1/en/quickstart.installation)
- [Composer](https://getcomposer.org/download/)

Once all the requirements are met, you can begin the installation process.

## Installation Guide

1 Git clone/Download the source code to your web root

```
git clone https://github.com/charlesportwoodii/CiiMS.git
git checkout 2.0.0-beta
```

2 Install composer dependencies (this may take some time to complete depending upon your internet connection)

```
cd /path/to/ciims
composer install
# composer dump-autoload --optimize # Be sure to optmize the autoloader if you're deploying CiiMS to a production environment
```

3 Verify that the following directories are writable by your web server user.

```
# eg chown -R www-data:www-data {dir}
#    chmod -R 754 {dir}
/assets
/protected/runtime
/protected/config
/uploads
```

4 Open up your web browser to the path where CiiMS is installed. Note, it's __HIGHLY__ recommended that CiiMS be installed either on the root domain or a subdomain (eg //blog.example.com, //example.com) rather than in a subfolder (eg //example.com/ciims). Most people have significantly _less_ issues with CiiMS when it is run this way.

5 Provide your MySQL database information to start the migrations.

<img src="/images/installation/001.PNG" class="img"/>

CiiMS will attempt to run the migrations for you automatically. If you have trouble running the migrations from the web installer, see the [manual migrations](/installation.html#installing-ciims-installation-notes-manual-migrations) section below.

<img src="/images/installation/002.PNG" class="img" />

6 Provide information about your site, and administrative user

<img src="/images/installation/003.PNG" class="img"/>

7 __Congratulations!__ CiiMS is now installed! At this point you can either click the "login" button, or refresh the page to head to the dashboard.

<img src="/images/installation/004.png" class="img"/>

## CLI Installation

In order to facilitate headless installations, the CiiMS installer comes with a CLI based installer which can be run via the ```yiic.php``` command. To install CiiMS from the CLI, complete steps 1-3 in the preceeding section, then run the following console commands, replacing each value for the appropriate value:

```
cd /path/to/ciims
php protected/yiic.php installer index --dbHost=value --dbName=value --dbUsername=value --dbPassword=value --adminEmail=value --adminPassword=value --adminUsername=value --siteName=value
```

## Installation Notes

### Web Server Configurations
CiiMS can use any basic Yii application web server configuration. For a list of examples configurations, take a look at [the offical Yii guide](http://www.yiiframework.com/doc/guide/1.1/en/quickstart.apache-nginx-config)

### Windows Environments

While CiiMS will run on a Windows environments, it's __highly__ recommended you run CiiMS on a Linux based server, such as Ubuntu 14.04 LTS, or CentOS. If you're trying CiiMS out, I'd recommend looking at (Vagrant)[https://www.vagrantup.com/] to use as a development environment.

# Upgrading

The following section contains information for upgrading CiiMS to the latest version.

## Basic Upgrading

In general - the following steps should be performed to upgrade from one version to another. Additional details will be provided if necessary.

```
git checkout {version}
git pull
composer install
php protected/yiic.php migrate up --interactive=0
rm -rf assets/*
rm -rf runtime/cache/*
rm -rf runtime/modules.config.php
php protected/yiic.php ciicache flush
```
## 1.x to 2.0.0

CiiMS 2.0 does not offer a direct upgrade path from the 1.x release branches. However it is possible to migrate your content and categories to a new instance using the following steps. Note that it is only possible to migrate your content and categories. Users cannot be migrated due to a change in the hashing algorithm used to secure their passwords.

1 Upgrade your existing CiiMS 1.x instance to 1.9

```
git checkout 1.9.1
git pull
php protected/yiic.php migrate up --interactive=0
rm -rf assets/*
rm -rf runtime/cache/*
rm -rf runtime/modules.config.php
php protected/yiic.php ciicache flush
```

2 Install CiiMS 2.0.0 as a separate instance using the guide above.
3 Run the following MySQL queries and export the data.

```
-- Categories
SELECT 
	id, 
	parent_id, 
	name, slug, 
	UNIX_TIMESTAMP(created) as created,
	UNIX_TIMESTAMP(updated) as updated 
FROM `categories`;

-- Content
SELECT
	id, 
	vid, 
	title, 
	content, 
	extract AS excerpt, 
	slug, 
	category_id,
	1 AS author_id,
	type_id,
	commentable,
	`password`,
	like_count,
	UNIX_TIMESTAMP(published) AS published,
	UNIX_TIMESTAMP(created) AS created,
	UNIX_TIMESTAMP(updated) AS updated FROM `content` 
WHERE 
	vid=(SELECT MAX(vid) FROM `content` AS t WHERE t.id=content.id)
```

4 Disable MySQL foreign key constraints and import the data into your new CiiMS instance

```
SET foreign_key_checks = 0;
-- Import Data here --
SET foreign_key_checks = 1;
```
