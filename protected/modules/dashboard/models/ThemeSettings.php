<?php

class ThemeSettings extends CiiSettingsModel
{
	protected $theme = 'default';

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
			'theme' => 'Theme',
			'mobileTheme' => 'Mobile Theme',
			'tabletTheme' => 'Tablet Theme',
		);
	}

	public function getThemes()
	{
		$themes = Yii::app()->cache->get('settings_themes');

		if ($themes == false)
		{
			$themes = array(
				'desktop',
				'mobile',
				'tablet'
			);

			$fileHelper = new CFileHelper;
			$files = $fileHelper->findFiles(Yii::getPathOfAlias('webroot.themes'), array('fileTypes'=>array('json'), 'level'=>1));

			foreach ($files as $file)
			{
				$theme = json_decode(file_get_contents($file), true);
				$themes[$theme['type']][] = $theme;
			}
			
			Yii::app()->cache->set('settings_themes', $themes);
		}

		return $themes;
	}

	public function getDesktopThemes()
	{
		$themes = $this->getThemes();
		return $themes['desktop'];
	}

	public function getTabletThemes()
	{
		$themes = $this->getThemes();
		return $themes['tablet'];
	}

	public function getMobileThemes()
	{
		$themes = $this->getThemes();
		return $themes['mobile'];
	}
}