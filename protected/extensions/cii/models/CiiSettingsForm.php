<?php
Yii::import('application.extensions.cii.widgets.CiiActiveForm');
/**
 * CiiSettingsForm is a CWidget that acts as a form builder based upon the information provided in Dashboard Settings Models
 * This class utilizes components from TbActiveForm
 */
class CiiSettingsForm extends CWidget
{

	/**
	 * The action to use for CActiveForm
	 * @var CActiveForm::$action
	 */
	public $action = NULL;

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
		'save-text' => 'Save',
		'save-icon' => NULL
	);

	/**
	 * Whether or not the header should be displayed
	 * @var bool
	 */
	public $displayHeader = true;

	/**
	 * Protected properties from Reflection
	 * @var mixed
	 */
	private $properties = NULL;

	/**
	 * Widget init function
	 * @see CActiveForm init()
	 */
	public function init()
	{
		// Use Reflection::getProperties(PROTECTED) to get protected properties from the passed model
		$reflection = new ReflectionClass($this->model);
		$this->properties = $reflection->getProperties(ReflectionProperty::IS_PROTECTED);

		return parent::init();
	}

	/**
	 * The following is run when the widget is called
	 */
	public function run()
	{

		// Setup the form
		$form = $this->beginWidget('CiiActiveForm', array(
		    'id'=>get_class($this->model),
		    'enableAjaxValidation'=>true,
		    'action' => $this->action,
		    'htmlOptions' => array(
		    	'class' => 'pure-form pure-form-aligned'
		    )
		));

			// Main Content
			$this->renderMain($form);

		// Close the form
		$this->endWidget();
	}

	/**
	 * Renders the main body content
	 * @param  CActiveForm $form  The Form we're working with
	 */
	private function renderMain($form)
	{
		// #main .content
		echo CHtml::openTag('div', array('id' => 'main', 'class' => 'nano'));
			echo CHtml::openTag('div', array('class' => 'nano-content'));

				echo CHtml::openTag('fieldset');

					// If we want a custom form view, render that view instead of the default behavior
					if ($this->model->form !== NULL)
						$this->controller->renderPartial($this->model->form, array('model' => $this->model, 'properties' => $this->properties, 'form' => $form));
					else if (count($this->properties) == 0)
					{
						echo CHtml::tag('legend', array(), Yii::t('Dashboard.main', 'Change Theme Settings'));
						echo CHtml::tag('div', array('class' => 'alert alert-info'), Yii::t('Dashboard.main', 'There are no settings for this section.'));
					}
					else
					{
						$groups = $this->model->groups();

						if (!empty($groups))
						{
							foreach ($groups as $name=>$attributes)
							{
								echo CHtml::tag('legend', array(), $name);
								echo CHtml::tag('div', array('class' => 'clearfix'), NULL);
								foreach ($attributes as $property)
								{
									$p = new StdClass();
									$p->name = $property;
									$this->renderProperties($form, $p);
								}
							}
						}
						else
						{
							echo CHtml::tag('legend', array(), CiiInflector::titleize(get_class($this->model)));
							foreach ($this->properties as $property)
							{
								$this->renderProperties($form, $property);
							}
						}
					}

				echo CHtml::closeTag('div');
			echo CHtml::closeTag('div');
		echo CHtml::closeTag('div');
	}

	/**
	 * Renders form properties utilizing the appropriate
	 * @param  CActiveForm   $form     The form we're working with
	 * @param  Property Name $property The property name from Reflection
	 */
	private function renderProperties(&$form, $property)
	{
		$htmlOptions = array(
			'class' => 'pure-input-2-3'
		);

		$validators = $this->model->getValidators($property->name);
		$stringValidators = $this->model->getStringValidator($property->name, $validators);

		if (in_array('required', $stringValidators))
			$htmlOptions['required'] = true;

		echo CHtml::openTag('div', array('class' => 'pure-control-group'));

			if (in_array('boolean', $stringValidators))
				$form->toggleButtonRow($this->model, $property->name, $htmlOptions, $validators);
			else if (in_array('number', $stringValidators) && isset($validators[0]->max) && isset($validators[0]->min))
				$form->rangeFieldRow($this->model, $property->name, $htmlOptions, $validators);
			else if (in_array('number', $stringValidators) && (isset($validators[0]->max) || isset($validators[0]->min)))
				$form->numberFieldRow($this->model, $property->name, $htmlOptions, $validators);
			else if (in_array('password', $stringValidators))
				echo $form->passwordFieldRow($this->model, $property->name, $htmlOptions, $validators);
			else
				echo $form->textFieldRow($this->model, $property->name, $htmlOptions, $validators);

		echo CHtml::closeTag('div');
	}
}
