<?php

require __DIR__.DS.'vendor'.DS.'autoload.php';
Yii::import('application.modules.dashboard.components.CiiSettingsModel');
class Theme extends CiiThemesModel
{
	// @var string  The theme name
	public $theme = 'default';
}
