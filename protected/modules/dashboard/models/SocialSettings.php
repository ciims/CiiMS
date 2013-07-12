<?php

class SocialSettings extends CiiSettingsModel
{
	protected $ha_twitter_enabled = false;
	protected $ha_twitter_key = NULL;
	protected $ha_twitter_secret = NULL;
	protected $ha_twitter_accessToken = NULL;
	protected $ha_twitter_accessTokenSecret = NULL;

	protected $ha_facebook_enabled = false;
	protected $ha_facebook_id = NULL;
	protected $ha_facebook_secret = NULL;
	protected $ha_facebook_scope =  NULL;

	protected $ha_google_enabled = false;
	protected $ha_google_id = NULL;
	protected $ha_google_secret = NULL;
	protected $ha_google_scope = NULL;

	protected $ha_linkedin_enabled = false;
	protected $ha_linkedin_key = NULL;
	protected $ha_linkedin_secret = NULL;
	
	public function groups()
	{
		return array(
			'Twitter'  => array('ha_twitter_enabled', 'ha_twitter_key', 'ha_twitter_secret', 'ha_twitter_accessToken', 'ha_twitter_accessTokenSecret'),
			'Facebook' => array('ha_facebook_enabled', 'ha_facebook_id', 'ha_facebook_secret', 'ha_facebook_scope'),
			'Google+'  => array('ha_google_enabled', 'ha_google_id', 'ha_google_secret', 'ha_google_scope'),
			'LinkedIn' => array('ha_linkedin_enabled', 'ha_linkedin_key', 'ha_linkedin_secret')
		);
	}

	public function rules()
	{
		return array(
			array('ha_twitter_key, ha_twitter_secret, ha_twitter_accessToken, ha_twitter_accessToken, ha_twitter_accessTokenSecret', 'length', 'max' => 255),
			array('ha_facebook_id, ha_facebook_secret', 'length', 'max' => 255),
			array('ha_google_id, ha_google_secret', 'length', 'max' => 255),
			array('ha_linkedin_key, ha_linkedin_secret', 'length', 'max' => 255),
			array('ha_twitter_enabled, ha_facebook_enabled, ha_google_enabled, ha_linkedin_enabled', 'boolean')
		);
	}

	public function attributeLabels()
	{
		return array(
			'ha_twitter_enabled' => 'Social Auth',
			'ha_twitter_key' => 'Consumer Key',
			'ha_twitter_secret' => 'Consumer Secret',
			'ha_twitter_accessToken' => 'Access Token',
			'ha_twitter_accessTokenSecret' => 'Access Token Secret',

			'ha_facebook_enabled' => 'Social Auth',
			'ha_facebook_id' => 'App ID',
			'ha_facebook_secret' => 'App Secret',
			'ha_facebook_scope' => 'Scope',

			'ha_google_enabled' => 'Social Auth',
			'ha_google_id' => 'Client ID',
			'ha_google_secret' => 'Client Secret',
			'ha_google_scope' => 'Scope',

			'ha_linkedin_enabled' => 'Social Auth',
			'ha_linkedin_key' => 'Consumer Key',
			'ha_linkedin_secret' => 'Consumer Secret'
		);
	}
}