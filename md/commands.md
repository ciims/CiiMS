# CiiMS Console Commands

To assist with development and basic site issues, CiiMS offers several basic commands. In general, the migrations can be called as follows:

```
$ cd /path/to/ciims
$ php index.php <command-name> [parameters...]
```

A list of available commands can be run by running the ```index.php``` command without any arguments
```
Yii command runner (based on Yii v1.1.15)
Usage: index.php <command-name> [parameters...]

The following commands are available:
 - ciicache
 - ciiclassmap
 - ciimessage
 - migrate
 - installer
```

## CLI Installation

In order to facilitate headless installations, the CiiMS installer comes with a CLI based installer which can be run via the ```yiic.php``` command. To install CiiMS from the CLI, complete steps 1-3 in the [Installation Guide](/installation.html#installing-ciims-cli-installation), then run the following console commands, replacing each value for the appropriate value:

```
cd /path/to/ciims
php index.php installer index --dbHost=value --dbName=value --dbUsername=value --dbPassword=value --adminEmail=value --adminPassword=value --adminUsername=value --siteName=value
```

## Migrations

CiiMS depends on the basic Yii migration script to apply new database migrations. The migration command can be run as follows:

```
cd /path/to/ciims
php index.php migrate up
```

## Cleaing Cache

CiiMS maintains an internal cache to optmize performance. If something doesn't appear correctly, you can either wait for the TTL on the cache entry to expire, or manually expire all cache entries for CiiMS as follows:

```
cd /path/to/ciims
php index.php ciicache up
```

Note, that purging the cache for CiiMS will result in a loss of performance until the cache data is rebuilt.

## Yii ClassMap

To optimize performance, CiiMS can optimize it's auto-loader to improve performance. By default, CiiMS comes with a pre-optimized class map file. During development however, you may need to re-generate this file so that new classes can be applied. This can be done as follows:

```
cd /path/to/ciims
php index.php ciiclassmap
```

## Generate Message/Translation Files

CiiMS has built in support for i18n translation files. When developing a new theme, or adding new translatable materials to CiiMS you'll need to update the translation files. The translation can be updated in three different ways:

### Update Base Translations

Running the CiiMessage command without any arguments will update all base translations (excluding items in the modules and themes directories)

```
cd /path/to/ciims
php index.php ciimessage
```

### Update Module Translations

To update the translation files for a specific module, run the following command:

```
cd /path/to/ciims
php index.php ciimessage modules [module-name]
```

### Update Theme Translations

To update the translation files for a specific theme, run the following command:
```
cd /path/to/ciims
php index.php ciimessage themes [theme-name]
```