<?php

class SettingsController extends CiiSettingsController
{
	public function actionIndex()
	{
		$model = new GeneralSettings;
		
		if (Cii::get($_POST, 'GeneralSettings') !== NULL)
		{
			$model->populate($_POST);

			if ($model->save())
				Yii::app()->user->setFlash('success', 'Your settings have been updated.');
		}

		$this->render('form', array('model' => $model, 'header' => array(
			'h3' => 'General Settings', 
			'p' => 'Set basic information about your site and change global settings.',
			'save-text' => 'Save Changes'
		)));
	}

	public function actionEmail()
	{
		$model = new EmailSettings;
		
		if (Cii::get($_POST, 'EmailSettings') !== NULL)
		{
			$model->populate($_POST);

			if ($model->save())
				Yii::app()->user->setFlash('success', 'Your settings have been updated.');
		}

		$this->render('form', array('model' => $model, 'header' => array(
			'h3' => 'Email Settings', 
			'p' => 'Configure and verify how CiiMS sends emails',
			'save-text' => 'Save Changes'
		)));
	}
}