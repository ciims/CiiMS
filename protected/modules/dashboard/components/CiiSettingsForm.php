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
		if (count($this->properties) == 0 && $this->model->preContentView !== NULL)
		{
			$this->controller->renderPartial($this->model->preContentView, array('model' => $this->model, 'properties' => $this->properties));
			return;
		}

		// Setup the form
		$form = $this->beginWidget('CiiActiveForm', array(
		    'id'=>get_class($this->model),
		    'enableAjaxValidation'=>true,
		    'action' => $this->action,
		    'htmlOptions' => array(
		    	'class' => 'pure-form pure-form-aligned'
		    )
		));

			// Header
			if ($this->displayHeader)
				$this->renderHeader($form);

			// Before Content View
			// CActiveForm elements should not be used so that they are not submitted
			if ($this->model->preContentView !== NULL)
				$this->controller->renderPartial($this->model->preContentView, array('model' => $this->model, 'properties' => $this->properties));

			// Main Content
			$this->renderMain($form);

		// Close the form
		$this->endWidget();
	}

	/**
	 * Renders the header
	 * @param  CActiveForm $form The Form we're working with
	 */
	private function renderHeader($form)
	{		
		// Render out the header
		echo CHtml::openTag('div', array('class'=>'header'));
			echo CHtml::openTag('div', array('class'=>'pull-left'));
				echo CHtml::tag('p', array(), $this->header['h3']);
			echo CHtml::closeTag('div');

			echo CHtml::openTag('div', array('class'=>'pull-right'));
				echo CHtml::tag('button', array('id' =>'header-button', 'escape' => false, 'class' => 'pure-button pure-button-primary pure-button-small'), CHtml::tag('i', array('class' => 'fa fa-spinner fa-spin icon-spinner-form2', 'style' => 'display: none'), NULL) . $this->header['save-text']);
			echo CHtml::closeTag('div');

			echo CHtml::tag('div', array('class' => 'clearfix'), NULL);
		echo CHtml::closeTag('div');

		$this->widget('cii.widgets.CiiAlert', array(
	              'block'=>true,
	              'fade'=>true,
	              'closeText'=>'×',
	         ));
	}

	/**
	 * Renders the main body content
	 * @param  CActiveForm $form  The Form we're working with
	 */
	private function renderMain($form)
	{
		// #main .content
		echo CHtml::openTag('div', array('id' => 'main', 'class' => 'nano'));
			echo CHtml::openTag('div', array('class' => 'content'));

				echo CHtml::openTag('fieldset');

					// If we want a custom form view, render that view instead of the default behavior
					if ($this->model->form !== NULL)
						$this->controller->renderPartial($this->model->form, array('model' => $this->model, 'properties' => $this->properties, 'form' => $form));
					else if (count($this->properties) == 0 && $this->model->preContentView == NULL)
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
						
						echo CHtml::tag('button', array('id' =>'header-button', 'escape' => false, 'class' => 'pure-button pure-button-primary pure-button-small pull-right'), CHtml::tag('i', array('class' => 'fa fa-spinner fa-spin icon-spinner icon-spin icon-spinner-form2', 'style' => 'display: none'), NULL) . $this->header['save-text']);
					}

				echo CHtml::closeTag('div');
			echo CHtml::closeTag('div');
		echo CHtml::closeTag('div');

		Yii::app()->getClientScript()->registerScript('change', '
			$("input:not([no-field-change=\'true\']").on("input onpropertychange change", function() {

				try {
					$(".icon-spinner-form2").fadeIn();
					clearTimeout(timeout);
				} catch (e) {}
				
				timeout = setTimeout(function() {

					var values = $("form").serializeArray();

				    $("form input[type=checkbox]:not(:checked)").each(function() {
				    	values.push({ "name" : this.name, "value" : 0 })
				    });

					$.post($("form").attr("action"), values, function(data, textStatus) {
						var d = $("#yw2", $.parseHTML(data));

						$("#yw2").html($(d).html());						
						$(".alert").not(".alert-secondary").fadeIn(1000);
						$(".icon-spinner-form2").fadeOut();
						setTimeout(function() { $(".alert").not(".alert-secondary").fadeOut(1000); }, 5000);
					});

				}, 1000);

			});
		');
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
