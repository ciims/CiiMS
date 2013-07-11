<?php

Yii::import('application.modules.dashboard.components.CiiSettingsModel');
class GeneralSettings extends CiiSettingsModel
{
	protected $name = NULL;

	protected $dateFormat = 'F jS, Y';

	protected $timeFormat = 'H:i';

	protected $timezone = "UTC";

	protected $defaultLanguage = 'en_US';

	protected $url = NULL;

	//protected $subdomain = NULL;

	protected $menu = 'admin|blog';

	protected $offline = 0;

	protected $preferMarkdown = 1;

	protected $bcrypt_cost = 13;

	protected $searchPaginationSize = 10;

	protected $categoryPaginationSize = 10;

	protected $contentPaginationSize = 10;

	/**
	 * Overload the getter so we can the site name from Yii::app()
	 * @see CiiSettingsModel::__get($name);
	 * @return  bool
	 */
	public function __get($name)
	{
		$ret = NULL;
		if ($name == 'name')
			 $ret = $this->getName();

		if ($ret !== NULL)
			return $ret;

		return parent::__get($name);

	}

	/**
	 * Get the site name if it isn't set
	 * @return mixed
	 */
	public function getName()
	{
		if ($this->name === NULL && !isset($this->attributes['name']))
			return Yii::app()->name;

		return NULL;
	}

	public function rules()
	{
		return array(
			array('name, dateFormat, timeFormat, timezone, defaultLanguage', 'required'),
			array('name, dateFormat, timeFormat, timezone, defaultLanguage, menu, subdomain', 'length', 'max' => 255),
			array('offline, preferMarkdown', 'boolean'),
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
			'contentPaginationSize' => 'Content Post Cost'
		);
	}
}