<?php
/**
 * SlugURLManager
 * Extends CUrlManager, allowing database slugs to be provided in liue of boring urls
 **/
class SlugURLManager extends CUrlManager
{
	
	// Whether or not to perform database caching
	// Set to false in urlManager array in config/main to disable caching (useful for debugging).
	public $cache = true;
	
	// Content URL Set
	public $contentUrlRulesId;
	
	// Categories URL set
	public $categoriesUrlRulesId;
	
	
	public function __construct()
	{
		$this->contentUrlRulesId = 'WFF-content-url-rules';
		$this->categoriesUrlRulesId  = 'WFF-categories-url-rules';
	}
	
	/**
	 * Overrides processRules, allowing us to inject our own ruleset into the URL Manager
	 * Takes no parameters
	 **/
	protected function processRules()
	{
	
		$this->cacheRules('content', $this->contentUrlRulesId);
		$this->cacheRules('categories', $this->categoriesUrlRulesId);
		
		// Append our cache rules BEFORE we run the defaults
		$this->rules['<controller:\w+>/<action:\w+>/<id:\w+>'] = '<controller>/<action>';
		$this->rules['<controller:\w+>/<action:\w+>'] = '<controller>/<action>';
		
		parent::processRules();
	}
	
	/**
	 * Method for retrieving rules from the database and caching them
	 * @param $fromString - The string to be used in our FROM query
	 * @param $item - Address of the caching rule
	 * @does - Adds to the url rules and caches the result
	 **/
	private function cacheRules($fromString, &$item)
	{
		$urlRules = Yii::app()->cache->get($item);
		if($urlRules===false)
		{
		    $urlRules = Yii::app()->db->createCommand("SELECT id, slug FROM {$fromString}")->queryAll();
			
			if ($this->cache)
		    	Yii::app()->cache->set($item, $urlRules);
		}
		
		foreach ($urlRules as $route)
		{
			if ($route['slug'] == NULL)
				continue;
			
			$pageRule = $route['slug'] . '/<page:\d+>';
			$rule = $route['slug'];
			
			// Handle the case of the slug being just /
			if($route['slug'] == '/')
			{
				$pageRule = '';
				$rule = '';
			}
			
			$this->rules[$pageRule] = "{$fromString}/index/id/{$route['id']}";
			
			if ($fromString == 'categories')
			{
				$this->rules[$rule.'.rss'] = "content/rss/id/{$route['id']}";
			}
			
			$this->rules[$rule] = "{$fromString}/index/id/{$route['id']}";
			
		}
	}

}
?>
