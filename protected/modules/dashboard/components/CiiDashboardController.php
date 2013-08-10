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
	 * out the js and the php
	 * 
	 * @param  CAction $action The action we are dealing with
	 * @see CiiController::beforeAction()
	 */
	public function beforeAction($action)
	{
		Yii::app()->clientScript->registerScriptFile($this->asset.'/js/dashboard/' . $this->id. '.js', CClientScript::POS_END);
		Yii::app()->clientScript->registerScript($this->id.'_'.$action->id, 'CiiDashboard.'.Cii::titleize($this->id).'.load'.Cii::titleize($action->id).'();', CCLientScript::POS_END);

		return parent::beforeAction($action);
	}

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl'
		);
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
			),
		);
	}
	
	/**
	 * Params... for?
	 * @var array
	 */
	public $params = array();
    
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='main';
}