<?php

class SocialSettings extends CiiSettingsModel
{
	protected $twitter_enabled = false;
	protected $twitter_key = NULL;
	protected $twitter_secret = NULL;
	protected $twitter_accessToken = NULL;
	protected $twitter_accessTokenSecret = NULL;

	protected $facebook_enabled = false;
	protected $facebook_id = NULL;
	protected $facebook_secret = NULL;
	protected $facebook_scope =  NULL;

	protected $google_enabled = false;
	protected $google_id = NULL;
	protected $google_secret = NULL;
	protected $google_scope = NULL;

	protected $linkedin_enabled = false;
	protected $linkedin_key = NULL;
	protected $linkedin_secret = NULL;
	public function groups()
	{
		return array(
			'Twitter'  => array('twitter_enabled', 'twitter_key', 'twitter_secret', 'twitter_accessToken', 'twitter_accessTokenSecret'),
			'Facebook' => array('facebook_enabled', 'facebook_id', 'facebook_secret', 'facebook_scope'),
			'Google+'  => array('google_enabled', 'google_id', 'google_secret', 'google_scope'),
			'LinkedIn' => array('linkedin_enabled', 'linkedin_key', 'linkedin_secret')
		);
	}

	public function rules()
	{
		return array(
			array('twitter_key, twitter_secret, twitter_accessToken, twitter_accessToken, twitter_accessTokenSecret', 'length', 'max' => 255),
			array('facebook_id, facebook_secret', 'length', 'max' => 255),
			array('google_id, google_secret', 'length', 'max' => 255),
			array('linkedin_key, linkedin_secret', 'length', 'max' => 255),
			array('twitter_enabled, facebook_enabled, google_enabled, linkedin_enabled', 'boolean')
		);
	}

	public function attributeLabels()
	{
		return array(
			'twitter_enabled' => 'Social Auth',
			'twitter_key' => 'Consumer Key',
			'twitter_secret' => 'Consumer Secret',
			'twitter_accessToken' => 'Access Token',
			'twitter_accessTokenSecret' => 'Access Token Secret',

			'facebook_enabled' => 'Social Auth',
			'facebook_id' => 'App ID',
			'facebook_secret' => 'App Secret',
			'facebook_scope' => 'Scope',

			'google_enabled' => 'Social Auth',
			'google_id' => 'Client ID',
			'google_secret' => 'Client Secret',
			'google_scope' => 'Scope',

			'linkedin_enabled' => 'Social Auth',
			'linkedin_key' => 'Consumer Key',
			'linkedin_secret' => 'Consumer Secret'
		);
	}
}