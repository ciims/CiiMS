<?php

class DefaultController extends CiiDashboardController
{
	public function actionIndex()
	{
		$this->render('index');
	}

}