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

		$this->render('form', array('model' => $model,'header' => array(
			'h3' => 'General Settings', 
			'p' => 'Set basic information about your site and change global settings.',
			'save-text' => 'Save Changes',
			'save-icon' => 'icon-plus'
		)));
	}
}