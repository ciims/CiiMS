<?php

class Weather extends CiiCard
{
	private $api_endpoint = 'https://api.forecast.io/forecast/';

	public $scriptName = 'FcAlexkTPM';
	
	/**
	 * Replaces the footer text with obligitory text for forecast.io
	 * @var string
	 */
	public $footerText = 'Powered by Forecast.io';

	/**
	 * Global API that is applied to all cards of the same type as this
	 * @var string Forecast.io API key
	 */
	protected $apikey = NULL;

	/**
	 * Use metric measurements for the display or not
	 * @var boolean false
	 */
	protected $metric = false;

	/**
	 * Validation rules for components of this model
	 * @return array
	 */
	public function rules()
	{
		return array(
			array('apikey', 'required'),
			array('metric', 'boolean')
		);
	}

	/**
	 * Attribute label
	 * @return array
	 */
	public function attributeLabels()
	{
		return array(
			'apikey' => 'API Key'
		);
	}

	public function getCurrentConditions($data)
	{
		$curl = curl_init();
		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_URL => $this->api_endpoint . $this->apiKey .'/' . $data['latitude'] . ',' . $data['longitude']
		));

		$resp = curl_exec($curl);
		curl_close($curl);

		return $resp;
	}
}