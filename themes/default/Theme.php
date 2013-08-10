<?php

Yii::import('application.modules.dashboard.components.CiiSettingsModel');
class Theme extends CiiSettingsModel
{
	private $theme = 'default';

	protected $twitterHandle = null;

	protected $twitterTweetsToFetch = 1;

	protected $splashLogo = '/images/splash-logo.jpg';

	public function rules()
	{
		return array(
			array('twitterHandle, splashLogo', 'length', 'max' => 255),
			array('twitterTweetsToFetch', 'numerical', 'integerOnly' => true, 'min' => 0),
		);
	}

	public function groups()
	{
		return array(
			'Twitter Settings' => array('twitterHandle', 'twitterTweetsToFetch'),
			'Appearance' =>  array('splashLogo')
		);
	}

	public function attributeLabels()
	{
		return array(
			'twitterHandle' => 'Twitter Handle',
			'twitterTweetsToFetch' => 'Number of Tweets to Fetch'
		);
	}

	public function afterSave()
	{
		// Bust the cache
		Yii::app()->cache->delete($this->theme . '_settings_tweets');
		Yii::app()->cache->delete($this->theme . '_settings_splashLogo');
	}

	/**
	 * getTweets callback method
	 * @param  $_POST  $postData Data supplied over post
	 */
	public function getTweets($postData=NULL)
	{

		header("Content-Type: application/json");

		Yii::import('ext.twitteroauth.*');

    	try {
    		$connection = new TwitterOAuth(
        		Cii::getConfig('ha_twitter_key', NULL, NULL), 
        		Cii::getConfig('ha_twitter_secret', NULL, NULL),
        		Cii::getConfig('ha_twitter_accessToken', NULL, NULL),
        		Cii::getConfig('ha_twitter_accessTokenSecret', NULL, NULL)
    		);
    		
    		$tweets = Yii::app()->cache->get($this->theme . '_settings_tweets');

    		if ($tweets == false)
    		{
				$tweets = $connection->get("https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name={$this->twitterHandle}&include_rts=false&exclude_replies=true&count={$this->twitterTweetsToFetch}");

				// Cache the result for 15 minutes
				if (!isset($tweets->errors))
					Yii::app()->cache->set($this->theme . '_settings_tweets', $tweets, 900);
			}

			echo CJSON::encode($tweets);

		} catch (Exception $e) {
			echo CJSON::encode(array('errors' => array('message' => $e->getMessage())));
		}
	}
}