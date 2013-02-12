<?php

class DefaultController extends CController
{
    public $layout = 'main';
    
    public $breadcrumbs = array(
        
    );
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
        $this->render('index');
    }
}