<?php

/**
 * This controller provides basic settings control and management for all basic CiiMS settings
 *
 * Actions in this controller should take advantage of CiiSettingsModel and classes extended from it
 * for automatic form construction, management, and validation
 */
class SettingsController extends CiiSettingsController
{
	public $themeType = 'desktop';

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
    
	/**
	 * Provides "general" settings control
	 * @class GeneralSettings
	 */
	public function actionIndex()
	{
		$model = new GeneralSettings;
		
		$this->submitPost($model);

		$this->render('form', array('model' => $model, 'header' => array(
			'h3' =>  Yii::t('Dashboard.main', 'General Settings'), 
			'p' =>  Yii::t('Dashboard.main', 'Set basic information about your site and change global settings.'),
			'save-text' =>  Yii::t('Dashboard.main', 'Save Changes')
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
			'h3' =>  Yii::t('Dashboard.main', 'Email Settings'), 
			'p' =>  Yii::t('Dashboard.main', 'Configure and verify how CiiMS sends emails.'),
			'save-text' =>  Yii::t('Dashboard.main', 'Save Changes')
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
			'h3' =>  Yii::t('Dashboard.main', 'Social Settings'), 
			'p' =>  Yii::t('Dashboard.main', 'Provide Credentials for accessing and submitting data to various third party social media sites.'),
			'save-text' =>  Yii::t('Dashboard.main', 'Save Changes')
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
			'h3' =>  Yii::t('Dashboard.main', 'Analytics Settings'), 
			'p' =>  Yii::t('Dashboard.main', 'Enable and configure various Analytics providers (more coming soon!).'),
			'save-text' =>  Yii::t('Dashboard.main', 'Save Changes')
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
			'h3' =>  Yii::t('Dashboard.main', 'Appearance'),
			'p' => Yii::t('Dashboard.main',  'Change the site theme for desktop, tablet, and mobile.'),
			'save-text' =>  Yii::t('Dashboard.main', 'Save Theme')
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
				'h3' =>  Yii::t('Dashboard.main', 'Manage Dashboard Cards'),
				'p' =>  Yii::t('Dashboard.main', 'Manage and add new dashboard cards.')
			),
			'cards' => $cards
		));
	}

	public function actionTheme($type='desktop')
	{
		$theme = null;
		if ($type == 'desktop')
			$theme = Cii::getConfig('theme', 'default');
		else if ($type == 'mobile')
			$theme = Cii::getConfig('mobileTheme');
		else if ($type == 'tablet')
			$theme = Cii::getConfig('tabletTheme');
		else
			$theme = Cii::getConfig('theme', 'default');

		$this->themeType = $type;

		if (!file_exists(Yii::getPathOfAlias('webroot.themes.' . $theme) . DIRECTORY_SEPARATOR . 'Theme.php'))
			throw new CHttpException(400, Yii::t('Dashboard.main',  'The requested theme type is not set. Please set a theme before attempting to change theme settings'));

		Yii::import('webroot.themes.' . $theme . '.Theme');

		try {
			$model = new Theme();
		} catch(Exception $e) {
			throw new CHttpException(400,  Yii::t('Dashboard.main', 'The requested theme type is not set. Please set a theme before attempting to change theme settings'));
		}
		
		$this->submitPost($model);

		$this->render('form', array('model' => $model, 'header' => array(
			'h3' =>  Yii::t('Dashboard.main', 'Theme Settings'), 
			'p' =>  Yii::t('Dashboard.main', 'Change optional parameters for a given theme.'),
			'save-text' =>  Yii::t('Dashboard.main', 'Save Changes')
		)));
	}

