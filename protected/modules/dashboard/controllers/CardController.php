<?php

class CardController extends CiiDashboardController
{
	public function actionDelete($id)
	{

	}

	public function actionAdd($name=NULL)
	{
		Yii::import('application.modules.dashboard.cards.Weather.*');
		$card = new Weather;
		Cii::debug($card->create());
		echo "done";
		die();
	}
}