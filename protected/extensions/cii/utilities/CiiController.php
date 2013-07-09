<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class CiiController extends CController
{
    /**
     * @var array
     * Default items to populate CiiMenu With
     */
    public $defaultItems = array(
        array(
            'label' => 'Blog', 
            'url' => array('/blog'), 
            'active' => false),
        array(
            'label' => 'Admin', 
            'url' => array('/admin'),
            'active' => false),
    );
    
    /**
     * @var array the default params for any request
     * 
     */
    public $params = array(
        'meta'=>array(
            'keywords'=>'',
            'description'=>'',
        ),
        'data'=>array(
            'extract'=>''
        )
    );
    
    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout='//layouts/blog';
    
    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu=array();
    
    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs=array();

	/**
	 * Default filter prevents dynamic pages (pagination, etc...) from being cached
	 */
	public function filters()
    {
        return array(
            array(
                'CHttpCacheFilter',
                'cacheControl'=>'public, no-store, no-cache, must-revalidate',
            ),
        );
    }
    

    /**
     * Sets the layout for the view
     * @param $layout - Layout
     * @action - Sets the layout
     **/
    protected function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * Generic method for sending an email. Instead of having to call a bunch of code all over over the place
     * This method can be called which should be able to handle almost anything.
     *
     * By calling this method, the SMTP details will automatically be setup as well the notify email and user
     * 
     * @param  Users   $user          The User we are sending the email to
     * @param  string  $subject       The email Subject
     * @param  string  $viewFile      The view file we want to render. Generally this should be in the form //email/<file>
     *                                And should correspond to a viewfile in /themes/<theme>/views/email/<file>
     * @param  array   $content       The content to pass to renderPartial()
     * @param  boolean $return        Whether the output should be returned. The default is TRUE since this output will be passed to MsgHTML
     * @param  boolean $processOutput Whether the output should be processed. The default is TRUE since this output will be passed to MsgHTML
     * @return boolean                Whether or not the email sent sucessfully
     */
    protected function sendEmail(Users $user, $subject = "", $viewFile, $content = array(), $return = true, $processOutput = true)
    {
        Yii::import('application.extensions.phpmailer.JPhpMailer');
        $mail = new JPhpMailer;
        $mail->IsSMTP();

        $smtpHost    = Cii::getConfig('SMTPHost',    NULL);
        $smtpPort    = Cii::getConfig('SMTPPort',    NULL);
        $smtpUser    = Cii::getConfig('SMTPUser',    NULL);
        $smtpPass    = Cii::getConfig('SMTPPass',    NULL);

        $notifyUser  = new stdClass;
        $notifyUser->email       = Cii::getConfig('notifyEmail', NULL);
        $notifyUser->displayName = Cii::getConfig('notifyName',  NULL);

        if ($smtpHost !== NULL)
            $mail->Host       = $smtpHost->value; 

        if ($smtpPort !== NULL)
            $mail->Port       = $smtpPort->value;

        if ($smtpUser !== NULL)                    
            $mail->Username   = $smtpUser->value; 

        if ($smtpPass !== NULL)
            $mail->Password   = $smtpPass->value;      

        if ($notifyUser->email == NULL && $notifyUser->displayName == NULL)
            $notifyUser = Users::model()->findByPk(1);

        $mail->SetFrom($notifyUser->email, $notifyUser->displayName);
        $mail->Subject = $subject;
        $mail->MsgHTML($this->renderPartial($viewFile, $content, $return, $processOutput));
        $mail->AddAddress($user->email, $user->displayName);

        try {
            return $mail->Send();
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * Sets the application language for the site based upon $_POST, $_SESSION, http headers
     * @return string   string for translations
     */
    private function setApplicationLanguage()
    {
        $app = Yii::app();
        if (Cii::get($_POST, '_lang', false))
            $app->language = $app->session['_lang'] = $_POST['_lang'];
        else if (Cii::get($app->session, '_lang', false))
            $app->language = $app->session['_lang'];
        else
            $app->language = $app->session['_lang'] = Yii::app()->getRequest()->getPreferredLanguage();

        return $app->language;
    }

    /**
     * BeforeAction method
     * The events defined here occur before every controller action that extends CiiController occurs.
     * This method will run the following tasks:
     *     - Attempt to update NewRelic if it is enabled
     *     - Prevent access to the site if it is in offline mode
     *     - Set the language for i18n
     *     - Apply the correct theme
     * @param  CAction $action The details of the action we want to run
     * @return CController::beforeAction($action)
     */
	public function beforeAction($action)
	{
        // Attempt to contact NewRelic with Reporting Data
        try {
            @Yii::app()->newRelic->setTransactionName($this->id, $action->id);
        } catch (Exception $e) {}

        $this->setApplicationLanguage();

        $offlineMode = (bool)Cii::getConfig('offline', false);

        if ($offlineMode)
        {
            if ($this->id == "site")
            {
                if (!in_array($action->id, array('login', 'logout', 'error', 'sitemap', 'migrate')))
                    throw new CHttpException(403, 'This site is currently disabled. Please check back later.');
            }
            else if (isset($this->module) && $this->module->getName() == "dashboard")
                $nop;
            else
                throw new CHttpException(403, 'This site is currently disabled. Please check back later.');
        }

	    header('Content-type: text/html; charset=utf-8');

        $theme = Cii::getConfig('theme', 'default');

        Yii::import('ext.mobile_detect.*');

        // Allow for mobile devices to have a separate theme
        if (MobileDetect::isMobileDevice())
        {
            $mobileTheme = Cii::getConfig('mobileTheme');
            if ($mobileTheme !== NULL)
                $theme = $mobileTheme;
        }

        // Allow for tablet devices to have a separate theme from desktop and mobile
        if (MobileDetect::isTabletDevice())
        {
            $tabletTheme = Cii::getConfig('tabletTheme');
            if ($tabletTheme !== NULL)
                $theme = $tabletTheme;
        }

		Yii::app()->setTheme(file_exists(YiiBase::getPathOfAlias('webroot.themes.' . $theme)) ? $theme : 'default');

        return parent::beforeAction($action);
	}
	
    /**
     * Retrieves keywords for use in the viewfile
     */
    public function getKeywords()
    {
        $keywords = Cii::get($this->params['meta'], 'keywords', '');
        if (Cii::get($keywords, 'value', false) != false)
            $keywords = implode(',', json_decode($keywords['value']));
            
        return $keywords == "" ? Cii::get($this->params['data'], 'title', Cii::getConfig('name', Yii::app()->name)): $keywords;
    }
		
	/**
	 * Overloaded Render allows us to generate dynamic content and to provide compression
     * @param string $view      The viewfile we want to render
     * @param array $data       The data that is passed to us from $this->render()
     * @param bool $return      Whether or not we should return the data as a variable or echo it.
	 **/
	public function render($view, $data=null, $return=false)
	{
	    if($this->beforeRender($view))
	    {
	    	$this->params['meta'] = Cii::get($data, 'meta', array());
            if (empty($this->params['meta']))
                $data['meta'] = array();

	    	if (isset($data['data']) && is_object($data['data']))
	    		$this->params['data'] = $data['data']->attributes;
	    	
    		$output=$this->renderPartial($view,$data,true);
            
    		if(($layoutFile=$this->getLayoutFile($this->layout))!==false)
    		    $output=$this->renderFile($layoutFile,array('content'=>$output, 'meta'=>$this->params['meta']),true);
    
    		$this->afterRender($view,$output);
            
    		$output = $this->processOutput($output);
            $config = Yii::app()->getComponents(false);

            if (isset($config['clientScript']->compressHTML) && $config['clientScript']->compressHTML == true)
            {
                Yii::import('ext.contentCompactor.*');
                $compactor = new ContentCompactor();
                $output = $compactor->compact($output, array());
            }
    		
    		if($return)
    		    return $output;
    		else
    		    echo $output;
	    }
	}

    /**
     * Retrieves all categories to display int he footer
     * @return array $items     The CMenu Items we are going to return
     */
    public function getCategories()
    {
        $items = array(array('label' => 'All Posts', 'url' => $this->createUrl('/blog')));
        $categories = Yii::app()->cache->get('categories-listing');
        if ($categories == false)
        {
            $categories = Yii::app()->db->createCommand('SELECT categories.id AS id, categories.name AS name, categories.slug AS slug, COUNT(DISTINCT(content.id)) AS content_count FROM categories LEFT JOIN content ON categories.id = content.category_id WHERE content.type_id = 2 AND content.status = 1 GROUP BY categories.id')->queryAll();
            Yii::app()->cache->set('categories-listing', $categories);                          
        }
        
        foreach ($categories as $k=>$v)
        {
            if ($v['name'] != 'Uncategorized')
                $items[] = array('label' => $v['name'], 'url' => $this->createUrl('/' . $v['slug']));
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
            $content = Yii::app()->db->createCommand('SELECT content.id, title, content.created,  content.slug AS content_slug, 
            												 categories.slug AS category_slug, 
            												 categories.name AS category_name, 
            												 comment_count, content.created 
            										  FROM content LEFT JOIN categories ON content.category_id = categories.id 
            										  WHERE vid = (
            										  	SELECT MAX(vid) 
            										  	FROM content AS content2 
            										  	WHERE content2.id = content.id
													  ) 
													  AND type_id = 2 AND status = 1 
                                                      AND password=""
            										  ORDER BY content.created DESC LIMIT 5')->queryAll();
            Yii::app()->cache->set('content-listing', $content);                            
        }
        
        foreach ($content as $k=>$v)
			$items[] = array('label' => $v['title'], 'url' => $this->createUrl('/' . $v['content_slug']), 'itemOptions' => array('id' => Cii::get($v, 'id', 1), 'created' => $v['created']));
        
        return $items;
    }
    
	/**
	 * Gets tags for a content for CMenu
	 * @return array $items
	 */
	public function getContentTags()
	{
		$items = array();
		$tags = Content::model()->findByPk($this->params['data']['id'])->getTags();
		foreach ($tags as $item)
			$items[] = array('label' => $item, 'url' => $this->createUrl('/search?q=' . $item));
		
		return $items;
	}
	
	/**
	 * Retrieves related posts to a given post
	 */
	public function getRelatedPosts()
	{
		$items = array();
		$related = Yii::app()->db->createCommand('SELECT content.id, title, slug, content.created
												  FROM content  WHERE status = 1 AND category_id = :category_id 
												  AND id != :id AND vid = (
												  	SELECT MAX(vid) 
												  	FROM content AS content2 
												  	WHERE content2.id = content.id) 
												  AND password="" 
												  ORDER BY updated DESC LIMIT 5')
								 ->bindParam(':category_id', $this->params['data']['category_id'])
								 ->bindParam(':id', $this->params['data']['id'])
		 						 ->queryAll();
			
		 foreach ($related as $v)
		 	$items[] = array('label' => $v['title'], 'url' => $this->createUrl('/' . $v['slug']), 'itemOptions' => array('id' => Cii::get($v, 'id', 1), 'created' => $v['created']));
        
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
        $related = Yii::app()->db->createCommand('SELECT content.id, title, content.created,  content.slug AS slug, 
                                                             categories.slug AS category_slug, 
                                                             categories.name AS category_name, 
                                                             comment_count, content.created 
                                                      FROM content LEFT JOIN categories ON content.category_id = categories.id 
                                                      WHERE vid = (
                                                        SELECT MAX(vid) 
                                                        FROM content AS content2 
                                                        WHERE content2.id = content.id
                                                      ) 
                                                      AND type_id = 2 AND status = 1 
                                                      AND password=""
                                                      AND content.author_id = :author_id
                                                      ORDER BY content.created DESC LIMIT 5')
                                 ->bindParam(':author_id', $id)
                                 ->queryAll();
            
         foreach ($related as $v)
            $items[] = array('label' => $v['title'], 'url' => $this->createUrl('/' . $v['slug']), 'itemOptions' => array('id' => Cii::get($v, 'id', 1), 'created' => $v['created']));
        
        return $items;
    }

    /**
     * Retrieves the CiiMenuItems from the configuration. If the items are not populated, then it 
     * builds them out from CiiMenu::$defaultItems
     */
    public function getCiiMenu()
    {
        // Retrieve the item from cache since we're going to have to build this out manually
        $items = Yii::app()->cache->get('CiiMenuItems');
        if ($items === false)
        {
            // Get the menu items from Configuration
            $menuRoutes = Cii::getConfig('menu', '');
            
            // If the configuration is not provided, then set this to our defualt items
            if ($menuRoutes == NULL)
                $items = $this->defaultItems;
            else
            {
                $fullRoutes = explode('|', $menuRoutes);
                foreach ($fullRoutes as $route)
                {
                    if ($route == "")
                        continue;
                    $items[] = array('label' => ucwords(str_replace('-', ' ', $route)), 'url' => Yii::app()->createUrl('/' . $route), 'active' => false);
                }
            }
            Yii::app()->cache->set('CiiMenuItems', $items, 3600);
        }
        
        return $items;
    }
}