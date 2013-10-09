<?php

Yii::import('ext.EAnalytics.EAnalytics');
class CiiAnalytics extends EAnalytics
{
	public $options;

	public $lowerBounceRate;

	/**
	 * Direct overload of EAnalytics::getProviders()
	 * @return array(), Providors from database merges with providers from config
	 */
	public function getProviders()
	{
		$providers = array();

		try {
			$providers = Cii::getAnalyticsProviders();
		} catch (Exception $e) {}

		return CMap::mergeArray($this->options, $providers);
	}
}