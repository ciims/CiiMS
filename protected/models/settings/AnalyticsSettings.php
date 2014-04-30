<?php

// See application.modules.dashboard.assets.AnalyticsSettingsBuilder.js for instructions on how to automatically generate this file
// Note that you'll need to remove any "undefined" variables in here via stringReplace
class AnalyticsSettings extends CiiSettingsModel
{
	protected $analyticsjs_AdRoll_enabled = false;
	protected $analyticsjs_AdRoll_advId = NULL;
	protected $analyticsjs_AdRoll_pixId = NULL;

	protected $analyticsjs_Amplitude_enabled = false;
	protected $analyticsjs_Amplitude_apiKey = NULL;
	protected $analyticsjs_Amplitude_pageview = NULL;

	protected $analyticsjs_BugHerd_enabled = false;
	protected $analyticsjs_BugHerd_apiKey = NULL;
	protected $analyticsjs_BugHerd_showFeedbackTab = NULL;

	protected $analyticsjs_Chartbeat_enabled = false;
	protected $analyticsjs_Chartbeat_domain = NULL;
	protected $analyticsjs_Chartbeat_uid = NULL;

	protected $analyticsjs_ClickTale_enabled = false;
	protected $analyticsjs_ClickTale_httpCdnUrl = NULL;
	protected $analyticsjs_ClickTale_httpsCdnUrl = NULL;
	protected $analyticsjs_ClickTale_projectId = NULL;
	protected $analyticsjs_ClickTale_recordingRatio = NULL;
	protected $analyticsjs_ClickTale_partitionId = NULL;

	protected $analyticsjs_Clicky_enabled = false;
	protected $analyticsjs_Clicky_siteId = NULL;

	protected $analyticsjs_comScore_enabled = false;
	protected $analyticsjs_comScore_c1 = NULL;
	protected $analyticsjs_comScore_c2 = NULL;

	protected $analyticsjs_CrazyEgg_enabled = false;
	protected $analyticsjs_CrazyEgg_accountNumber = NULL;

	protected $analyticsjs_Customer___io_enabled = false;
	protected $analyticsjs_Customer___io_siteId = NULL;

	protected $analyticsjs_Errorception_enabled = false;
	protected $analyticsjs_Errorception_projectId = NULL;
	protected $analyticsjs_Errorception_meta = NULL;

	protected $analyticsjs_FoxMetrics_enabled = false;
	protected $analyticsjs_FoxMetrics_appId = NULL;

	protected $analyticsjs_Gauges_enabled = false;
	protected $analyticsjs_Gauges_siteId = NULL;

	protected $analyticsjs_Get__Satisfaction_enabled = false;
	protected $analyticsjs_Get__Satisfaction_widgetId = NULL;

	protected $analyticsjs_Google__Analytics_enabled = false;
	protected $analyticsjs_Google__Analytics_anonymizeIp = false;
	protected $analyticsjs_Google__Analytics_domain = NULL;
	protected $analyticsjs_Google__Analytics_doubleClick = false;
	protected $analyticsjs_Google__Analytics_enhancedLinkAttribution = true;
	protected $analyticsjs_Google__Analytics_ignoreReferrer = false;
	protected $analyticsjs_Google__Analytics_initialPageview = true;
	protected $analyticsjs_Google__Analytics_siteSpeedSampleRate = NULL;
	protected $analyticsjs_Google__Analytics_trackingId = NULL;
	protected $analyticsjs_Google__Analytics_universalClient = false;

	protected $analyticsjs_GoSquared_enabled = false;
	protected $analyticsjs_GoSquared_siteToken = NULL;

	protected $analyticsjs_Heap_enabled = false;
	protected $analyticsjs_Heap_apiKey = NULL;

	protected $analyticsjs_HitTail_enabled = false;
	protected $analyticsjs_HitTail_siteId = NULL;

