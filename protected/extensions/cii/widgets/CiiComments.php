<?php

class CiiComments extends CWidget
{
	public function init()
	{
		Yii::app()->controller->widget('ext.cii.widgets.CiiAddThisWidget');
	}
}