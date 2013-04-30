## Security Considerations

The following are recommendations for improving the security of CiiMS from the default installation. Some directories and files should be modified either at the Apache/Nginx server level, or by modifying the permissions or deleting the files/directories. It is recommended that you follow this guide if you use CiiMS in a production environment.

#### Directory Permissions

After completing the installation, it is recommended that you change the permissions on the following directories to a non world-readable level

~~~
	protected/runtime
	protected/config/main.php
	protected/assets
~~~

Yii will still need to be able to write to these directories to work properly however. Permissions such as 754, and 644 are more secure than the defualts of 777 which you used for the installation.

#### Removing Installer Script And Special Directories
In _theory_, the installer should disable itself after completing the installation. However since it does interact directly with the database, I recomend that you remove the following files from your production environment, either by physically deleting them, or by blocking them from your webserver

~~~
	index-test.php
	install.php
	OPTIONS.md
	README.md
	UPGRADING.md
	SECURITY.md
	.travis.yml
	Capfile
	protected/config/deploy.rb
	protected/config/deploy/*
~~~

Since git is used for upgrading, I recommend you block that folder from your web server rather than deleting it.