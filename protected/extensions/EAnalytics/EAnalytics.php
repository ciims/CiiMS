<?php

/**
 * EAnalytics uses Analytics.js via Segment.io to provide a global analytics and tracking solution
 * This widget is solely for having a unified tracking code and does not report to Segment.io
 * 
 * @author  Charles R. Portwood II <charlesportwoodii@ethreal.net>
 * @license MIT
 */
class EAnalytics extends CApplicationComponent
{
	public $providers = array();

	public $lowerBounceRate = false;
	
	/**
	 * Registers Analytics.js and initializes the tracking code
	 */
	public function init()
	{
		// Set the alias path
		if (Yii::getPathOfAlias('analytics') === false)
            Yii::setPathOfAlias('analytics', realpath(dirname(__FILE__) . '/..'));

        parent::init();

		// Don't load up the analytics.js if we don't have any data
		$providers = $this->getProviders()
		if (empty($providers))
			return;

		// Conver options into json
		$json = CJSON::encode($this->getProviders());

		// Load up the asset manager
		$asset = Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('ext.EAnalytics.assets.js'), true, -1, YII_DEBUG);
		$cs    = Yii::app()->getClientScript();

		// Register the appropriate script file
		$cs->registerScriptFile($asset . (YII_DEBUG ? '/analytics.js' : '/analytics.min.js'));

		// Initialize
		$cs->registerScript('analytics.js', "analytics.initialize({$json});");

		if ($this->lowerBounceRate)
		{
			$cs->registerScript('analytics.js-bounce-rate-15', 'setTimeout(function() { analytics.track("_trackEvent", "15 Seconds"); }, 15000 );');
			$cs->registerScript('analytics.js-bounce-rate-30', 'setTimeout(function() { analytics.track("_trackEvent", "30 Seconds"); }, 30000 );');
			$cs->registerScript('analytics.js-bounce-rate-60', 'setTimeout(function() { analytics.track("_trackEvent", "60 Seconds"); }, 60000 );');
		}

	}

	/**
	 * getProviders provides us with the providers that we want to use.
	 * This method is implemented so that we can overload it via class extension 
	 * @return array
	 */
	public function getProviders()
	{
		return $this->providers;
	}
}