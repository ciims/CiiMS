# Themes

CiiMS supports custom and dynamic themes to change the appearance of your site. This section will cover how to install and develop new themes for CiiMS.

## Managing Themes

All installed themes for your CiiMS instance will appear in the appearance section of the settings pane (/dashboard/settings/appearance). 

<img src="/images/themes/002.PNG" class="img" />

From this view your can see all installed themes with numbered versions, details about the theme, the active theme, and any themes needing to be updated.

## Updating Themes 

Your CiiMS instance actively monitors all themes and regularly checks if an update is made available for that theme (when possible). Any themes installed through the dashboard will support this feature. To update a theme installed through the CiiMS dashboard, simply click on the "Update Available" button next to the theme you want to update. CiiMS will automatically update the theme.

<img src="/images/themes/003.PNG" class="img" />

## Installing Themes

Themes can be installed to CiiMS through one of two different ways, through the dashboard of your CiiMS instance or through composer.

### Dashboard Install

To install a new theme through your dashboard, navigate the appearance section of your dashboard (the "eye" icon in the settings pane, eg https://www.example.com/dashboard/settings/appearance) and click on the "plus" button in the upper right corner of the pane. From this window you can view all available themes that can be installed via themes.ciims.io.

<img src="/images/themes/001.PNG" class="img" />

To install a theme from this window, simply select the theme you want to install and press the "Install" button in the upper right corner. CiiMS will automatically download and install the new theme. To active the newly installed theme, simply reload the page and activate the theme.

### Composer Install

Alternatively, you can install any composer theme via the command line from the root of your CiiMS instance. Simply open up your command line interface, and run the following command:

```
composer install [namespace/theme] <version>
```

Be sure to substitute _namespace_, _theme_, and _version_ with their appropriate values. When installed this way, composer will automatically download any dependencies for the theme.

## Developing Themes

Developing themes for CiiMS is easy and straightfoward, and builds upon the existing themeing system used in Yii Framework. For a detailed example of what a theme looks like, check out either the [default theme](https://github.com/charlesportwoodii/ciims-themes-default) or the [spectre theme](https://github.com/charlesportwoodii/ciims-themes-spectre).

### Theme Basics

A CiiMS theme can be broken down to the following basic folder structure. Note that all files listed are required

```
assets/
messages/
views/
	categories/
		index.php
	content/
		password.php
		blog.php
	email/
		email-change.php
		forgot.php
		invite.php
		passwordchange.php
		register.php
	site/
		acceptinvite.php
		activation.php
		emailchange.php
		error.php
		forgot.php
		invitesuccess.php
		login.php
		register.php
		resetpassword.php
		search.php
	layouts/
		main.php
		password.php
		default.php
		blog.php
	profile/
		index.php
		edit.php
Theme.php
Readme.md
Default.png
composer.json
composer.lock
```

### Theme.php

```Theme.php``` serves as the bootstrap file for your Theme, and is used to integrate your theme with CiiMS. A simplified ```Theme.php``` requires at minimum the following:

```
<?php
// Load the local autoloder if it exists
if (file_exists(__DIR__.DS.'vendor'.DS.'autoload.php'))
	require __DIR__.DS.'vendor'.DS.'autoload.php';
// Import CiiSettingsModel
Yii::import('application.modules.dashboard.components.CiiSettingModel');
class Theme extends CiiThemesModel
{
	/**
     * @var string  The theme name
     */
	public $theme = '<theme_name>';
}
// ?>
```

Where the public ```$theme``` should be the name of the theme, matching the containing folder.

#### Extending Themes

Themes can be extended in several ways, and support both callbacks and custom variables..

##### Disabling Rendering

CiiMS' default behavior is to handle it's own rendering. However CiiMS can be put into an _API only_ mode in which only the dashboard and the API modules are made available. This can be used if you want to create a 100% client side only theme.

To put your theme in this state, set the following in your ```Theme.php``` file

```
    public $noRouting = false;
```

##### Properties

CiiMS themes support dynamically updatable properties that can be edited from the themes section of the dashboard. To create and editable property, declare a protected property in the theme.

```
	protected $val;
```

From the ```Theme.php``` file, these properties can be retrieved with their dynamic values by polling

```
	$value = $this->{property_name} // $this->val using the preceeding example
```

##### Provided Methods

CiiMS comes with several methods by default, and supports several default Yii methods.

The following methods are provided by the parent class for ease of use, and should be self explainatory.

```
getCategories()
getRecentPosts()
getRelatedPosts($id, $category_id)
getPostsByAuthor($author_id)
getContentTags($content_id)
```

These methods, and all other methods can be fetched in your views by calling:

```
	$this->theme->{method}($params);
```

CiiMS also supports several Yii methods which can be used to handle validation and display of your custom attributes.

__Validation Rules__

CiiMS supports basic Yii validation rules to ensure your data is inserted into the database properly. For more information on what validation rules are available, see [the following wiki article](http://www.yiiframework.com/wiki/56/).

```
public function rules()
{
	return array(
		array('property1, property2', 'validator_type', 'params'=>'options')		
	);
}
```

__Groups__

Properties can be grouped and displayed in customizable groupings in the dashboard by declaring a ```groups``` method. Note that if this method is populated, only properties listed in the group will be displayed.

```
public function groups()
{
	return array(
		Yii::t('<NAME>Theme.main', 'Group Name') => array('property1', 'property2'),
	);
}

```

__Attribute Labels__

CiiMS supports custom attribute labels for each declared property, which can be defined by using the standard Yii ```attributeLabels()``` method.

```
public function attributeLabels()
{
	return array(
		'property1'        => Yii::t('<NAME>Theme.main', 'Property 1 Display Name'),
		'property2'        => Yii::t('<NAME>Theme.main', 'Property 2 Display Name')
	);
}
```

##### Callbacks

All other custom methods defined in your ```Theme.php``` file will be available as either $_GET or $_POST callbacks. Callbacks can be made to the API via the following GET/POST request.

```
GET /api/themes/theme/<theme_name>/method/<method_name>?param1=this&param2=that
```

Or via POST

```
POST /api/themes/theme/<theme_name>/method/<method_name>
{
	"param1": "this",
	"param2": "that"
}
```

Your methods should at minimum have the following signature, and should always return their results for the API to handle and parse.
```
public function <methodName>($data)
{
	return $data;
}
```

### Theme Translation Files

Themes support custom translation files for i18n. Any string you want to be translated should be wrapped in the following method:

```
Yii::t('<THEMENAME>Theme.<class>', 'string');
```

Where __THEMENAME__ should correspond to the name of your theme as defined in your ```Theme.php``` file, which should match the parent folder of the theme. For example, if the theme was named ```MyTheme```, the callback would look as such

```
Yii::t('MyThemeTheme.<class>', 'string');
```

The _<class>_ attribute can be whatever you want it to be. For simplicitly it should be the same throughout all of your view files.

Translation/message files can be generated by running the appropriate message class, which can be found in the [commands](/commands.html#ciims-console-commands-generate-messagetranslation-files-update-theme-translations) section.

### Composer Support

All themes created for CiiMS should have at composer.json file, and at minimum should have the following information:

```
{
    "name": "ciims-themes/<theme_name_as_matches_Theme.php_$theme>",
    "description": "<DESCRIPTION>",
    "license": "<LICENES>",
    "type" : "drupal-theme",
}
```

Until support for CiiMS is built into ```composer-installers```, the type of any theme should be set to ```drupal-theme``` to ensure that CiiMS thems are installed to the correct folder.

> __WARNING__: Themes installed through the dashboard presently do not have support for composer dependencies. Any themes published to themes.ciims.io must either pre-bundle their composer dependencies, or not have any composer dependencies to be published to the dashboard.

### Publishing Themes

If you want to make your theme available for installation via the CiiMS dashboard:

1. Tag and version your theme and publish to Github
2. Submit your theme to packagist so that it can be found.
3. Submit a pull request to https://github.com/ciims/themes and modify the ```index.json``` file with the following information:

```
"<ThemeNane>": {
    "name": "<composer.json_name>",
    "version": "<tagged_version>",
    "repository": "https://github.com/repository/name"
},
```

As an example, the default themes entry looks as follows:

```
"Default": {
    "name": "ciims-themes/default",
    "version": "3.0.8",
    "repository": "https://github.com/charlesportwoodii/ciims-themes-default"
},
```

Your theme will be reviewed by the team at CiiMS.io, and once accepted your pull request will be merged in. If we feel you need to modify you theme, we'll provide the necessary information in the pull request for you to fix before accepting the pull request.

> __NOTE:__ themes.ciims.io will only fetch your theme data once. If you make an update or change to your theme, you __MUST__ resubmit a new pull request which updates the version.

#### Updating Themes

If you make an update to a theme that you want to see be made available on themes.ciims.io, follow the same steps in the publishing themes section, and submit a pull request which updates the version of your theme.

# Installation/Update Issues

If you encounter issues installing themes from the dashboard, ensure that your ```/themes``` directory is writable by your web user, and that all basic CiiMS requirements are met.