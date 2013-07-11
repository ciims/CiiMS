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
	 * Protected properties from Reflection
	 * @var [type]
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

		$asset=Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('application.modules.dashboard.assets'), true, -1, YII_DEBUG);
		$cs = Yii::app()->getClientScript();
		$cs->registerCssFile($asset.'/css/pure.css'); 
		$cs->registerCssFile($asset.'/prism/prism-light.css'); 
		$cs->registerScriptFile($asset.'/prism/prism.js', CClientScript::POS_END); 

		return parent::init();
	}

	/**
	 * The following is run when the widget is called
	 */
	public function run()
	{
		if (count($this->properties) == 0)
			return;

		// Setup the form
		$form = $this->beginWidget('TbActiveForm', array(
		    'id'=>get_class($this->model),
		    'enableAjaxValidation'=>true,
		    'htmlOptions' => array(
		    	'class' => 'pure-form pure-form-aligned'
		    )
		));

			// Header
			$this->renderHeader($form);

			// Before Content View
			// CActiveForm elements should not be used so that they are not submitted
			if ($this->model->preContentView !== NULL)
				$this->renderPartial($this->model->preContentView, array('model' => $this->model, 'properties' => $this->properties));

			// Main Content
			$this->renderMain($form);

		// Close the form
		$this->endWidget();
	}

	/**
	 * Renders the header
	 */
	private function renderHeader($form)
	{
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
	}

	/**
	 * Renders the main body content
	 */
	private function renderMain($form)
	{
		// #main .content
		echo CHtml::openTag('div', array('id' => 'main', 'class' => 'nano'));
			echo CHtml::openTag('div', array('class' => 'content'));
				
				// If we want a custom form view, render that view instead of the default behavior
				if ($this->model->form !== NULL)
					$this->renderPartial(Yii::getPathOfAlias($this->model->form), array('model' => $this->model, 'properties' => $this->properties, 'form' => $form));
				else
				{
					foreach ($this->properties as $property)
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
						{
							$this->toggleButtonRow($form, $this->model, $property->name, $htmlOptions, $validators);
						}
						else if (in_array('number', $stringValidators))
						{
							$this->rangeRow($form, $this->model, $property->name, $htmlOptions, $validators);
						}
						else
							echo $form->textFieldRow($this->model, $property->name, $htmlOptions);
						echo CHtml::closeTag('div');
					}
				}
				
				echo CHtml::submitButton('Save Changes', array('class' => 'pure-button pure-button-primary pull-right'));

			echo CHtml::closeTag('div');
		echo CHtml::closeTag('div');
	}

	/**
	 * rangeRow provides a pretty ish range slider with view controls
	 * @param  CACtiveForm      $form        The CActiveForm element
	 * @param  CiiSettingsModel $model       The model that we are operating on
	 * @param  string           $property    The name of the property we are working with
	 * @param  array            $htmlOptions An array of HTML Options
	 * @param  CValidator       $validators  The Validator(s) for this property
	 *                                       Since we already have it, it's worth passing through
	 */
	private function rangeRow($form, $model, $property, $htmlOptions=array(), $validators=NULL)
	{
		//$htmlOptions['style'] = 'width: 59%';
		foreach ($validators as $k=>$v)
		{
			if (get_class($v) == "CNumberValidator")
			{
				$htmlOptions['min']  = $v->min;
				$htmlOptions['max']  = $v->max;
				$htmlOptions['step'] = 1;
			}
			break;
		}

		echo CHtml::tag('label', array(), $model->getAttributeLabel($property));
		echo $form->rangeField($model, $property, $htmlOptions);
		echo CHtml::tag('div', array('class' => 'output'), NULL);

		// Register a script. Allow it to be overriden since it is global
		Yii::app()->getClientScript()->registerScript('slider', '
			$("input[type=\"range\"]").each(function() {
				$(this).parent().find(".output").html($(this).val());
			})

			$("input[type=\"range\"]").change(function() { 
				$(this).parent().find(".output").html($(this).val()); 
			});
		');
	}

	/**
	 * toggleButtonRow provides a checkbox with toggle support via purecss.io and prism.js
	 * @param  CACtiveForm      $form        The CActiveForm element
	 * @param  CiiSettingsModel $model       The model that we are operating on
	 * @param  string           $property    The name of the property we are working with
	 * @param  array            $htmlOptions An array of HTML Options
	 * @param  CValidator       $validators  The Validator(s) for this property
	 *                                       Since we already have it, it's worth passing through
	 */
	private function toggleButtonRow($form, $model, $property, $htmlOptions=array(), $validators=NULL)
	{
		echo CHtml::tag('label', array(), $model->getAttributeLabel($property));
		echo CHtml::openTag('div', array('class' => 'pure-input-2-3', 'style' => 'display: inline-block'));
			echo CHtml::openTag('label', array('class' => 'checkbox toggle candy blue'));
				echo $form->checkBox($model, $property, $htmlOptions);
				echo CHtml::openTag('p');
					echo CHtml::tag('span', array(), 'On');
					echo CHtml::tag('span', array(), 'Off');
				echo CHtml::closeTag('p');

				echo CHtml::tag('a', array('class' => 'slide-button'), NULL);
			echo CHtml::closeTag('label');
		echo CHtml::closeTag('div');
	}
}