<?php

class Weather extends CiiCard
{
	/**
	 * Replaces the footer text with obligitory text for forecast.io
	 * @var string
	 */
	public $footerText = 'Powered by Forecast.io';

	/**
	 * Disables the setting pane for this particular card
	 * @var boolean false
	 */
	//public $settingsPane = false;
	
	/**
	 * Global API that is applied to all cards of the same type as this
	 * @var string Forecast.io API key
	 */
	protected $global_apikey = NULL;

	protected $enableWeather = true;

	/**
	 * Validation rules for components of this model
	 * @return array
	 */
	public function rules()
	{
		return array(
			array('global_apikey', 'required'),
			array('enableWeather', 'boolean')
		);
	}

	/**
	 * Attribute label
	 * @return array
	 */
	public function attributeLabels()
	{
		return array(
			'global_apikey' => 'API Key',
			'enableWeather' => 'Enable Weather'
		);
	}
}