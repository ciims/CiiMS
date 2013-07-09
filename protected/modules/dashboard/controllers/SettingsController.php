<?php

class SettingsController extends CiiSettingsController
{
	public function actionIndex()
	{
		$model = new GeneralSettings;
		
		if (Cii::get($_POST, 'GeneralSettings') !== NULL)
		{
			$model->attributes = Cii::get($_POST, 'GeneralSettings');

			if ($model->save())
				Yii::app()->user->setFlash('success', 'Your settings have been updated.');
		}

		$this->render('index', array('model' => $model));
	}
}