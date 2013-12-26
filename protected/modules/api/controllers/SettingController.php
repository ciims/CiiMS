<?php

Yii::import('application.modules.dashboard.components.CiiSettingsModel');
Yii::import('application.modules.dashboard.models.*');
class SettingController extends ApiController
{
	/**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {   
        return array(
            array('allow',
                'expression' => '$user!=NULL&&($user->user_role==6||$user->user_role==9)'
            ),
            array('deny') 
        );  
    }

    /**
	 * [GET] [/api/setting]
	 * @class GeneralSettings
	 */
	public function actionIndex()
	{
		$model = new GeneralSettings;
		return $this->getModelAttributes($model);
	}

	/**
	 * [POST] [/api/setting]
	 * @class GeneralSettings
	 */
	public function actionIndexPost()
	{
		$model = new GeneralSettings;
		return $this->loadData($_POST, $model);
	}

	/**
	 * [GET] [/api/setting/email]
	 * @class EmailSettings
	 */
	public function actionEmail()
	{
		$model = new EmailSettings;
		return $this->getModelAttributes($model);
	}

	/**
	 * [POST] [/api/setting/email]
	 * @class EmailSettings
	 */
	public function actionEmailPost()
	{
		$model = new EmailSettings;
		return $this->loadData($_POST, $model);
	}

	/**
	 * [GET] [/api/setting/social]
	 * @class SocialSettings
	 */
	public function actionSocial()
	{
		$model = new SocialSettings;
		return $this->getModelAttributes($model);
	}

	/**
	 * [POST] [/api/setting/social]
	 * @class SocialSettings
	 */
	public function actionSocialPost()
	{
		$model = new SocialSettings;
		return $this->loadData($_POST, $model);
	}

	/**
	 * [GET] [/api/setting/analytics]
	 * @class AnalyticsSettings
	 */
	public function actionAnalytics()
	{
		$model = new AnalyticsSettings;
		return $this->getModelAttributes($model);
	}

	/**
	 * [POST] [/api/setting/analytics]
	 * @class AnalyticsSettings
	 */
	public function actionAnalyticsPost()
	{
		$model = new AnalyticsSettings;
		return $this->loadData($_POST, $model);
	}

	/**
	 * [GET] [/api/setting/appearance]
	 * @class ThemeSettings
	 */
	public function actionAppearance()
	{
		$model = new ThemeSettings;
		return $this->getModelAttributes($model);
	}

	/**
	 * [GET] [/api/setting/appearance]
	 * @class ThemeSettings
	 */
	public function actionAppearancePost()
	{
		$model = new ThemeSettings;
		return $this->loadData($_POST, $model);
	}

	/**
	 * [GET] [/api/setting/theme]
	 * @class Theme
	 */
	public function actionTheme($type='desktop')
	{
		$model = $this->getTheme($type);
		return $this->getModelAttributes($model);
	}

	/**
	 * [POST] [/api/setting/theme]
	 * @class Theme
	 */
	public function actionThemePost($type='desktop')
	{
		$model = $this->getThemeAttributes($type);
		return $this->loadData($_POST, $model);
	}

	/**
	 * Retrieves the appropriate model for the theme
	 * @param  string $type The data type to load
	 * @return CiiThemeModel
	 */
	private function getThemeAttributes($type)
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

		if (!file_exists(Yii::getPathOfAlias('webroot.themes.' . $theme) . DIRECTORY_SEPARATOR . 'Theme.php'))
			throw new CHttpException(400, Yii::t('Api.setting',  'The requested theme type is not set. Please set a theme before attempting to change theme settings.'));

		Yii::import('webroot.themes.' . $theme . '.Theme');

		try {
			$model = new Theme();
		} catch(Exception $e) {
			throw new CHttpException(400,  Yii::t('Api.setting', 'The requested theme type is not set. Please set a theme before attempting to change theme settings.'));
		}

		return $model;
	}

	/**
	 * Populates and saves model attributes
	 * @param  $_POST $post            $_POST data
	 * @param  CiiSettingsModel $model The model we want to populate
	 * @return array                   The saved model attributes or an error message
	 */
	private function loadData($post, &$model)
	{
		$model->populate($_POST, true);

		if ($model->save())
			return $this->getModelAttributes($model);

		return $this->returnError(400, NULL, $model->getErrors());
	}

	/**
	 * Retrieves model attributes for a particular model
	 * @param  CiiSettingsModel $model The model we want to query against
	 * @return array
	 */
	private function getModelAttributes(&$model)
	{
		$response = array();
		$reflection = new ReflectionClass($model);
		$properties = $reflection->getProperties(ReflectionProperty::IS_PROTECTED);

		foreach ($properties as $property)
			$response[$property->name] = $model[$property->name];

		return $response;
	}
}