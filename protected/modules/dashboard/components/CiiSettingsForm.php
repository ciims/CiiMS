<?php
Yii::setPathOfAlias('bootstrap', Yii::getPathOfAlias('application.extensions.bootstrap'));
Yii::import('application.extensions.bootstrap.widgets.TbActiveForm'); 
class CiiSettingsForm extends TbActiveForm
{
	public $model = NULL;

	public function run()
	{
		$reflection = new ReflectionClass($this->model);
		$properties = $reflection->getProperties(ReflectionProperty::IS_PROTECTED);

		if (count($properties) == 0)
			return;

		$form = $this->beginWidget('TbActiveForm', array(
		    'id'=>get_class($this->model),
		    'enableAjaxValidation'=>true,
		));

		foreach ($properties as $property)
			echo $form->textFieldRow($this->model, $property->name);
		
		echo CHtml::submitButton();

		$this->endWidget();
	}
}