	protected $analyticsjs_HubSpot_enabled = false;
	protected $analyticsjs_HubSpot_portalId = NULL;

	protected $analyticsjs_Improvely_enabled = false;
	protected $analyticsjs_Improvely_domain = NULL;
	protected $analyticsjs_Improvely_projectId = NULL;

	protected $analyticsjs_Intercom_enabled = false;
	protected $analyticsjs_Intercom_appId = NULL;
	protected $analyticsjs_Intercom_activator = NULL;
	protected $analyticsjs_Intercom_counter = NULL;

	protected $analyticsjs_Keen__IO_enabled = false;
	protected $analyticsjs_Keen__IO_projectId = NULL;
	protected $analyticsjs_Keen__IO_writeKey = NULL;
	protected $analyticsjs_Keen__IO_readKey = NULL;
	protected $analyticsjs_Keen__IO_pageview = NULL;
	protected $analyticsjs_Keen__IO_initialPageview = NULL;

	protected $analyticsjs_KISSmetrics_enabled = false;
	protected $analyticsjs_KISSmetrics_apiKey = NULL;

	protected $analyticsjs_Klaviyo_enabled = false;
	protected $analyticsjs_Klaviyo_apiKey = NULL;

	protected $analyticsjs_LiveChat_enabled = false;
	protected $analyticsjs_LiveChat_license = NULL;

	protected $analyticsjs_Lytics_enabled = false;
	protected $analyticsjs_Lytics_cid = NULL;

	protected $analyticsjs_Mixpanel_enabled = false;
	protected $analyticsjs_Mixpanel_nameTag = NULL;
	protected $analyticsjs_Mixpanel_people = NULL;
	protected $analyticsjs_Mixpanel_token = NULL;
	protected $analyticsjs_Mixpanel_pageview = NULL;
	protected $analyticsjs_Mixpanel_initialPageview = NULL;

	protected $analyticsjs_Olark_enabled = false;
	protected $analyticsjs_Olark_siteId = NULL;
	protected $analyticsjs_Olark_identify = NULL;
	protected $analyticsjs_Olark_track = NULL;
	protected $analyticsjs_Olark_pageview = NULL;

	protected $analyticsjs_Optimizely_enabled = false;
	protected $analyticsjs_Optimizely_variations = NULL;

	protected $analyticsjs_Perfect__Audience_enabled = false;
	protected $analyticsjs_Perfect__Audience_siteId = NULL;

	protected $analyticsjs_Pingdom_enabled = false;
	protected $analyticsjs_Pingdom_id = NULL;

	protected $analyticsjs_pwk_enabled = false;
	protected $analyticsjs_pwk_url = NULL;
	protected $analyticsjs_pwk_id = NULL;

	protected $analyticsjs_Preact_enabled = false;
	protected $analyticsjs_Preact_projectCode = NULL;

	protected $analyticsjs_Qualaroo_enabled = false;
	protected $analyticsjs_Qualaroo_customerId = NULL;
	protected $analyticsjs_Qualaroo_siteToken = NULL;
	protected $analyticsjs_Qualaroo_track = NULL;

	protected $analyticsjs_Quantcast_enabled = false;
	protected $analyticsjs_Quantcast_pCode = NULL;

	protected $analyticsjs_Sentry_enabled = false;
	protected $analyticsjs_Sentry_config = NULL;

	protected $analyticsjs_SnapEngage_enabled = false;
	protected $analyticsjs_SnapEngage_apiKey = NULL;

	protected $analyticsjs_USERcycle_enabled = false;
	protected $analyticsjs_USERcycle_key = NULL;

	protected $analyticsjs_userfox_enabled = false;
	protected $analyticsjs_userfox_clientId = NULL;

