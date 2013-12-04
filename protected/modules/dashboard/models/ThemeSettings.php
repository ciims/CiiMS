<?php

class ThemeSettings extends CiiSettingsModel
{
	public $theme = 'default';

	protected $mobileTheme = NULL;

	protected $tabletTheme = NULL;

	public $form = 'application.modules.dashboard.views.settings.theme';

	public function rules()
	{
		return array(
			array('theme', 'required'),
			array('theme, mobileTheme, tabletTheme', 'length', 'max' => 255)
		);
	}

	public function attributeLabels()
	{
		return array(
			'theme' => Yii::t('Dashboard.models-theme', 'Theme'),
			'mobileTheme' => Yii::t('Dashboard.models-theme', 'Mobile Theme'),
			'tabletTheme' => Yii::t('Dashboard.models-theme', 'Tablet Theme'),
		);
	}

	public function getTheme()
	{
		return $this->theme;
	}

	/**
	 * Retrieves all of the themes from webroot.themes and returns them in an array group by type, each containing
	 * the contents of theme.json. 
	 *
	 * The themes are then cached for easy retrieval later. (I really hate unecessary DiskIO if something isn't changing...)
	 * 
	 * @return array
	 */
	public function getThemes()
	{
		$themes = Yii::app()->cache->get('settings_themes');

		if ($themes == false)
		{
			$themes = array(
				'desktop' => array(),
				'mobile' => array(),
				'tablet' => array()
			);

			$fileHelper = new CFileHelper;
			$files = $fileHelper->findFiles(Yii::getPathOfAlias('webroot.themes'), array('fileTypes'=>array('json'), 'level'=>1));

			foreach ($files as $file)
			{
				if (strpos($file,'theme.json') === false)
					continue;
				$theme = json_decode(file_get_contents($file), true);
				$themes[$theme['type']][] = $theme;
			}

			Yii::app()->cache->set('settings_themes', $themes);
		}

		return $themes;
	}
}