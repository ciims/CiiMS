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

        $this->loadUserInfo();

        parent::init();
    }

    /**
     * Loads the user information for the dashboard and CiiMSComments to use
     */
    private function loadUserInfo()
    {
        if (isset(Yii::app()->user))
        {
            // Load some specific CiiMS JS here
            $json = CJSON::encode(array(
                'email' =>  Cii::get(Yii::app()->user, 'email'),
                'token' => Cii::get(Yii::app()->user, 'api_key'),
                'role' => Cii::get(Yii::app()->user, 'role'),
                'isAuthenticated' => isset(Yii::app()->user->id),
                'time' => time()
            ));
              
            Yii::app()->clientScript->registerScript('ciims', "
                $(document).ready(function() { localStorage.setItem('ciims', '$json'); });
            ");
        }
    }
}
