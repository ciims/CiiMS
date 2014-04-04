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

		$this->render('theme', array('model' => $model, 'header' => array(
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

	/**
	 * Provides control for Theme management
	 * @param  string $type The type we want to display
	 */
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
		    CURLOPT_URL => 'https://raw.github.com/charlesportwoodii/CiiMS/latest-version/composer.json',
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

        CJSON::decode(curl_exec($curl));

		foreach ($issues as $issue)
		{
			echo CHtml::openTag('div', array('class' => 'pure-control-group'));
				echo CHtml::tag('label', array(), CiiInflector::titleize($issue['issue']));
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
	    Yii::app()->cache->flush();
        return unlink(Yii::getPathOfAlias('application.runtime').DS.'modules.config.php');
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