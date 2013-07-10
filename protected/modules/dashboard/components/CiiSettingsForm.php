<?php
// Import bootstrap TBActiveForm
Yii::setPathOfAlias('bootstrap', Yii::getPathOfAlias('application.extensions.bootstrap'));
Yii::import('application.extensions.bootstrap.widgets.TbActiveForm'); 

/**
 * CiiSettingsForm is a CWidget that acts as a form builder based upon the information provided in Dashboard Settings Models
 * This class utilizes components from TbActiveForm
 */
class CiiSettingsForm extends CWidget
{
	/**
	 * Model used for form builder
	 * @var CFormModel $model
	 */
	public $model = NULL;

	/**
	 * Header information
	 * @var array of header data 
	 */
	public $header = array(
		'h3' => NULL,
		'p' => NULL,
		'show-save' => true,
		'save-text' => NULL,
		'save-icon' => NULL
	);

	/**
	 * Widget init function
	 * @see CActiveForm init()
	 */
	public function init()
	{
		$asset=Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('application.modules.dashboard.assets'), true, -1, YII_DEBUG);
		Yii::app()->getClientScript()->registerCssFile($asset.'/css/pure.css'); 

		return parent::init();
	}

	/**
	 * The following is run when the widget is called
	 */
	public function run()
	{
		// Use Reflection::getProperties(PROTECTED) to get protected properties from the passed model
		$reflection = new ReflectionClass($this->model);
		$properties = $reflection->getProperties(ReflectionProperty::IS_PROTECTED);

		if (count($properties) == 0)
			return;

		// Setup the form
		$form = $this->beginWidget('TbActiveForm', array(
		    'id'=>get_class($this->model),
		    'enableAjaxValidation'=>true,
		    'htmlOptions' => array(
		    	'class' => 'pure-form pure-form-aligned'
		    )
		));

		// Render out the header
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
				
				echo CHtml::submitButton('Save Changes', array('class' => 'pure-button pure-button-primary pull-right'));

			echo CHtml::closeTag('div');
		echo CHtml::closeTag('div');

		// Close the form
		$this->endWidget();
	}
}