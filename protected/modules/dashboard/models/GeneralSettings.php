<?php

Yii::import('application.modules.dashboard.components.CiiSettingsModel');
class GeneralSettings extends CiiSettingsModel
{
	protected $name;

	protected $dateFormat;

	protected $timeFormat;

	protected $timezone;

	protected $defaultLanguage = 'en_US';

	protected $url;

	protected $subdomain;

	protected $menu = 'admin|blog';

	protected $offline = false;

	protected $preferMarkdown = true;

	protected $bcrypt_cost = 13;

	protected $searchPaginationSize = 10;

	protected $categoryPaginationSize = 10;

	protected $contentPaginationSize = 10;

	public function rules()
	{
		return array(
		);
	}

	
	/**
	 * Attribute labels
	 * @return array
	 */
	public function attributeLabels()
	{
		return array(
			'name' => 'Site Name',
			'dateFormat' => 'Date Format',
			'timeFormat' => 'Time Format',
			'timezone' => 'Timezone',
			'defaultLanguage' => 'Default Language',
			'url' => 'Site URL',
			'subdomain' => 'Subdomain',
			'menu' => 'Menu Navigation',
			'offline' => 'Offline Mode',
			'preferMarkdown' => 'Editor Preferences',
			'splashLogo' => 'Splash Image',
			'bcrypt_cost' => 'Password Strength Settings',
			'searchPaginationSize' => 'Search Post Count',
			'categoryPaginationSize' => 'Category Post Count',
			'contentPaginationSize' => 'Content Post Cost'
		);
	}
}