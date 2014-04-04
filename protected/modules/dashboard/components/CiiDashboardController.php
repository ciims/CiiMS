<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class CiiDashboardController extends CiiController
{
	/**
	 * Retrieve assetManager from anywhere without having to instatiate this code
	 * @return CAssetManager
	 */
	public function getAsset()
	{
		return Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('application.modules.dashboard.assets'), true, -1, YII_DEBUG);
	}

	/**
	 * Automatically bind some Javascript black magic!
	 * Since _nearly_ every page has a bunch of javascript, all javascript for a particular controller 
	 * is now wrapped up in modules.dashboard.assets.js.dashboard.<controller>.js
	 *
	 * This makes management of the code _very_ friendly and easy to handle. Additionally, it separates
	 * out the js and the php code
	 *
	 * This deliberately occurs afterRender because script order does matter for the dashboard. This really needs to be dead last
	 */
	protected function afterRender($view, &$output)
	{
		Yii::app()->clientScript->registerScriptFile($this->asset.'/js/dashboard/' . $this->id. '.js', CClientScript::POS_END);
		Yii::app()->clientScript->registerScript($this->id.'_'.$this->action->id, '$(document).ready(function() { CiiDashboard.'.CiiInflector::titleize($this->id).'.load'.CiiInflector::titleize($this->action->id).'(); });', CCLientScript::POS_END);
	}

	/**
	 * Before action method
	 * @param  CAction $action The aciton
	 * @return boolean
	 */
	public function beforeAction($action)
	{
		// Redirect to SSL if this is set in the dashboard
		if (!Yii::app()->getRequest()->isSecureConnection && Cii::getConfig('forceSecureSSL', false))
            $this->redirect('https://' . Yii::app()->getRequest()->serverName . Yii::app()->getRequest()->requestUri);

        return parent::beforeAction($action);
	}

	/**
	 * @return string[] action filters
	 */
	public function filters()
	{
		return array(
			'accessControl'
		);
	}

    /**
     * Handles errors
     */
    public function actionError()
    {
        if (Yii::app()->user->isGuest)
           return $this->redirect($this->createUrl('/login?next=' . Yii::app()->request->requestUri));

        if($error=Yii::app()->errorHandler->error)
        {
            if(Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', array('error' => $error));
        }
        else
            $this->redirect($this->createUrl('/error/403'));
    }

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow authenticated admins to perform any action
				'users'=>array('@'),
				'expression'=>'Yii::app()->user->role>=5'
			),
			array('deny',  // deny all users
				'users'=>array('*'),
				'deniedCallback' => array($this, 'actionError')
			),
		);
	}
    
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='main';
}