<?php

class AnalyticsController extends CiiSettingsController
{
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
                            'expression'=>'Yii::app()->user->role==6||Yii::app()->user->role==9'
                    ),
                    array('deny',  // deny all users
                            'users'=>array('*'),
                    ),
            );
    }
    
	public function actionIndex()
	{
		$this->render('index');
	}

}