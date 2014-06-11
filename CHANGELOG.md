# 2.0.0 (unreleased)

###### BREAKING CHANGES:
	
- There is no way to upgrade currently installed cards
- Cards and Themes can no longer be installed from Github
- Functionality previous stored in ```Theme.php``` has been moved into ```CiiThemesModel.php```. Current themes may break if functionality is overloaded by existing theme file. New themes now only require the following base structure to load themselves.
	```
	class Theme extends CiiThemesModel
	{
	    public  $theme = '<theme_name>';
	}
	```
- ```CommentController``` and views have been removed in favor of using the Comment API
- Reliance on Composer means that you must run the ```php composer.phar install --no-dev``` on first run. To avoid downtime in upgrading, generate the composer dependencies first and copy them into the ```vendor``` directory perform checkout out ```1.10.0```

###### BUG FIXES:

- #106 Installer now permits empty passwords
- #105 Publishing time is now properly offset by UTC in the Dashboard
- Theme page will appropriately show an error if the theme has no configurable options
- Dashboard redirects to login page on session timeout rather than throwing a render error
- CiiTimezoneFixCommand added to correct Timezone bugs introduced in 1.9.0. This command only needs to be run if you're experience timezone related issues.
- Javascript errors in Dashboard (GH #110)
- CSS Assets with data:application/mime-type CSS URL rules triggered fatal error when loaded with YII_DEBUG=false (GH #128)

###### IMPROVEMENTS:

- Added CHANGELOG.md
- Switched to [semantic versioning](http://semver.org/)
- New Default Theme
- Installed has been streamlined and rethemed
- Comment API is now always enabled if not using a secondary comment provider
- Event API is now always enabled
- Event API pre-registers itself for loading
- Disqus automatically injects itself on the appropriate pages when enabled. Custom configuration in themes is no longer required
- AddThis automatically injects itself on the appropriate pages when enabled. Manual configuration is themes is no longer required
- CA Certs have been added to ensure SSL validation on curl requests always succeeds
- Support for CiiParams override file for hosted and managed environments
- CiiSetupCommand added support for headless/automated installs
- All Cii related functionality has been moved into extensions/cii
- CiiMS now ships with less code due to composer supported added in GH #111
- Composer will notify you if you have insufficient requirements to run CiiMS on first install
- FontAwesome version update
- CiiMS can now run with Yii's internal routing disable for 100% client side themes.
- Modules and Themes now utilize npm, grunt, and bower for asset and dependency management
- Refactoring for all modules

###### FEATURES:

- Cards and Themes can notify user when there is an update available for them, and can do ondemand in place self updates.
- Cards are now 100% self contained javascript/html applications. PHP is no longer required.
- Added Composer Support (GH #111)
- Modules now are self loading, and no longer require configuration changes to load (GH #114)

# Previous

The changelog began with version 1.10.0 so any changes prior to that can be seen by checking the tagged releases and reading git commit messages.