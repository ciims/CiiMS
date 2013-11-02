<?php

class EventController extends ApiController
{
	public function accessRules()
	{
		return array(
			array('allow')
		);
	}

	public function actionTrackEvent()
	{
		$event = new Events;
		$event->attributes = $_GET;

		if ($event->save())
			return $event->getApiAttributes();

		return $this->returnError(400, NULL, $model->getErrors());
	}
}