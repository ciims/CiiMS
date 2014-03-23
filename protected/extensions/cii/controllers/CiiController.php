<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class CiiController extends CController
{    
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
    public function sendEmail($user, $subject = "", $viewFile, $content = array(), $return = true, $processOutput = true)
    {
        Yii::import('application.extensions.phpmailer.JPhpMailer');
        $mail = new JPhpMailer;
        $mail->IsSMTP();
        $mail->SMTPAuth = false;

        $smtpHost    = Cii::getConfig('SMTPHost',    NULL);
        $smtpPort    = Cii::getConfig('SMTPPort',    NULL);
        $smtpUser    = Cii::getConfig('SMTPUser',    NULL);
        $smtpPass    = Cii::getConfig('SMTPPass',    NULL);

        $notifyUser  = new stdClass;
        $notifyUser->email       = Cii::getConfig('notifyEmail', NULL);
        $notifyUser->displayName = Cii::getConfig('notifyName',  NULL);

        if ($smtpHost !== NULL && $smtpHost !== "")
            $mail->Host       = $smtpHost; 

        if ($smtpPort !== NULL && $smtpPort !== "")
            $mail->Port       = $smtpPort;

        if ($smtpUser !== NULL && $smtpUser !== "")
        {               
            $mail->Username   = $smtpUser; 
            $mail->SMTPAuth = true;
        }

        if ($smtpPass !== NULL && $smtpPass !== "" && Cii::decrypt($smtpPass) != "")
        {
            $mail->Password   = Cii::decrypt($smtpPass);
            $mail->SMTPAuth = true;
        }

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
        header('Content-type: text/html; charset=utf-8');

        // Attempt to contact NewRelic with Reporting Data
        try {
            @Yii::app()->newRelic->setTransactionName($this->id, $action->id);
        } catch (Exception $e) {}

        Cii::setApplicationLanguage();

        $offlineMode = (bool)Cii::getConfig('offline', false);

        if ($offlineMode)
        {
            if ($this->id == "site")
            {
                if (!in_array($action->id, array('login', 'logout', 'error', 'sitemap', 'migrate')))
                    throw new CHttpException(403, Yii::t('ciims.controllers.Cii', 'This site is currently disabled. Please check back later.'));
            }
            else if (isset($this->module) && $this->module->getName() == "dashboard")
                $nop = 1;
            else
                throw new CHttpException(403, Yii::t('ciims.controllers.Cii', 'This site is currently disabled. Please check back later.'));
        }

        $theme = $this->getTheme();

		Yii::app()->setTheme(file_exists(YiiBase::getPathOfAlias('webroot.themes.' . $theme)) ? $theme : 'default');

        return parent::beforeAction($action);
	}
	
    /**
     * Retrieves the appropriate theme
     * @return string $theme
     */
    public function getTheme()
    {
        $theme = Cii::getConfig('theme', 'default');

        // Allow for mobile devices to have a separate theme
        if (MobileDetect::isMobileDevice() && !Mobiledetect::isTabletDevice())
        {
            $mobileTheme = Cii::getConfig('mobileTheme');
            if ($mobileTheme != NULL && $mobileTheme != "")
                $theme = $mobileTheme;
        }

        // Allow for tablet devices to have a separate theme from desktop and mobile
        if (MobileDetect::isTabletDevice())
        {
            $tabletTheme = Cii::getConfig('tabletTheme');
            if ($tabletTheme != NULL && $tabletTheme != "")
                $theme = $tabletTheme;
        }

        return $theme;
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

            if (file_exists(Yii::getPathOfAlias('webroot.themes.') . DIRECTORY_SEPARATOR . Yii::app()->theme->name .  DIRECTORY_SEPARATOR . 'Theme.php'))
            {
                Yii::import('webroot.themes.' . Yii::app()->theme->name . '.Theme');
                $data['theme'] = $this->params['theme'] = new Theme;
	    	}
            
    		$output=$this->renderPartial($view,$data,true);
            
    		if(($layoutFile=$this->getLayoutFile($this->layout))!==false)
            {
                // Render AddThis
                if ($this->layout == 'blog')
                    $this->widget('ext.cii.widgets.CiiAddThisWidget');

                // Render the Comment functionality automatically
                if (Cii::getConfig('useDisqusComments'))
                    $this->widget('ext.cii.widgets.CiiDisqusComments', array('content' => isset($data['data']) && is_a($data['data'], 'Content') ? $data['data']->attributes : false));
                else
                    $this->widget('ext.cii.widgets.CiiComments', array('content' => isset($data['data']) && is_a($data['data'], 'Content') ? $data['data']->attributes : false));

    		    $output=$this->renderFile($layoutFile,array('content'=>$output, 'meta'=>$this->params['meta']),true);
            }
    
    		$this->afterRender($view,$output);
            
    		$output = $this->processOutput($output);
            $config = Yii::app()->getComponents(false);
    		
    		if($return)
    		    return $output;
    		else
    		    echo $output;
	    }
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
}