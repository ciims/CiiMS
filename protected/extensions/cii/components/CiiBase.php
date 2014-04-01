<?php

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

        // Load some specific CiiMS JS here
        $json = CJSON::encode(array(
            'email' =>  isset(Yii::app()->user->email) ? Yii::app()->user->email : NULL,
            'token' => isset(Yii::app()->user->api_key) ? Yii::app()->user->api_key : NULL,
            'role' => isset(Yii::app()->user->role) ? Yii::app()->user->role : NULL,
            'isAuthenticated' => isset(Yii::app()->user->id) ? true : false,
            'time' => time()
        ));
          
        Yii::app()->clientScript->registerScript('ciims', "
            $(document).ready(function() { localStorage.setItem('ciims', '$json'); });
        ");

        parent::init();
    }
}
