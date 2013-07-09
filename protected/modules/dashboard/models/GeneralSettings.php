<?php

class GeneralSettings extends CFormModel
{
	public $name;

	public $dateFormat;

	public $timeFormat;

	public $timezone;

	public $defaultLanguage;

	public $url;

	public $subdomain;

	public $menu;

	public $offline;

	public $preferMarkdown;

	public $splashLogo;

	public $bcrypt_cost;

	public $searchPaginationSize;

	public $categoryPaginationSize;

	public $contentPaginationSize;

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

	public function save()
	{
		if (!$this->validate())
			return false;
	}
}