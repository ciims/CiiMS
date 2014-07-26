<?php

// See application.modules.dashboard.assets.AnalyticsSettingsBuilder.js for instructions on how to automatically generate this file
// Note that you'll need to remove any "undefined" variables in here via stringReplace
class AnalyticsSettings extends CiiSettingsModel
{
	protected $analyticsjs_Google__Analytics_enabled = false;
	protected $analyticsjs_Google__Analytics_domain = NULL;
	protected $analyticsjs_Google__Analytics_trackingId = NULL;
	protected $analyticsjs_Google__Analytics_universalClient = false;

	protected $analyticsjs_Pingdom_enabled = false;
	protected $analyticsjs_Pingdom_id = NULL;

	protected $analyticsjs_Piwik_enabled = false;
	protected $analyticsjs_Piwik_url = NULL;
	protected $analyticsjs_Piwik_id = NULL;

	public $form = 'application.modules.dashboard.views.analytics.form';

	public function groups()
	{
		return array(
			'Google Analytics' => array('analyticsjs_Google__Analytics_enabled','analyticsjs_Google__Analytics_domain', 'analyticsjs_Google__Analytics_trackingId', 'analyticsjs_Google__Analytics_universalClient'),
			'Pingdom' => array('analyticsjs_Pingdom_enabled', 'analyticsjs_Pingdom_id'),
			'Piwik' => array('analyticsjs_Piwik_enabled', 'analyticsjs_Piwik_url', 'analyticsjs_Piwik_id'),
		);
	}

	public function rules()
	{
		return array(
			array('analyticsjs_Google__Analytics_enabled, analyticsjs_Google__Analytics_universalClient, analyticsjs_Pingdom_enabled, analyticsjs_Piwik_enabled', 'boolean')
		);
	}

	public function attributeLabels()
	{
		return array(
			'analyticsjs_Google__Analytics_enabled' => Yii::t('ciims.models.analytics', 'Enabled'),
			'analyticsjs_Google__Analytics_domain' => Yii::t('ciims.models.analytics', 'Domain'),
			'analyticsjs_Google__Analytics_trackingId' => Yii::t('ciims.models.analytics', 'UA Tracking ID'),
			'analyticsjs_Google__Analytics_universalClient' => Yii::t('ciims.models.analytics', 'Use Universal Client?'),

			'analyticsjs_Pingdom_enabled' => Yii::t('ciims.models.analytics', 'Enabled'),
			'analyticsjs_Pingdom_id' => Yii::t('ciims.models.analytics', 'id'),

			'analyticsjs_Piwik_enabled' => Yii::t('ciims.models.analytics', 'Enabled'),
			'analyticsjs_Piwik_url' => Yii::t('ciims.models.analytics', 'Piwik Host URL'),
			'analyticsjs_Piwik_id' => Yii::t('ciims.models.analytics', 'Site ID'),
		);
	}

	public function afterSave()
	{
		Yii::app()->cache->set('analyticsjs_providers', false);
		Cii::getAnalyticsProviders();

		return parent::afterSave();
	}
}