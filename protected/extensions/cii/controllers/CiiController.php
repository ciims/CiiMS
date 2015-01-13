<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class CiiController extends CController
{
    public $theme = NULL;

    /*
     * Retrieves the asset manager for the theme
     * @return Published AssetManager path
     */    
    public function getAsset()
    {
        $theme = $this->getTheme();
        return Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('webroot.themes.' . $theme  . '.assets'), false, -1, YII_DEBUG);
    }

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
     * Verifies that our request does not produce duplicate content (/about == /content/index/2), and prevents direct access to the controller
     * protecting it from possible attacks.
     * @param $id   - The content ID we want to verify before proceeding
     **/
    protected function beforeCiiAction($id=NULL)
    {
        // If we do not have an ID, consider it to be null, and throw a 404 error
        if ($id == NULL)
            throw new CHttpException(404, Yii::t('ciims.core', 'The specified post cannot be found.'));
        
        // Retrieve the HTTP Request
        $r = new CHttpRequest();
        
        // Retrieve what the actual URI
        $requestUri = str_replace($r->baseUrl, '', $r->requestUri);
        
        // Retrieve the route
        $route = '/' . $this->getRoute() . '/' . $id;
        $requestUri = preg_replace('/\?(.*)/','',$requestUri);
        
        // If the route and the uri are the same, then a direct access attempt was made, and we need to block access to the controller
        if ($requestUri == $route)
            throw new CHttpException(404, Yii::t('ciims.core', 'The requested post cannot be found.'));
        
        return str_replace($r->baseUrl, '', $r->requestUri);
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
     * @deprecated, @todo: Deprecate usage of this method
     * @see models/settings/EmailSettings::send
     * @return boolean
     */
    public function sendEmail($user, $subject = "", $viewFile, $content = array(), $return = true, $processOutput = true, $debug=false)
    {
        Yii::log(Yii::t('ciims.core', 'Use of CiiController::sendEmail is deprecated, and will be dropped in a future version. Use EmailSettings::send instead'), 'warning', 'ciims.core');
        $email = new EmailSettings;
        return $email->send($user, $subject, $viewFile, $content, $return, $processOutput, YII_DEBUG);
    }

    /**
     * BeforeAction method
     * The events defined here occur before every controller action that extends CiiController occurs.
     * This method will run the following tasks:
     *     - Prevent access to the site if it is in offline mode
     *     - Set the language for i18n
     *     - Apply the correct theme
     * @param  CAction $action The details of the action we want to run
     * @return CController::beforeAction($action)
     */
	public function beforeAction($action)
	{
        try {
            @Yii::app()->newRelic->setAppName(Cii::getConfig('name', Yii::app()->name));
            @Yii::app()->newRelic->setTransactionName($this->id, $action->id);
        } catch (Exception $e) {}

        // Sets the application language
        Cii::setApplicationLanguage();

        // Handles the offline state
        $this->handleOfflineMode((bool)Cii::getConfig('offline', false), $action);

        // Sets the global theme for CiiMS
        $this->getTheme();

        return parent::beforeAction($action);
	}
	
    /**
     * Retrieves the appropriate theme
     * @return string $theme
     */
    public function getTheme()
    {
        $theme = Cii::getConfig('theme', 'default');
        Yii::app()->setTheme(file_exists(YiiBase::getPathOfAlias('webroot.themes.' . $theme)) ? $theme : 'default');
        return $theme;
    }

    /**
     * Handles being on offline mode
     * @param string theme The current theme
     * @param CAction $action
     */
    private function handleOfflineMode($isOffline=false, &$action)
    {
        if (!$isOffline)
            return;

        if ($isOffline && $this->id == 'site')
        {
            if (!in_array($action->id, array('login', 'logout', 'error', 'sitemap', 'migrate')))
                throw new CHttpException(403, Yii::t('ciims.controllers.Cii', 'This site is currently disabled. Please check back later.'));
        }
        else if (!(isset($this->module) && $this->module->getName() == "dashboard"))
            throw new CHttpException(403, Yii::t('ciims.controllers.Cii', 'This site is currently disabled. Please check back later.'));

        return;
    }

    /**
     * Determines if the currently loaded route is in a module or not
     * @return boolean
     */
    private function isInModule()
    {
        return isset(Yii::app()->controller->module);
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
	 * Overloaded Render allows us to generate dynamic content
     * @param string $view      The viewfile we want to render
     * @param array $data       The data that is passed to us from $this->render()
     * @param bool $return      Whether or not we should return the data as a variable or echo it.
	 **/
	public function render($view, $data=null, $return=false)
	{
	    if($this->beforeRender($view))
	    {
            if (empty($this->params['meta']))
                $data['meta'] = array();

	    	if (isset($data['data']) && is_object($data['data']))
	    		$this->params['data'] = $data['data']->attributes;

            if (!$this->isInModule() && file_exists(Yii::getPathOfAlias('webroot.themes.') . DS . Yii::app()->theme->name .  DS . 'Theme.php'))
            {
                Yii::import('webroot.themes.' . Yii::app()->theme->name . '.Theme');
                $this->theme = new Theme;
	    	}
                        
    		$output=$this->renderPartial($view,$data,true);

    		if(($layoutFile=$this->getLayoutFile($this->layout))!==false)
            {
                // Render the Comment functionality automatically
                if (!$this->isInModule())
                    $this->widget('ext.cii.widgets.comments.CiiCommentMaster', array('type' => Cii::getCommentProvider(), 'content' => isset($data['data']) && is_a($data['data'], 'Content') ? $data['data']->attributes : false));

    		    $output=$this->renderFile($layoutFile,array('content'=>$output, 'meta'=>$this->params['meta']),true);
            }
    
    		$this->afterRender($view,$output);
            
    		$output = $this->processOutput($output);

    		if($return)
    		    return $output;
    		else
    		    echo $output;
	    }
	}
}
