<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class CiiDashboardController extends CiiController
{
	
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
	
	public $params = array();
    
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='main';
}