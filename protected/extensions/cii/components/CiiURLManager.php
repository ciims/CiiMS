<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This class provides functionality for a dynamic ruleset, allowing us to inject routing rules on the fly via the admin
 * panel rather than relying solely upon the main.php array
 *
 * PHP version 5
 *
 * MIT LICENSE Copyright (c) 2012-2013 Charles R. Portwood II
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom
 * the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE
 * FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @package    CiiMS Content Management System
 * @author     Charles R. Portwood II <charlesportwoodii@ethreal.net>
 * @copyright  Charles R. Portwood II <https://www.erianna.com> 2012-2013
 * @license    http://opensource.org/licenses/MIT  MIT LICENSE
 * @link       https://github.com/charlesportwoodii/CiiMS
 */

class CiiURLManager extends CUrlManager
{
	/**
	 * Whether or not we should cache url rules
	 * Override in main.php
	 * @var boolean
	 */
	public $cache = true;

	/**
	 * This is where our defaultRules are stored. This takes the place of the rules array in main.php
	 * This has been moved to here so that we can dynamically update rules without having to worry
	 * making sure the client updates their main.php file on updates.
	 * @var array
	 */
	public $defaultRules = array(
		'/sitemap.xml' 					        => '/site/sitemap',
		'/search/<page:\d+>' 			        => '/site/search',
		'/search' 						        => '/site/search',
		'/hybridauth/<provider:\w+>'	        => '/hybridauth',
		'/contact' 						        => '/site/contact',
		'/blog/<page:\d+>' 				        => '/content/list',
		'/' 							        => '/content/list',
		'/blog' 						        => '/content/list',
		'/activation/<id:\w+>' 	                => '/site/activation',
		'/activation' 					        => '/site/activation',
		'/emailchange/<key:\w+>'		        => '/site/emailchange',
		'/emailchange'					        => '/site/emailchange',
		'/resetpassword/<id:\w+>' 			    => '/site/resetpassword',
    '/resetpassword' 			            => '/site/resetpassword',
		'/forgot' 						        => '/site/forgot',
		'/register' 					        => '/site/register',
		'/register-success' 			        => '/site/registersuccess',
		'/login'						        => '/site/login',
		'/logout' 						        => '/site/logout',
		'/profile/edit'					        => '/profile/edit',
    '/profile/resend'					    => '/profile/resend',
		'/profile/<id:\w+>/<username:\w+>' 	=> '/profile/index',
		'/profile/<id:\w+>' 			        => '/profile/index',
		'/acceptinvite'					        => '/site/acceptinvite',
		'/acceptinvite/<id:\w+>'		        => '/site/acceptinvite',
		'/error/<code:\w+>' 			        => '/site/error'
	);

	/**
	 * Overrides processRules, allowing us to inject our own ruleset into the URL Manager
	 * Takes no parameters
	 **/
	protected function processRules()
	{
		$this->cache = !YII_DEBUG;
		// Generate the clientRules
		$this->rules = $this->cache ? Yii::app()->cache->get('CiiMS::Routes') : array();
		if ($this->rules == false || empty($this->rules))
		{
			$this->rules = array();
        	$this->rules = $this->generateClientRules();
        	$this->rules = CMap::mergearray($this->addRssRules(), $this->rules);
        	$this->rules = CMap::mergeArray($this->addModuleRules(), $this->rules);

        	Yii::app()->cache->set('CiiMS::Routes', $this->rules);
        }        

		// Append our cache rules BEFORE we run the defaults
		$this->rules['<controller:\w+>/<action:\w+>/<id:\d+>'] = '<controller>/<action>';
		$this->rules['<controller:\w+>/<action:\w+>'] = '<controller>/<action>';

        return parent::processRules();
	}

