<?php
/**
 * CiiBase
 * Preloads all the necessary YII components
 */
class CiiBase extends CApplicationComponent
{
    public function init()
    {
        // Register the cii path alias.
        if (Yii::getPathOfAlias('cii') === false)
            Yii::setPathOfAlias('cii', realpath(dirname(__FILE__) . '/..'));
        
        // Register all of Cii
        Yii::import('ext.cii.utilities.*');
        Yii::import('ext.cii.cache.*');
        Yii::import('ext.cii.controllers.*');
        Yii::import('ext.cii.models.*');

        Cii::loadUserInfo();
	$this->registerJqueryCore();
        parent::init();
    }

    private function registerJqueryCore()
    {
	$cs = Yii::app()->clientScript;	
	$cs->scriptMap = array(
		'jquery.js' => Yii::app()->assetManager->publish(Yii::getPathOfAlias('ext.cii.assets').'/js/jquery-2.0.3.min.js')
	);
	$cs->registerCoreScript('jquery'); 
    }
}
