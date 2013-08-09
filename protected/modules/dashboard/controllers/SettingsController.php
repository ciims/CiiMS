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

	/**
	 * Provides "social" settings control
	 * @class GeneralSettings
	 */
	public function actionSocial()
	{
		$model = new SocialSettings;
		
		$this->submitPost($model);

		$this->render('form', array('model' => $model, 'header' => array(
			'h3' => 'Social Settings', 
			'p' => 'Provide Credentials for accessing and submitting data to various third party social media sites.',
			'save-text' => 'Save Changes'
		)));
	}

	/**
	 * Provides "general" settings control
	 * @class GeneralSettings
	 */
	public function actionAnalytics()
	{
		$model = new AnalyticsSettings;
		
		$this->submitPost($model);

		$this->render('form', array('model' => $model, 'header' => array(
			'h3' => 'Analytics Settings', 
			'p' => 'Enable and configure various Analytics providers',
			'save-text' => 'Save Changes'
		)));
	}

	/**
	 * Provides theme control settings
	 * @class ThemeSettings
	 */
	public function actionAppearance()
	{
		$model = new ThemeSettings;
		
		$this->submitPost($model);

		$this->render('form', array('model' => $model, 'header' => array(
			'h3' => 'Appearance', 
			'p' => 'Change the site theme for desktop, tablet, and mobile',
			'save-text' => 'Save Theme'
		)));
	}

	/**
	 * Provides card management saettings
	 */
	public function actionCards()
	{
		$criteria = new CDbCriteria;
		$criteria->addSearchCondition('t.key', 'dashboard_card_%', false);
		$cards = Configuration::model()->findAll($criteria);

		$this->render('cards', array(
			'header' => array(
				'h3' => 'Manage Dashboard Cards',
				'p' => 'Manage and add new dashboard cards'
			),
			'cards' => $cards
		));
	}

	/**
	 * This is a temporary way to add and install new cards via Github.
	 *
	 * Once CiiMS.org is setup, this functionality will be deprecated in favor of a download from CiiMS.org
	 * @return boolean   If the download, extraction, and install was successful
	 */
	public function actionAddCard()
	{
		// Only proceed on POST
		if (Cii::get($_POST, 'Card') !== NULL)
		{
			$repository = Cii::get($_POST['Card'], 'new');

			if ($repository == NULL)
				return false;

			$repoInfo = explode('/', $repository);

			// Download the Card information from Github via cURL
			$curl = curl_init();
			curl_setopt_array($curl, array(
			    CURLOPT_RETURNTRANSFER => 1,
			    CURLOPT_URL => 'https://raw.github.com/' . $repository . '/master/card.json',
			));

			$json = CJSON::decode(curl_exec($curl));

			// If we have an invalid repo - abort
			if ($json == NULL)
				throw new CHttpException(400, 'Unable to find valid card at that location');

			$config = new Configuration;

			$uuid = $config->generateUniqueId();

			$config->key = 'dashboard_card_' . $uuid;
			$config->value = CJSON::encode(array(
				'name'  =>  Cii::get(Cii::get($json, 'name'), 'displayName'),
				'class' => Cii::get(Cii::get($json, 'name'), 'name'),
				'path' => 'application.runtime.cards.' . $uuid . '.' . $repoInfo[1] .'-master',
				'folderName' => $uuid
			));

			// Determine the runtime directory
			$runtimeDirectory = Yii::getPathOfAlias('application.runtime');
			$downloadPath = $runtimeDirectory . DIRECTORY_SEPARATOR . 'cards' . DIRECTORY_SEPARATOR . $uuid . '.zip';
			if (!is_writable($runtimeDirectory))
				throw new CHttpException(500, 'Runtime directory is not writable');

			$targetFile = fopen($downloadPath, 'w' );

            // Initiate the CURL request
            $ch = curl_init('https://github.com/' . $repository . '/archive/master.zip');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_FILE, $targetFile);
            curl_exec($ch);
            
            // Extract the file
            $zip = new ZipArchive;
            $res = $zip->open($downloadPath);

            // If we can open the file
            if ($res === true)
            {
            	// Extract it to the appropriate location
            	$extraction = $zip->extractTo(str_replace('.zip', '', $downloadPath));

            	// If we can extract it
            	if ($extraction)
            	{
            		// Save the config in the database
            		$config->save();

            		// Delete the zip file
            		unlink($downloadPath);

            		// Flush the cache
	            	Yii::app()->cache->delete('dashboard_cards_available');
	            	Yii::app()->cache->delete('cards_in_category');

	            	header('Content-Type: application/json');
	            	
	            	// Output the json so we can display pretty stuff in the main view
	            	echo $config->value;

	            	// And return true
	            	return true;
	            }
            }
		}

		return false;
	}

	/**
	 * Deletes a card and all associated files from the system
	 * @param  string $id The id of the card we want to delete
	 * @return boolean    If the card was deleted or not
	 */
	public function actionDeleteCard($id=NULL)
	{
		if ($id == NULL)
			throw new CHttpException(400, 'You must specify a card to delete');

		$card = Configuration::model()->findByAttributes(array('key' => $id));

		if ($card == NULL)
			throw new CHttpException(400, 'There are no dashboard cards with that id');

		$card->value = CJSON::decode($card->value);
		
		return $card->fullDelete($card->value['folderName']);
	}

	/**
	 * Plugin Management 
	 */
	public function actionPlugins()
	{
		throw new CHttpException(400, 'Plugins are not yet supported');
	}
	
	/**
	 * System Management
	 */
	public function actionSystem()
	{
		$this->render('system', array('header' => array(
			'h3' => 'System Information',
			'p' => 'View system and diagnostic information'
		)));
	}

	/**
	 * Retrieves any issues with the present CiiMS instance.
	 *
	 * This checks several things
	 * 1) The current CiiMS version as compared to the installed version
	 * 2) Whether or there are missing migrations
	 * 3) If there are any permissions problems
	 */
	public function actionGetIssues()
	{
		$issues = array();
		
		// Check CiiMS version
		$curl = curl_init();
		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_URL => 'https://raw.github.com/charlesportwoodii/CiiMS/latest-version/protected/extensions/cii/ciims.json',
		));

		$json = CJSON::decode(curl_exec($curl));
		if ($json['version'] >= Cii::getVersion())
			$issues[] = array('issue' => 'version', 'message' => 'CiiMS is out of date. Please update to the latest version (' . CHtml::link($json['version'], 'https://github.com/charlesportwoodii/CiiMS/tree/latest-version/', array('target' => '_blank')) . ')');

		// Check if migrations have been run
		$migrations = Yii::app()->db->createCommand('SELECT COUNT(*) as count FROM tbl_migration')->queryScalar();
		$fileHelper = new CFileHelper;
		$files = count($fileHelper->findFiles(Yii::getPathOfAlias('application.migrations'), array('fileTypes'=>array('php'), 'level'=>1)));

		if ($migrations < $files)
			$issues[] = array('issue' => 'migrations', 'message' => 'CiiMS\' database is out of date. Please run yiic migrate up to migrate your database to the latest version.');

		// Check common permission problems
		if (!is_writable(Yii::getPathOfAlias('webroot.uploads')))
			$issues[] = array('issue' => 'permssions', 'message' => 'Your uploads folder (' . Yii::getPathOfAlias('webroot.uploads') . ') is not writable. Please change the permissions on the folder to be writable');

		if (!is_writable(Yii::getPathOfAlias('application.runtime')))
			$issues[] = array('issue' => 'permssions', 'message' => 'Your runtime folder (' . Yii::getPathOfAlias('application.runtime') . ') is not writable. Please change the permissions on the folder to be writable');

		if (count($issues) == 0)
			echo CHtml::tag('div', array('class' => 'alert in alert-block fade alert-success'), 'There are no issues with your system. =)');
		else
			echo CHtml::tag('div', array('class' => 'alert in alert-block fade alert-error'), 'Please address the following issues.');

		foreach ($issues as $issue)
		{
			echo CHtml::openTag('div', array('class' => 'pure-control-group'));
				echo CHtml::tag('label', array(), Cii::titleize($issue['issue']));
				echo CHtml::tag('span', array('class' => 'inline'), $issue['message']);
			echo CHtml::closeTag('div');
		}

		return;		
	}

	/**
	 * Flushes the Yii::cache.
	 * @return bool    If the cache flush was successful or not
	 */
	public function actionFlushCache()
	{
		return Yii::app()->cache->flush();
	}

	/**
	 * Provides functionality to send a test email
	 */
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
		}

		return false;
	}

	/**
	 * Generic handler for sacing $model data since the model is completely generic.
	 * @param  CiiSettingsModel $model The model we are working with
	 */
	private function submitPost(&$model)
	{
		if (Cii::get($_POST, get_class($model)) !== NULL)
		{
			$model->populate($_POST);

			if ($model->save())
				Yii::app()->user->setFlash('success', 'Your settings have been updated.');
			else
				Yii::app()->user->setFlash('error', 'There was an error saving your settings.');
		}
	}
}