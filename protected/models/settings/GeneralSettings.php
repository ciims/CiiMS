<?php

class GeneralSettings extends CiiSettingsModel
{
	protected $name = NULL;

	protected $dateFormat = 'F jS, Y';

	protected $timeFormat = 'H:i';

	protected $defaultLanguage = 'en_US';

	protected $forceSecureSSL = false;

	protected $bcrypt_cost = 13;

	protected $searchPaginationSize = 10;

	protected $categoryPaginationSize = 10;

	protected $contentPaginationSize = 10;

	protected $useDisqusComments = 0;

	protected $disqus_shortname = NULL;

	//protected $useDiscourseComments = 0;

	//protected $discourseUrl = '';

	public function groups()
	{
		return array(
			Yii::t('ciims.models.general', 'Site Settings') => array('name', 'forceSecureSSL', 'bcrypt_cost', 'categoryPaginationSize','contentPaginationSize','searchPaginationSize'),
			Yii::t('ciims.models.general', 'Disqus') => array('useDisqusComments', 'disqus_shortname'),
			//Yii::t('ciims.models.general', 'Discourse') => array('useDiscourseComments', 'discourseUrl'),
			Yii::t('ciims.models.general', 'Display Settings') => array('dateFormat', 'timeFormat', 'defaultLanguage'),
		);
	}

	/**
	 * Validation rules
	 * @return array
	 */
	public function rules()
	{
		return array(
			array('name, dateFormat, timeFormat, defaultLanguage', 'required'),
			array('name', 'length', 'max' => 255),
			array('dateFormat, timeFormat, defaultLanguage', 'length', 'max' => 25),
			array('useDisqusComments, forceSecureSSL', 'boolean'),
			array('disqus_shortname', 'length', 'max' => 255),
			array('bcrypt_cost', 'numerical', 'integerOnly'=>true, 'min' => 13),
			array('searchPaginationSize, categoryPaginationSize, contentPaginationSize', 'numerical', 'integerOnly' => true, 'min' => 10),
			//array('url', 'url')
		);
	}

	/**
	 * Attribute labels
	 * @return array
	 */
	public function attributeLabels()
	{
		return array(
			'name' => Yii::t('ciims.models.general', 'Site Name'),
			'dateFormat' => Yii::t('ciims.models.general', 'Date Format'),
			'timeFormat' => Yii::t('ciims.models.general', 'Time Format'),
			'defaultLanguage' => Yii::t('ciims.models.general', 'Default Language'),
			'forceSecureSSL' => Yii::t('ciims.models.general', 'Force SSL for Secure Areas'),
			'bcrypt_cost' => Yii::t('ciims.models.general', 'Password Strength Settings'),
			'searchPaginationSize' => Yii::t('ciims.models.general', 'Search Post Count'),
			'categoryPaginationSize' => Yii::t('ciims.models.general', 'Category Post Count'),
			'contentPaginationSize' => Yii::t('ciims.models.general', 'Content Post Cost'),

			// Discourse
			'useDisqusComments'    => Yii::t('ciims.models.general', 'Use Disqus Comments'),
			'disqus_shortname'     => Yii::t('ciims.models.general', 'Disqus Shortcode'),

			// Discourse
			//'useDiscourseComments' => Yii::t('ciims.models.general', 'Use Discourse Comments'),
			//'discourseUrl' => Yii::t('ciims.models.general', 'Discourse URL'),
		);
	}
}
