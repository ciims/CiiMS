<?php
Yii::setPathOfAlias('bootstrap', Yii::getPathOfAlias('application.extensions.bootstrap'));
Yii::import('application.extensions.bootstrap.widgets.TbActiveForm'); 
class CiiSettingsForm extends CWidget
{
	public $model = NULL;

	public $header = array(
		'h3' => NULL,
		'p' => NULL,
		'save-text' => 'Save Changes',
		'save-icon' => NULL
	);

	public function run()
	{
		$reflection = new ReflectionClass($this->model);
		$properties = $reflection->getProperties(ReflectionProperty::IS_PROTECTED);

		if (count($properties) == 0)
			return;

		$form = $this->beginWidget('TbActiveForm', array(
		    'id'=>get_class($this->model),
		    'enableAjaxValidation'=>true,
		    'htmlOptions' => array(
		    	'class' => 'pure-form pure-form-aligned'
		    )
		));

		// Header
		echo CHtml::openTag('div', array('class'=>'header'));
			echo CHtml::openTag('div', array('class'=>'pull-left'));
				echo CHtml::tag('h3', array(), $this->header['h3']);
				echo CHtml::tag('p', array(), $this->header['p']);
			echo CHtml::closeTag('div');

			echo CHtml::openTag('div', array('class'=>'pull-right'));
				echo CHtml::submitButton($this->header['save-text'], array('id' =>'header-button', 'class' => 'pure-button pure-button-primary pull-right'));
			echo CHtml::closeTag('div');

			echo CHtml::tag('div', array('class' => 'clearfix'), NULL);

		echo CHtml::closeTag('div');

		// #main .content
		echo CHtml::openTag('div', array('id' => 'main', 'class' => 'nano'));
			echo CHtml::openTag('div', array('class' => 'content'));
				
				foreach ($properties as $property)
				{
					echo CHtml::openTag('div', array('class' => 'pure-control-group'));
					echo $form->textFieldRow($this->model, $property->name, array('class' => 'pure-input-2-3'));
					echo CHtml::closeTag('div');
				}
				
				echo CHtml::submitButton('Save Changes', array('class' => 'pure-button pure-button-small pure-button-primary pull-right'));

			echo CHtml::closeTag('div');
		echo CHtml::closeTag('div');
		$this->endWidget();
	}
}