    /**
     * Adds rules from the module/config/routes.php file
     * @return
     */
    private function addModuleRules()
    {
    	// Load the routes from cache
        $moduleRoutes = array();
        $directories = glob(Yii::getPathOfAlias('application.modules') . '/*' , GLOB_ONLYDIR);

        foreach ($directories as $dir)
        {
            $routePath = $dir .DS. 'config' .DS. 'routes.php';
            if (file_exists($routePath))
            {
                $routes = require_once($routePath);
                // Unit tests are failing here for some reason
                if (!is_array($routes))
                    continue;

                foreach ($routes as $k=>$v)
                    $moduleRoutes[$k] = $v;
            }
        }

        return $moduleRoutes;
    }

    /**
     * Generates RSS rules for categories
     * @return array
     */
    private function addRSSRules()
   	{
   		$categories = Categories::model()->findAll();
   		foreach ($categories as $category)
   			$routes[$category->slug.'.rss'] = "categories/rss/id/{$category->id}";

   		$routes['blog.rss'] = '/categories/rss';
   		return $routes;
   	}

   	/**
   	 * Generates client rules, depending on if we want to handle rendering client side or server side
   	 * @return array
   	 */
   	private function generateClientRules()
   	{
   		$theme;
   		$themeName = Cii::getConfig('theme', 'default');
   		if (file_exists(Yii::getPathOfAlias('webroot.themes.') . DS . $themeName .  DS . 'Theme.php'))
        {
            Yii::import('webroot.themes.' . $themeName . '.Theme');
            $theme = new Theme;
    	}

    	// Generate the initial rules
		$rules = CMap::mergeArray($this->defaultRules, $this->rules);
    	
    	// If the Theme has requested to handle routing client side, allow it to do so
    	// Otherwise generate the URL rules for Yii to handle it
    	if ($theme->noRouting)
    		return $this->routeAllRulesToRoot();
    	else
    		return CMap::mergeArray($this->generateRules(), $rules);   	}

   	/**
   	 * Eraseses all the existing rules and remaps them to the index
   	 * @return array
   	 */
   	private function routeAllRulesToRoot()
   	{
   		$rules = $this->rules;
   		foreach ($rules as $k=>$v)
   			$rules[$k] = '/';

      $rules['/login'] = '/site/login';
   		return $rules;
   	}

   	/**
   	 * Wrapper function for generation of content rules and category rules
   	 * @return array
   	 */
   	private function generateRules()
   	{
   		return CMap::mergeArray($this->generateContentRules(), $this->generateCategoryRules());
   	}

   	/**
   	 * Generates content rules
   	 * @return array
   	 */
    private function generateContentRules()
    {
    	$rules = array();
    	$criteria = Content::model()->getBaseCriteria();
   		$content = Content::model()->findAll($criteria);
   		foreach ($content as $el)
   		{
   			if ($el->slug == NULL)
   				continue;

   			$pageRule = $el->slug.'/<page:\d+>';
   			$rule = $el->slug;

   			if ($el->slug == '/')
   				$pageRule = $rule = '';

   			$pageRule = $el->slug . '/<page:\d+>';
			$rule = $el->slug;

			$rules[$pageRule] = "content/index/id/{$el->id}/vid/{$el->vid}";
			$rules[$rule] = "content/index/id/{$el->id}/vid/{$el->vid}";
   		}

   		return $rules;
    }

    /**
   	 * Generates category rules
   	 * @return array
   	 */
    private function generateCategoryRules()
    {
    	$rules = array();
   		$categories = Categories::model()->findAll();
   		foreach ($categories as $el)
   		{
   			if ($el->slug == NULL)
   				continue;

   			$pageRule = $el->slug.'/<page:\d+>';
   			$rule = $el->slug;

   			if ($el->slug == '/')
   				$pageRule = $rule = '';

   			$pageRule = $el->slug . '/<page:\d+>';
			$rule = $el->slug;

			$rules[$pageRule] = "categories/index/id/{$el->id}";
			$rules[$rule] = "categories/index/id/{$el->id}";
   		}

   		return $rules;
    }
}
