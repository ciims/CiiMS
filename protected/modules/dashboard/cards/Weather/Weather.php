<?php

class Weather extends CiiCard
{
	/**
	 * Replaces the footer text with obligitory text for forecast.io
	 * @var string
	 */
	public $footerText = 'Powered by Forecast.io';

	/**
	 * Global API that is applied to all cards of the same type as this
	 * @var string Forecast.io API key
	 */
	protected $global_apikey = NULL;

	/**
	 * Validation rules for components of this model
	 * @return array
	 */
	public function rules()
	{
		return array(
			array('global_apikey', 'required'),
		);
	}

	/**
	 * Attribute label
	 * @return array
	 */
	public function attributeLabels()
	{
		return array(
			'global_apikey' => 'API Key'
		);
	}
}