# 1.10.0 (unreleased)

###### BREAKING CHANGES:
	
- There is no way to upgrade currently installed cards.
- Cards and Themes can no longer be installed from Github
- Functionality previous stored in ```Theme.json``` has been moved into ```Cii```. Current themes may break if functionality is overloaded by existing theme file. New themes now only require the following base structure to function
```
class Theme extends CiiThemesModel
{
    public  $theme = '<theme_name>';
}
```
- ```CommentController``` and views have been removed in favor of using the Comment API

###### BUG FIXES:

- #106 Installer now permits empty passwords
- #105 Publishing time is now properly offset by UTC in the Dashboard
- Theme page will appropriately show an error if the theme has no configurable options
- Dashboard redirects to login page on timeout rather than throwing a render error
- CiiTimezoneFixCommand added to correct Timezone bugs introduced in 1.9.0. This command only needs to be run if you're experience time related issues.

###### IMPROVEMENTS:

- Migrations automatically register instance with ciims.org
- Cards now use uuid from ciims.org rather than generating their own
- New Default Theme
- Installed has been streamlined and rethemed
- Comment API is now always enabled
- Event API is now always enabled
- Event API pre-registers itself for loading
- Disqus automatically injects itself on the appropriate pages when enabled. Custom configuration in themes is no longer required
- AddThis automatically injects itself on the appropriate pages when enabled. Manual configuration is themes is no longer required
- CA Certs have been added to ensure SSL validation on curl requests always succeeds
- Support for CiiParams override file for hosted and managed environments
- Added CHANGELOG.md
- CiiSetupCommand added support for headless/automated installs
- All Cii related functionality has been moved into extensions/cii

###### FEATURES:

- Cards can now be installed from ciims.org
- Themes can now be installed from ciims.org
- Cards and Themes can notify user when there is an update available for them, and can do ondemand in place self updates.

# Previous

The changelog began with version 1.10.0 so any changes prior to that can be seen by checking the tagged releases and reading git commit messages.