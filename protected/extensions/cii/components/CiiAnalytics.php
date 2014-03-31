<?php

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
		$providers = Cii::getAnalyticsProviders();
		return CMap::mergeArray($this->options, $providers);
	}
}