<?php

/**
 * This controller provides basic settings control and management for all basic CiiMS settings
 *
 * Actions in this controller should take advantage of CiiSettingsModel and classes extended from it
 * for automatic form construction, management, and validation
 */
class SettingsController extends CiiSettingsController
{
	/**
	 * Provides "general" settings control
	 * @class GeneralSettings
	 */
	public function actionIndex()
	{
		$model = new GeneralSettings;
		
		$this->submitPost($model);

		$this->render('form', array('model' => $model, 'header' => array(
			'h3' => 'General Settings', 
			'p' => 'Set basic information about your site and change global settings.',
			'save-text' => 'Save Changes'
		)));
	}

	/**
	 * Provides basic email control
	 * @class EmailSettings
	 */
	public function actionEmail()
	{
		$model = new EmailSettings;
		
		$this->submitPost($model);

		$this->render('form', array('model' => $model, 'header' => array(
			'h3' => 'Email Settings', 
			'p' => 'Configure and verify how CiiMS sends emails',
			'save-text' => 'Save Changes'
		)));
	}

	public function actionEmailTest()
	{
		if (Cii::get($_POST, 'email') !== NULL)
		{
			// Verify that the email is valid
			if (filter_var(Cii::get($_POST, 'email'), FILTER_VALIDATE_EMAIL))
			{
				// Create a user object to pass to the sender
				$user = new StdClass();
				$user->displayName = NULL;
				$user->email = Cii::get($_POST, 'email');

				// Send the test email
				$response = $this->sendEmail($user, 'CiiMS Test Email', 'application.modules.dashboard.views.email.test');

				echo $response;
				Yii::app()->end();
			}

			return false;
		}

		return false;
	}

	private function submitPost($model)
	{
		if (Cii::get($_POST, get_class($model)) !== NULL)
		{
			$model->populate($_POST);

			if ($model->save())
				Yii::app()->user->setFlash('success', 'Your settings have been updated.');
		}
	}
}