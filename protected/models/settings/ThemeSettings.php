<?php

class ThemeSettings extends CiiSettingsModel
{
	/**
	 * The active theme
	 * @var string
	 */
	public $theme = 'default';

	/**
	 * Validation rules for the theme
	 * @return array
	 */
	public function rules()
	{
		return array(
			array('theme', 'required'),
			array('theme', 'length', 'max' => 255)
		);
	}

	/**
	 * Attribute labels for themes
	 * @return array
	 */
	public function attributeLabels()
	{
		return array(
			'theme' => Yii::t('ciims.models.theme', 'Theme'),
		);
	}

	/**
	 * Returns the active theme name
	 * @return strings
	 */
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
			$themes = array();
			$currentTheme = Cii::getConfig('theme');

			$directories = glob(Yii::getPathOfAlias('webroot.themes') . DIRECTORY_SEPARATOR . "*", GLOB_ONLYDIR);

			// Pushes the current theme onto the top of the list
			foreach ($directories as $k=>$dir)
			{
				if ($dir == Yii::getPathOfAlias('webroot.themes').DS.$currentTheme)
				{
					unset($directories[$k]);
					break;
				}
			}

			array_unshift($directories, Yii::getPathOfAlias('webroot.themes').DS.$currentTheme);

	        foreach($directories as $dir)
	        {
	            $json = CJSON::decode(file_get_contents($dir . DIRECTORY_SEPARATOR . 'composer.json'));
	            $name = $json['name'];
	            $key = str_replace('ciims-themes/', '', $name);
	            $themes[$key] = array(
	                'path' => $dir,
	                'name' => $name,
	            );
	        }

	        return $themes;

			Yii::app()->cache->set('settings_themes', $themes);
		}

		return $themes;
	}
}