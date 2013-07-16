<?php

class GeneralSettings extends CiiSettingsModel
{
	protected $name = NULL;

	protected $dateFormat = 'F jS, Y';

	protected $timeFormat = 'H:i';

	protected $timezone = "UTC";

	protected $defaultLanguage = 'en_US';

	protected $url = NULL;

	protected $subdomain = NULL;

	protected $menu = 'admin|blog';

	protected $offline = 0;

	protected $preferMarkdown = 1;

	protected $bcrypt_cost = 13;

	protected $searchPaginationSize = 10;

	protected $categoryPaginationSize = 10;

	protected $contentPaginationSize = 10;

	protected $sphinx_enabled = false;

	protected $sphinxHost = 'localhost';

	protected $sphinxPort = 9312;

	protected $sphinxSource = NULL;

	public function groups()
	{
		return array(
			'Site Settings' => array('name', 'url', 'subdomain', 'menu', 'offline', 'preferMarkdown', 'bcrypt_cost', 'categoryPaginationSize','contentPaginationSize','searchPaginationSize'),
			'Display Settings' => array('dateFormat', 'timeFormat', 'timezone', 'defaultLanguage'),
			'Sphinx' => array('sphinx_enabled', 'sphinxHost', 'sphinxPort', 'sphinxSource')
		);
	}

	/**
	 * Validation rules
	 * @return array
	 */
	public function rules()
	{
		return array(
			array('name, dateFormat, timeFormat, timezone, defaultLanguage', 'required'),
			array('name, menu, subdomain', 'length', 'max' => 255),
			array('dateFormat, timeFormat, timezone, defaultLanguage', 'length', 'max' => 25),
			array('offline, preferMarkdown, sphinx_enabled', 'boolean'),
			array('sphinxHost, sphinxSource', 'length', 'max' => 255),
			array('sphinxPort', 'numerical', 'integerOnly' => true),
			array('bcrypt_cost', 'numerical', 'integerOnly'=>true, 'min' => 13, 'max' => 50),
			array('searchPaginationSize, categoryPaginationSize, contentPaginationSize', 'numerical', 'integerOnly' => true, 'min' => 1, 'max' => 100),
			array('url', 'url')
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
			'menu' => 'Menu Navigation',
			'offline' => 'Offline Mode',
			'preferMarkdown' => 'Use Markdown',
			'bcrypt_cost' => 'Password Strength Settings',
			'searchPaginationSize' => 'Search Post Count',
			'categoryPaginationSize' => 'Category Post Count',
			'contentPaginationSize' => 'Content Post Cost',
			'sphinx_enabled' => 'Enable Sphinx Search',
			'sphinxHost' => 'Sphinx Hostname',
			'sphinxPort' => 'Sphinx Port',
			'sphinxSource' => 'Sphinx Source Name'
		);
	}
}