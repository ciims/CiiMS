<?php

Yii::import('ext.EAnalytics.EAnalytics');
class CiiAnalytics extends EAnalytics
{
	public $options;
	public $lowerBounceRate;

	public function getProviders()
	{
		return $this->options;
	}
}