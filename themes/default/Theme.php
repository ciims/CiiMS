<?php

Yii::import('application.modules.dashboard.components.CiiSettingsModel');
class Theme extends CiiSettingsModel
{
    /**
     * @var string  The theme name
     */
	public $theme = 'default';

    /**
     * @var bool    Enables the use and inclusion of Bootstrap
     */
    public $useBootstrap = true;

    /**
     * @var string  The user's twitter handle
     */
	protected $twitterHandle = NULL;

    /**
     * @var string  The number of tweets to fetch for display
     */
	protected $twitterTweetsToFetch = 1;

    /**
     * @var string  The Splash logo URI
     */
	protected $splashLogo = NULL;

    /**
     * @var string  The default menu items in | form
     */
	protected $menu = 'dashboard|blog';

    /**
     * Validation Rules
     * @return array
     */
	public function rules()
	{
		return array(
			array('twitterHandle, menu, splashLogo', 'length', 'max' => 255),
			array('twitterTweetsToFetch', 'numerical', 'integerOnly' => true, 'min' => 0),
		);
	}

    /**
     * Dashboard Groupings
     * @return array
     */
	public function groups()
	{
		return array(
			Yii::t('DefaultTheme', 'Twitter Settings') => array('twitterHandle', 'twitterTweetsToFetch'),
			Yii::t('DefaultTheme', 'Appearance')       => array('splashLogo', 'menu'),
		);
	}

    /**
     * Attribute Labels
     * @return array
     */
	public function attributeLabels()
	{
		return array(
			'twitterHandle'        => Yii::t('DefaultTheme', 'Twitter Handle'),
			'twitterTweetsToFetch' => Yii::t('DefaultTheme', 'Number of Tweets to Fetch'),
			'menu'                 => Yii::t('DefaultTheme', 'Menu Items'),
			'splashLogo'           => Yii::t('DefaultTheme', 'Front Page Image'),
		);
	}

    /**
     * AfterSave Event, is triggered when the model attributes are saved
     */
	public function afterSave()
	{
		// Bust the cache
		Yii::app()->cache->delete($this->theme . '_settings_tweets');
		Yii::app()->cache->delete($this->theme . '_settings_splashLogo');
		return parent::afterSave();
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
				foreach ($tweets as &$tweet)
	            {
					$tweet->text = preg_replace("/([\w]+\:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/", "<a target=\"_blank\" href=\"$1\">$1</a>", $tweet->text);
					$tweet->text = preg_replace("/#([A-Za-z0-9\/\.]*)/", "<a target=\"_new\" href=\"http://twitter.com/search?q=$1\">#$1</a>", $tweet->text);
					$tweet->text = preg_replace("/@([A-Za-z0-9\/\.]*)/", "<a href=\"http://www.twitter.com/$1\">@$1</a>", $tweet->text);
				}

				// Cache the result for 15 minutes
				if (!isset($tweets->errors))
					Yii::app()->cache->set($this->theme . '_settings_tweets', $tweets, 900);
			}

			echo CJSON::encode($tweets);
		} catch (Exception $e) {
			echo CJSON::encode(array('errors' => array(array('message' => $e->getMessage()))));
		}
	}

	/**
     * Retrieves all categories to display int he footer
     * @return array $items     The CMenu Items we are going to return
     */
    public function getCategories()
    {
        $items = array(array('label' =>Yii::t('DefaultTheme',  'All Posts'), 'url' => Yii::app()->createUrl('/blog')));
        $categories = Yii::app()->cache->get('categories-listing');
        if ($categories == false)
        {
            $criteria = new CDbCriteria();
            $criteria->addCondition('type_id = 2')
                     ->addCondition('status = 1');
            $criteria->with = 'content';
            $criteria->select = 't.id, t.slug, t.name';

            $categories = Categories::model()->findAll($criteria);
            Yii::app()->cache->set('categories-listing', $categories);                          
        }
        
        foreach ($categories as $k=>$v)
        {
            if ($v['name'] != 'Uncategorized')
                $items[] = array('label' => $v->name, 'url' => Yii::app()->createUrl('/' . $v->slug));
        }
        
        return $items;
    }
    
    /**
     * Retrieves the recent post items so that the view is cleaned up
     * @return array $items     The CMenu items we are going to return
     */
    public function getRecentPosts()
    {
        $items = array();
        $content = Yii::app()->cache->get('content-listing');
        if ($content == false)
        {
            $criteria = Content::model()->getBaseCriteria()
                        ->addCondition('type_id = 2')
                        ->addCondition('password = ""');
            $criteria->order = 'published DESC';
            $criteria->limit = 5;

            $content = Content::model()->findAll($criteria);
            Yii::app()->cache->set('content-listing', $content);                            
        }
        
        foreach ($content as $v)
			$items[] = array('label' => $v->title, 'url' => Yii::app()->createAbsoluteUrl($v->slug), 'itemOptions' => array('id' => $v->id, 'published' => $v->published));
        
        return $items;
    }

    /**
	 * Retrieves related posts to a given post
	 */
	public function getRelatedPosts($id, $category_id)
	{
		$items = array();
        $criteria = Content::model()->getBaseCriteria()
                    ->addCondition('category_id = :category_id') 
                    ->addCondition('id != :id')
                    ->addCondition('type_id = 2')
                    ->addCondition('password = ""');
        $criteria->order = 'published DESC';
        $criteria->limit = 5;
        $criteria->params = array(':id' => $id, ':category_id' => $category_id);

        $related = Content::model()->findAll($criteria);
			
		 foreach ($related as $v)
		 	$items[] = array('label' => $v->title, 'url' => Yii::app()->createAbsoluteUrl($v->slug), 'itemOptions' => array('id' => $v->id, 'published' => $v->published));
        
        return $items;
	}
	
    /**
     * Retrieves the posts authored by a given user
     * @param  integer $id the id of the user
     * @return array of items
     */
    public function getPostsByAuthor($id=1)
    {
        $items = array();
        $criteria = Content::model()->getBaseCriteria()
                    ->addCondition('author_id = :author_id') 
                    ->addCondition('type_id = 2')
                    ->addCondition('password = ""');
        $criteria->order = 'published DESC';
        $criteria->limit = 5;
        $criteria->params = array(':author_id' => $id);

        $related = Content::model()->findAll($criteria);
            
        foreach ($related as $v)
            $items[] = array('label' => $v['title'], 'url' => Yii::app()->createAbsoluteUrl($v->slug), 'itemOptions' => array('id' => $v->id, 'published' => $v->published));
        
        return $items;
    }

    /**
     * Retrieves the CiiMenuItems from the configuration. If the items are not populated, then it 
     * builds them out from CiiMenu::$defaultItems
     */
    public function getMenu()
    {
        // Retrieve the item from cache since we're going to have to build this out manually
        $items = array();
        $fullRoutes = explode('|', $this->menu);
        foreach ($fullRoutes as $route)
        {
            if ($route == "")
                continue;
            $items[] = array('label' => ucwords(str_replace('-', ' ', $route)), 'url' => Yii::app()->createUrl('/' . $route), 'active' => false);
        }
        
        return $items;
    }

    /**
     * Retrieves the tags for a particular article and flattens them to a pretty array
     * @param  int $id     The content id
     * @return array
     */
    public function getContentTags($id)
    {
        $items = array();
        $tags = Content::model()->findByPk($id)->getTags();
        foreach ($tags as $item)
            $items[] = array('label' => $item, 'url' => $this->createUrl('/search?q=' . $item));

        return $items;
    }
}
