<?php

class CiiBase extends CApplicationComponent
{
    public function init()
    {
        // Register the cii path alias.
        if (Yii::getPathOfAlias('cii') === false)
            Yii::setPathOfAlias('cii', realpath(dirname(__FILE__) . '/..'));
        
        // Register the CiiUtilities
        Yii::import('ext.cii.utilities.*');
        
        parent::init();
    }
}