	protected $analyticsjs_UserVoice_enabled = false;
	protected $analyticsjs_UserVoice_widgetId = NULL;
	protected $analyticsjs_UserVoice_forumId = NULL;
	protected $analyticsjs_UserVoice_showTab = NULL;
	protected $analyticsjs_UserVoice_mode = NULL;
	protected $analyticsjs_UserVoice_primaryColor = NULL;
	protected $analyticsjs_UserVoice_linkColor = NULL;
	protected $analyticsjs_UserVoice_defaultMode = NULL;
	protected $analyticsjs_UserVoice_tabLabel = NULL;
	protected $analyticsjs_UserVoice_tabColor = NULL;
	protected $analyticsjs_UserVoice_tabPosition = NULL;
	protected $analyticsjs_UserVoice_tabInverted = NULL;

	protected $analyticsjs_Vero_enabled = false;
	protected $analyticsjs_Vero_apiKey = NULL;

	protected $analyticsjs_Visual__Website__Optimizer_enabled = false;
	protected $analyticsjs_Visual__Website__Optimizer_replay = NULL;

	protected $analyticsjs_Woopra_enabled = false;
	protected $analyticsjs_Woopra_domain = NULL;

	public $form = 'application.modules.dashboard.views.analytics.form';

	public function groups()
	{
		return array(
			//'AdRoll' => array('analyticsjs_AdRoll_enabled', 'analyticsjs_AdRoll_advId', 'analyticsjs_AdRoll_pixId'),
			//'Amplitude' => array('analyticsjs_Amplitude_enabled', 'analyticsjs_Amplitude_apiKey', 'analyticsjs_Amplitude_pageview'),
			'BugHerd' => array('analyticsjs_BugHerd_enabled', 'analyticsjs_BugHerd_apiKey', 'analyticsjs_BugHerd_showFeedbackTab'),
			//'Chartbeat' => array('analyticsjs_Chartbeat_enabled', 'analyticsjs_Chartbeat_domain', 'analyticsjs_Chartbeat_uid'),
			//'ClickTale' => array('analyticsjs_ClickTale_enabled', 'analyticsjs_ClickTale_httpCdnUrl', 'analyticsjs_ClickTale_httpsCdnUrl', 'analyticsjs_ClickTale_projectId', 'analyticsjs_ClickTale_recordingRatio', 'analyticsjs_ClickTale_partitionId'),
			'Clicky' => array('analyticsjs_Clicky_enabled', 'analyticsjs_Clicky_siteId'),
			//'comScore' => array('analyticsjs_comScore_enabled', 'analyticsjs_comScore_c1', 'analyticsjs_comScore_c2'),
			//'CrazyEgg' => array('analyticsjs_CrazyEgg_enabled', 'analyticsjs_CrazyEgg_accountNumber'),
			//'Customer.io' => array('analyticsjs_Customer___io_enabled', 'analyticsjs_Customer___io_siteId'),
			'Errorception' => array('analyticsjs_Errorception_enabled', 'analyticsjs_Errorception_projectId', 'analyticsjs_Errorception_meta'),
			//'FoxMetrics' => array('analyticsjs_FoxMetrics_enabled', 'analyticsjs_FoxMetrics_appId'),
			'Gauges' => array('analyticsjs_Gauges_enabled', 'analyticsjs_Gauges_siteId'),
			//'Get Satisfaction' => array('analyticsjs_Get__Satisfaction_enabled', 'analyticsjs_Get__Satisfaction_widgetId'),
			'Google Analytics' => array('analyticsjs_Google__Analytics_enabled', 'analyticsjs_Google__Analytics_anonymizeIp', 'analyticsjs_Google__Analytics_domain', 'analyticsjs_Google__Analytics_doubleClick', 'analyticsjs_Google__Analytics_enhancedLinkAttribution', 'analyticsjs_Google__Analytics_ignoreReferrer', 'analyticsjs_Google__Analytics_initialPageview', 'analyticsjs_Google__Analytics_siteSpeedSampleRate', 'analyticsjs_Google__Analytics_trackingId', 'analyticsjs_Google__Analytics_universalClient'),
			//'GoSquared' => array('analyticsjs_GoSquared_enabled', 'analyticsjs_GoSquared_siteToken'),
			//'Heap' => array('analyticsjs_Heap_enabled', 'analyticsjs_Heap_apiKey'),
			//'HitTail' => array('analyticsjs_HitTail_enabled', 'analyticsjs_HitTail_siteId'),
			//'HubSpot' => array('analyticsjs_HubSpot_enabled', 'analyticsjs_HubSpot_portalId'),
			//'Improvely' => array('analyticsjs_Improvely_enabled', 'analyticsjs_Improvely_domain', 'analyticsjs_Improvely_projectId'),
			//'Intercom' => array('analyticsjs_Intercom_enabled', 'analyticsjs_Intercom_appId', 'analyticsjs_Intercom_activator', 'analyticsjs_Intercom_counter'),
			//'Keen IO' => array('analyticsjs_Keen__IO_enabled', 'analyticsjs_Keen__IO_projectId', 'analyticsjs_Keen__IO_writeKey', 'analyticsjs_Keen__IO_readKey', 'analyticsjs_Keen__IO_pageview', 'analyticsjs_Keen__IO_initialPageview'),
			//'KISSmetrics' => array('analyticsjs_KISSmetrics_enabled', 'analyticsjs_KISSmetrics_apiKey'),
			//'Klaviyo' => array('analyticsjs_Klaviyo_enabled', 'analyticsjs_Klaviyo_apiKey'),
			//'LiveChat' => array('analyticsjs_LiveChat_enabled', 'analyticsjs_LiveChat_license'),
			//'Lytics' => array('analyticsjs_Lytics_enabled', 'analyticsjs_Lytics_cid'),
			'Mixpanel' => array('analyticsjs_Mixpanel_enabled', 'analyticsjs_Mixpanel_nameTag', 'analyticsjs_Mixpanel_people', 'analyticsjs_Mixpanel_token', 'analyticsjs_Mixpanel_pageview', 'analyticsjs_Mixpanel_initialPageview'),
			//'Olark' => array('analyticsjs_Olark_enabled', 'analyticsjs_Olark_siteId', 'analyticsjs_Olark_identify', 'analyticsjs_Olark_track', 'analyticsjs_Olark_pageview'),
			//'Optimizely' => array('analyticsjs_Optimizely_enabled', 'analyticsjs_Optimizely_variations'),
			//'Perfect Audience' => array('analyticsjs_Perfect__Audience_enabled', 'analyticsjs_Perfect__Audience_siteId'),
			'Pingdom' => array('analyticsjs_Pingdom_enabled', 'analyticsjs_Pingdom_id'),
			'pwk' => array('analyticsjs_pwk_enabled', 'analyticsjs_pwk_url', 'analyticsjs_pwk_id'),
			//'Preact' => array('analyticsjs_Preact_enabled', 'analyticsjs_Preact_projectCode'),
			//'Qualaroo' => array('analyticsjs_Qualaroo_enabled', 'analyticsjs_Qualaroo_customerId', 'analyticsjs_Qualaroo_siteToken', 'analyticsjs_Qualaroo_track'),
			//'Quantcast' => array('analyticsjs_Quantcast_enabled', 'analyticsjs_Quantcast_pCode'),
			//'Sentry' => array('analyticsjs_Sentry_enabled', 'analyticsjs_Sentry_config'),
			//'SnapEngage' => array('analyticsjs_SnapEngage_enabled', 'analyticsjs_SnapEngage_apiKey'),
			//'USERcycle' => array('analyticsjs_USERcycle_enabled', 'analyticsjs_USERcycle_key'),
			//'userfox' => array('analyticsjs_userfox_enabled', 'analyticsjs_userfox_clientId'),
			//'UserVoice' => array('analyticsjs_UserVoice_enabled', 'analyticsjs_UserVoice_widgetId', 'analyticsjs_UserVoice_forumId', 'analyticsjs_UserVoice_showTab', 'analyticsjs_UserVoice_mode', 'analyticsjs_UserVoice_primaryColor', 'analyticsjs_UserVoice_linkColor', 'analyticsjs_UserVoice_defaultMode', 'analyticsjs_UserVoice_tabLabel', 'analyticsjs_UserVoice_tabColor', 'analyticsjs_UserVoice_tabPosition', 'analyticsjs_UserVoice_tabInverted'),
			//'Vero' => array('analyticsjs_Vero_enabled', 'analyticsjs_Vero_apiKey'),
			//'Visual Website Optimizer' => array('analyticsjs_Visual__Website__Optimizer_enabled', 'analyticsjs_Visual__Website__Optimizer_replay'),
			//'Woopra' => array('analyticsjs_Woopra_enabled', 'analyticsjs_Woopra_domain'),
		);
	}

