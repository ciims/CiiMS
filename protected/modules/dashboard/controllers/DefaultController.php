<?php

class DefaultController extends CiiDashboardController
{
	public function actionIndex()
	{
		$this->render('index');
	}

	public function actionError()
    {
	    if($error=Yii::app()->errorHandler->error)
	    {
            if(Yii::app()->request->isAjaxRequest)
                    echo $error['message'];
            else 
                $this->render('error', $error);     
	    }
    }

}