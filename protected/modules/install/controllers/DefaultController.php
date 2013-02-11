<?php

class DefaultController extends CController
{
    /**
     * Error Action
     * The installer shouldn't error, if this happens, flat out die and blame the developer
     */
    public function actionError()
    {
        die('The requested action is not possible. Please press the back button to return to the previous page.');
    }
    
    public function actionIndex()
    {
        Cii::debug('HelloWorld!'); die();
    }
}