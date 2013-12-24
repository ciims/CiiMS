<?php

class GeneralSettings extends CiiSettingsModel
{
	protected $name = NULL;

	protected $dateFormat = 'F jS, Y';

	protected $timeFormat = 'H:i';

	protected $defaultLanguage = 'en_US';

	protected $enableAPI = false;

	protected $offline = 0;

	protected $bcrypt_cost = 13;

	protected $searchPaginationSize = 10;

	protected $categoryPaginationSize = 10;

	protected $contentPaginationSize = 10;

	protected $autoApproveComments = 1;

	protected $notifyAuthorOnComment = 1;

	protected $useDisqusComments = 0;

	protected $disqus_shortname = NULL;

	protected $sphinx_enabled = 0;

	protected $sphinxHost = 'localhost';

	protected $sphinxPort = 9312;

	protected $sphinxSource = NULL;

	public function groups()
	{
		$groups = array(
			Yii::t('Dashboard.models-general', 'Site Settings') => array('name', 'offline', 'enableAPI', 'bcrypt_cost', 'categoryPaginationSize','contentPaginationSize','searchPaginationSize'),
			Yii::t('Dashboard.models-generate', 'Disqus') => array('useDisqusComments', 'disqus_shortname'),
			Yii::t('Dashboard.models-general', 'Comments') => array('notifyAuthorOnComment', 'autoApproveComments'),
			Yii::t('Dashboard.models-general', 'Display Settings') => array('dateFormat', 'timeFormat', 'defaultLanguage'),
			Yii::t('Dashboard.models-general', 'Sphinx') => array('sphinx_enabled', 'sphinxHost', 'sphinxPort', 'sphinxSource'),
		);

		// If the API has been disabled via CiiParams, then don't show it here
		if (($allow_api = Cii::get(Cii::getCiiConfig(), 'allow_api', true)) == false)
			unset($groups[Yii::t('Dashboard.models-general', 'Site Settings')][2]);

		return $groups;
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
			array('offline, preferMarkdown, sphinx_enabled, notifyAuthorOnComment, autoApproveComments, useDisqusComments, enableAPI', 'boolean'),
			array('sphinxHost, sphinxSource, disqus_shortname', 'length', 'max' => 255),
			array('sphinxPort', 'numerical', 'integerOnly' => true),
			array('bcrypt_cost', 'numerical', 'integerOnly'=>true, 'min' => 13, 'max' => 50),
			array('searchPaginationSize, categoryPaginationSize, contentPaginationSize', 'numerical', 'integerOnly' => true, 'min' => 1, 'max' => 100),
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
			'name' => Yii::t('Dashboard.models-general', 'Site Name'),
			'dateFormat' => Yii::t('Dashboard.models-general', 'Date Format'),
			'timeFormat' => Yii::t('Dashboard.models-general', 'Time Format'),
			'defaultLanguage' => Yii::t('Dashboard.models-general', 'Default Language'),
			'offline' => Yii::t('Dashboard.models-general', 'Offline Mode'),
			'enableAPI' => Yii::t('Dashboard.models-general', 'Enable API'),
			'bcrypt_cost' => Yii::t('Dashboard.models-general', 'Password Strength Settings'),
			'searchPaginationSize' => Yii::t('Dashboard.models-general', 'Search Post Count'),
			'categoryPaginationSize' => Yii::t('Dashboard.models-general', 'Category Post Count'),
			'contentPaginationSize' => Yii::t('Dashboard.models-general', 'Content Post Cost'),
			'sphinx_enabled' => Yii::t('Dashboard.models-general', 'Enable Sphinx Search'),
			'sphinxHost' => Yii::t('Dashboard.models-general', 'Sphinx Hostname'),
			'sphinxPort' => Yii::t('Dashboard.models-general', 'Sphinx Port'),
			'sphinxSource' => Yii::t('Dashboard.models-general', 'Sphinx Source Name'),
			'notifyAuthorOnComment' => Yii::t('Dashboard.models-general', 'Notify Author on New Comment'),
			'autoApproveComments'	=> Yii::t('Dashboard.models-general', 'Auto Approve Comments'),
			'useDisqusComments'    => Yii::t('Dashboard.models-general', 'Use Disqus Comments'),
			'disqus_shortname'     => Yii::t('Dashboard.models-general', 'Disqus Shortcode')
		);
	}

	/**
	 * Allow some override values
	 * @return parent::beforeSave();
	 */
	public function beforeSave()
	{
		if (($allow_api= Cii::get(Cii::getCiiConfig(), 'allow_api', true)) == false)
			$this->attributes['enableAPI'] = $this->enableAPI = (int)$allow_api;

		return parent::beforeSave();
	}
}