	/**
	 * This is a temporary way to add and install new cards via Github.
	 *
	 * Once CiiMS.org is setup, this functionality will be deprecated in favor of a download from CiiMS.org
	 * @return boolean   If the download, extraction, and install was successful
	 */
	public function actionAddTheme()
	{
		// Only proceed on POST
		if (Cii::get($_POST, 'Theme') !== NULL)
		{
			Yii::app()->cache->delete('settings_themes');

			$repository = Cii::get($_POST['Theme'], 'new');
			$repo = explode('/', $repository);

			if ($repository == NULL)
				return false;

			$repoInfo = explode('/', $repository);

			// Download the Card information from Github via cURL
			$curl = curl_init();
			curl_setopt_array($curl, array(
			    CURLOPT_RETURNTRANSFER => true,
			    CURLOPT_FOLLOWLOCATION => true,
			    CURLOPT_URL => 'https://raw.github.com/' . $repository . '/master/theme.json',
			    CURLOPT_CAINFO => Yii::app()->basePath . '/config/certs/DigiCertHighAssuranceEVRootCA.crt'
			));

			$json = CJSON::decode(curl_exec($curl));
			curl_close($curl);

			// If we have an invalid repo - abort
			if ($json == NULL)
				throw new CHttpException(400,  Yii::t('Dashboard.main', 'Unable to find valid theme at that location.'));

			if (file_exists(Yii::getPathOfAlias($json['folder'])))
				throw new CHttpException(400, Yii::t('Dashboard.main', 'A theme with that name already exist. Unable to install theme.'));

			// Determine the runtime directory
			$themesDirectory = Yii::getPathOfAlias('webroot.themes');
			$downloadPath = $themesDirectory . DIRECTORY_SEPARATOR . str_replace('webroot.themes.', '', $json['folder']) . "-tmp.zip";
			if (!is_writable($themesDirectory))
				throw new CHttpException(500,  Yii::t('Dashboard.main', 'Themes directory is not writable'));

			$targetFile = fopen($downloadPath, 'w' );

            // Initiate the CURL request
            $curl = curl_init();
			curl_setopt_array($curl, array(
			    CURLOPT_RETURNTRANSFER => true,
			    CURLOPT_FOLLOWLOCATION => true,
			    CURLOPT_URL => 'https://github.com/' . $repository . '/archive/master.zip',
			    CURLOPT_CAINFO => Yii::app()->basePath . '/config/certs/DigiCertHighAssuranceEVRootCA.crt',
			    CURLOPT_FILE => $targetFile
			));
			curl_exec($curl);
			curl_close($curl);
            
            // Extract the file
            $zip = new ZipArchive;
            $res = $zip->open($downloadPath);

            // If we can open the file
            if ($res === true)
            {
            	// Extract it to the appropriate location
            	$extractedDir = str_replace('.zip', '', $downloadPath);
            	$extraction = $zip->extractTo($extractedDir);

            	// If we can extract it
            	if ($extraction)
            	{
            		$finalDir = Yii::getPathOfAlias('webroot.themes') . DIRECTORY_SEPARATOR . str_replace('webroot.themes.', '', $json['folder']);
            		$tempDir  = $extractedDir . DIRECTORY_SEPARATOR . $repo[1] . '-master';
            		rename($tempDir, $finalDir);
            		
            		CiiFileDeleter::removeDirectory($extractedDir);

            		// Delete the zip file
            		unlink($downloadPath);

            		header('Content-Type: application/json');

            		echo CJSON::encode(array('theme' => str_replace('webroot.themes.', '', $json['folder']), 'type' => $json['type']));

	            	// And return true
	            	return true;
	            }
            }
		}

		return false;
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
			    CURLOPT_RETURNTRANSFER => true,
			    CURLOPT_FOLLOWLOCATION => true,
			    CURLOPT_URL => 'https://raw.github.com/' . $repository . '/master/card.json',
			    CURLOPT_CAINFO => Yii::app()->basePath . '/config/certs/DigiCertHighAssuranceEVRootCA.crt',
			));

			$json = CJSON::decode(curl_exec($curl));
			curl_close($curl);

			// If we have an invalid repo - abort
			if ($json == NULL)
				throw new CHttpException(400,  Yii::t('Dashboard.main', 'Unable to find valid card at that location.'));

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
				throw new CHttpException(500,  Yii::t('Dashboard.main', 'Runtime directory is not writable'));

			$targetFile = fopen($downloadPath, 'w' );

            $curl = curl_init();
			curl_setopt_array($curl, array(
			    CURLOPT_RETURNTRANSFER => true,
			    CURLOPT_FOLLOWLOCATION => true,
			    CURLOPT_URL => 'https://github.com/' . $repository . '/archive/master.zip',
			    CURLOPT_CAINFO => Yii::app()->basePath . '/config/certs/DigiCertHighAssuranceEVRootCA.crt',
			    CURLOPT_FILE => $targetFile
			));

			curl_exec($curl);
			curl_close($curl);            
            
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
			throw new CHttpException(400,  Yii::t('Dashboard.main', 'You must specify a card to delete'));

		$card = Configuration::model()->findByAttributes(array('key' => $id));

		if ($card == NULL)
			throw new CHttpException(400,  Yii::t('Dashboard.main', 'There are no dashboard cards with that id'));

		$card->value = CJSON::decode($card->value);
				
		return $card->fullDelete($card->value['folderName']);
	}

	/**
	 * Plugin Management 
	 */
	public function actionPlugins()
	{
		throw new CHttpException(400,  Yii::t('Dashboard.main', 'Plugins are not yet supported'));
	}
	
	/**
	 * System Management
	 */
	public function actionSystem()
	{
		$this->render('system', array('header' => array(
			'h3' =>  Yii::t('Dashboard.main', 'System Information'),
			'p' =>  Yii::t('Dashboard.main', 'View system and diagnostic information.')
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
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_FOLLOWLOCATION => true,
		    CURLOPT_URL => 'https://raw.github.com/charlesportwoodii/CiiMS/latest-version/extensions/cii/ciims.json',
		    CURLOPT_CAINFO => Yii::app()->basePath . '/config/certs/DigiCertHighAssuranceEVRootCA.crt',
		));

		$json = CJSON::decode(curl_exec($curl));
		curl_close($curl);
		if ($json['version'] > Cii::getVersion())
			$issues[] = array('issue' => 'version', 'message' =>  Yii::t('Dashboard.main', 'CiiMS is out of date. Please update to the latest version ({{version}})', array('{{version}}' => CHtml::link($json['version'], 'https://github.com/charlesportwoodii/CiiMS/tree/latest-version/', array('target' => '_blank')))));

		// Check if migrations have been run
		$migrations = Yii::app()->db->createCommand('SELECT COUNT(*) as count FROM tbl_migration')->queryScalar();
		$fileHelper = new CFileHelper;
		$files = count($fileHelper->findFiles(Yii::getPathOfAlias('application.migrations'), array('fileTypes'=>array('php'), 'level'=>1)));

		if ($migrations < $files)
			$issues[] = array('issue' => 'migrations', 'message' =>  Yii::t('Dashboard.main', "CiiMS' database is out of date. Please run yiic migrate up to migrate your database to the latest version."));

		// Check common permission problems
		if (!is_writable(Yii::getPathOfAlias('webroot.uploads')))
			$issues[] = array('issue' => 'permssions', 'message' =>  Yii::t('Dashboard.main', 'Your uploads folder ({{folder}}) is not writable. Please change the permissions on the folder to be writable', array('{{folder}}' => Yii::getPathOfAlias('webroot.uploads'))));

		if (!is_writable(Yii::getPathOfAlias('application.runtime')))
			$issues[] = array('issue' => 'permssions', 'message' =>  Yii::t('Dashboard.main', 'Your runtime folder ({{folder}}) is not writable. Please change the permissions on the folder to be writable', array('{{folder}}' => Yii::getPathOfAlias('application.runtime'))));

		if (count($issues) == 0)
			echo CHtml::tag('div', array('class' => 'alert in alert-block fade alert-success'),  Yii::t('Dashboard.main', 'There are no issues with your system. =)'));
		else
			echo CHtml::tag('div', array('class' => 'alert in alert-block fade alert-error'),  Yii::t('Dashboard.main', 'Please address the following issues.'));

        // Send Notifications to CiiMS.org with updated information
        unset($curl);

        $curl = curl_init();
        $data = array(
            'name' => Cii::getConfig('name'),
            'url' => Yii::app()->getBaseUrl(true)
        );

        curl_setopt_array($curl, array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_POSTFIELDS => CJSON::encode($data),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen(CJSON::encode($data)),
                    'X-Auth-ID: ' .  Configuration::model()->findByAttributes(array('key' => 'instance_id'))->value,
                    'X-Auth-Token: ' .  Configuration::model()->findByAttributes(array('key' => 'token'))->value,
                ),
                CURLOPT_URL => 'https://www.ciims.org/customize/default/registration',
                CURLOPT_CAINFO => Yii::getPathOfAlias('application.config.certs') . DIRECTORY_SEPARATOR . 'GeoTrustGlobalCA.cer'
        ));

        // TODO: Do something with this?
        $response = CJSON::decode(curl_exec($curl));

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
				$response = $this->sendEmail($user,  Yii::t('Dashboard.email', 'CiiMS Test Email'), 'application.modules.dashboard.views.email.test');

				// Send an appropriate status code if sending the email fails
				if (!$response)
					header('HTTP/1.1 502 Failed to connect to SMTP Server.');

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
				Yii::app()->user->setFlash('success',  Yii::t('Dashboard.main', 'Your settings have been updated.'));
			else
				Yii::app()->user->setFlash('error',  Yii::t('Dashboard.main', 'There was an error saving your settings.'));
		}
	}
}