	public function rules()
	{
		return array(
			array('analyticsjs_AdRoll_enabled, analyticsjs_Amplitude_enabled, analyticsjs_BugHerd_enabled, analyticsjs_Chartbeat_enabled, analyticsjs_ClickTale_enabled, analyticsjs_Clicky_enabled, analyticsjs_comScore_enabled, analyticsjs_CrazyEgg_enabled, analyticsjs_Customer___io_enabled, analyticsjs_Errorception_enabled, analyticsjs_FoxMetrics_enabled, analyticsjs_Gauges_enabled, analyticsjs_Get__Satisfaction_enabled, analyticsjs_Google__Analytics_enabled, analyticsjs_GoSquared_enabled, analyticsjs_Heap_enabled, analyticsjs_HitTail_enabled, analyticsjs_HubSpot_enabled, analyticsjs_Improvely_enabled, analyticsjs_Intercom_enabled, analyticsjs_Keen__IO_enabled, analyticsjs_KISSmetrics_enabled, analyticsjs_Klaviyo_enabled, analyticsjs_LiveChat_enabled, analyticsjs_Lytics_enabled, analyticsjs_Mixpanel_enabled, analyticsjs_Olark_enabled, analyticsjs_Optimizely_enabled, analyticsjs_Perfect__Audience_enabled, analyticsjs_Pingdom_enabled, analyticsjs_pwk_enabled, analyticsjs_Preact_enabled, analyticsjs_Google__Analytics_universalClient, analyticsjs_Qualaroo_enabled,analyticsjs_Google__Analytics_anonymizeIp, analyticsjs_Google__Analytics_doubleClick, analyticsjs_Google__Analytics_enhancedLinkAttribution, analyticsjs_Google__Analytics_ignoreReferrer, analyticsjs_Google__Analytics_initialPageview, analyticsjs_Quantcast_enabled, analyticsjs_Sentry_enabled, analyticsjs_SnapEngage_enabled, analyticsjs_USERcycle_enabled, analyticsjs_userfox_enabled, analyticsjs_UserVoice_enabled, analyticsjs_Vero_enabled, analyticsjs_Visual__Website__Optimizer_enabled, analyticsjs_Woopra_enabled', 'boolean'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'analyticsjs_AdRoll_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_AdRoll_advId' => 'advId',
			'analyticsjs_AdRoll_pixId' => 'pixId',

			'analyticsjs_Amplitude_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Amplitude_apiKey' => Yii::t('Dashboard.models-analytics', 'API Key'),
			'analyticsjs_Amplitude_pageview' => 'pageview',

			'analyticsjs_BugHerd_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_BugHerd_apiKey' => Yii::t('Dashboard.models-analytics', 'API Key'),
			'analyticsjs_BugHerd_showFeedbackTab' => 'showFeedbackTab',

			'analyticsjs_Chartbeat_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Chartbeat_domain' => Yii::t('Dashboard.models-analytics', 'Domain'),
			'analyticsjs_Chartbeat_uid' => Yii::t('Dashboard.models-analytics', 'UID'),

			'analyticsjs_ClickTale_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_ClickTale_httpCdnUrl' => 'httpCdnUrl',
			'analyticsjs_ClickTale_httpsCdnUrl' => 'httpsCdnUrl',
			'analyticsjs_ClickTale_projectId' => 'projectId',
			'analyticsjs_ClickTale_recordingRatio' => 'recordingRatio',
			'analyticsjs_ClickTale_partitionId' => 'partitionId',

			'analyticsjs_Clicky_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Clicky_siteId' => Yii::t('Dashboard.models-analytics', 'Site ID'),

			'analyticsjs_comScore_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_comScore_c1' => 'c1',
			'analyticsjs_comScore_c2' => 'c2',

			'analyticsjs_CrazyEgg_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_CrazyEgg_accountNumber' => 'accountNumber',

			'analyticsjs_Customer___io_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Customer___io_siteId' => 'siteId',

			'analyticsjs_Errorception_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Errorception_projectId' => Yii::t('Dashboard.models-analytics', 'Project ID'),
			'analyticsjs_Errorception_meta' => Yii::t('Dashboard.models-analytics', 'Meta'),

			'analyticsjs_FoxMetrics_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_FoxMetrics_appId' => 'appId',

			'analyticsjs_Gauges_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Gauges_siteId' => Yii::t('Dashboard.models-analytics', 'Site ID'),

			'analyticsjs_Get__Satisfaction_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Get__Satisfaction_widgetId' => 'widgetId',

			'analyticsjs_Google__Analytics_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Google__Analytics_anonymizeIp' => Yii::t('Dashboard.models-analytics', 'Anonymize IP?'),
			'analyticsjs_Google__Analytics_domain' => Yii::t('Dashboard.models-analytics', 'Domain'),
			'analyticsjs_Google__Analytics_doubleClick' => Yii::t('Dashboard.models-analytics', 'Double Click?'),
			'analyticsjs_Google__Analytics_enhancedLinkAttribution' => Yii::t('Dashboard.models-analytics', 'Enable Enhanced Link Attribution?'),
			'analyticsjs_Google__Analytics_ignoreReferrer' => Yii::t('Dashboard.models-analytics', 'Ignore Referrer?'),
			'analyticsjs_Google__Analytics_initialPageview' => Yii::t('Dashboard.models-analytics', 'Track Initial Pageview?'),
			'analyticsjs_Google__Analytics_siteSpeedSampleRate' => Yii::t('Dashboard.models-analytics', 'Site Speed Sample Rate'),
			'analyticsjs_Google__Analytics_trackingId' => Yii::t('Dashboard.models-analytics', 'UA Tracking ID'),
			'analyticsjs_Google__Analytics_universalClient' => Yii::t('Dashboard.models-analytics', 'Use Universal Client?'),

			'analyticsjs_GoSquared_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_GoSquared_siteToken' => Yii::t('Dashboard.models-analytics', 'Site Token'),

			'analyticsjs_Heap_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Heap_apiKey' => Yii::t('Dashboard.models-analytics', 'API Key'),

			'analyticsjs_HitTail_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_HitTail_siteId' => 'siteId',

			'analyticsjs_HubSpot_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_HubSpot_portalId' => 'portalId',

			'analyticsjs_Improvely_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Improvely_domain' => 'domain',

			'analyticsjs_Improvely_projectId' => 'projectId',

			'analyticsjs_Intercom_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Intercom_appId' => 'appId',
			'analyticsjs_Intercom_activator' => 'activator',
			'analyticsjs_Intercom_counter' => 'counter',

			'analyticsjs_Keen__IO_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Keen__IO_projectId' => 'projectId',
			'analyticsjs_Keen__IO_writeKey' => 'writeKey',
			'analyticsjs_Keen__IO_readKey' => 'readKey',
			'analyticsjs_Keen__IO_pageview' => 'pageview',
			'analyticsjs_Keen__IO_initialPageview' => 'initialPageview',

			'analyticsjs_KISSmetrics_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_KISSmetrics_apiKey' => Yii::t('Dashboard.models-analytics', 'API Key'),

			'analyticsjs_Klaviyo_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Klaviyo_apiKey' => Yii::t('Dashboard.models-analytics', 'API Key'),

			'analyticsjs_LiveChat_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_LiveChat_license' => 'license',

			'analyticsjs_Lytics_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Lytics_cid' => 'cid',

			'analyticsjs_Mixpanel_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Mixpanel_nameTag' => 'nameTag',
			'analyticsjs_Mixpanel_people' => 'people',
			'analyticsjs_Mixpanel_token' => 'token',
			'analyticsjs_Mixpanel_pageview' => 'pageview',
			'analyticsjs_Mixpanel_initialPageview' => 'initialPageview',

			'analyticsjs_Olark_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Olark_siteId' => 'siteId',
			'analyticsjs_Olark_identify' => 'identify',
			'analyticsjs_Olark_track' => 'track',
			'analyticsjs_Olark_pageview' => 'pageview',
			'analyticsjs_Optimizely_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Optimizely_variations' => 'variations',

			'analyticsjs_Perfect__Audience_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Perfect__Audience_siteId' => 'siteId',

			'analyticsjs_Pingdom_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Pingdom_id' => Yii::t('Dashboard.models-analytics', 'id'),

			'analyticsjs_pwk_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_pwk_url' => Yii::t('Dashboard.models-analytics', 'pwk Host URL'),
			'analyticsjs_pwk_id' => Yii::t('Dashboard.models-analytics', 'Site ID'),

			'analyticsjs_Preact_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Preact_projectCode' => 'projectCode',

			'analyticsjs_Qualaroo_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Qualaroo_customerId' => 'customerId',
			'analyticsjs_Qualaroo_siteToken' => 'siteToken',
			'analyticsjs_Qualaroo_track' => 'track',
			'analyticsjs_Quantcast_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Quantcast_pCode' => 'pCode',

			'analyticsjs_Sentry_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Sentry_config' => Yii::t('Dashboard.models-analytics', 'Config'),

			'analyticsjs_SnapEngage_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_SnapEngage_apiKey' => Yii::t('Dashboard.models-analytics', 'API Key'),

			'analyticsjs_USERcycle_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_USERcycle_key' => 'key',

			'analyticsjs_userfox_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_userfox_clientId' => 'clientId',
			'analyticsjs_UserVoice_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_UserVoice_widgetId' => 'widgetId',
			'analyticsjs_UserVoice_forumId' => 'forumId',
			'analyticsjs_UserVoice_showTab' => 'showTab',
			'analyticsjs_UserVoice_mode' => 'mode',
			'analyticsjs_UserVoice_primaryColor' => 'primaryColor',
			'analyticsjs_UserVoice_linkColor' => 'linkColor',
			'analyticsjs_UserVoice_defaultMode' => 'defaultMode',
			'analyticsjs_UserVoice_tabLabel' => 'tabLabel',
			'analyticsjs_UserVoice_tabColor' => 'tabColor',
			'analyticsjs_UserVoice_tabPosition' => 'tabPosition',
			'analyticsjs_UserVoice_tabInverted' => 'tabInverted',

			'analyticsjs_Vero_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Vero_apiKey' => Yii::t('Dashboard.models-analytics', 'API Key'),

			'analyticsjs_Visual__Website__Optimizer_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Visual__Website__Optimizer_replay' => 'replay',

			'analyticsjs_Woopra_enabled' => Yii::t('Dashboard.models-analytics', 'Enabled'),
			'analyticsjs_Woopra_domain' => 'domain',
		);
	}

	public function afterSave()
	{
		Yii::app()->cache->set('analyticsjs_providers', false);
		Cii::getAnalyticsProviders();

		return parent::afterSave();
	}
}