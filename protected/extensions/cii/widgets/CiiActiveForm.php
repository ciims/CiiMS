<?php

Yii::import('cii.widgets.CiiBaseActiveForm');
class CiiActiveForm extends CiiBaseActiveForm
{
	public $registerPureCss = false;

	public $registerPrism = false;

	/**
	 * EmailField type
	 * @param  CiiSettingsModel $model       The model that we are operating on
	 * @param  string           $property    The name of the property we are working with
	 * @param  array            $htmlOptions An array of HTML Options
	 * @param  CValidator       $validators  The Validator(s) for this property
	 *                                       Since we already have it, it's worth passing through
	 */
	public function emailFieldRow($model, $property, $htmlOptions=array(), $validators=NULL)
	{
		if ($validators == NULL)
			$validators = $model->getValidators($property);

		if ($validators !== NULL)
		{
			foreach ($validators as $k=>$v)
			{
				if (get_class($v) == "CStringValidator")
				{
					if (isset($v->min))
						$htmlOptions['min']  = $v->min;

					if (isset($v->max))
						$htmlOptions['max']  = $v->max;
				}

				if (get_class($v) == "CRequiredValidator")
					$htmlOptions['required'] = true;
			}
		}

		$htmlOptions['value'] = $model->$property;
		$htmlOptions['type'] = 'email';
		$htmlOptions['id'] = get_class($model) . '_' . $property;
		$htmlOptions['name'] = get_class($model) . '[' . $property .']';

		echo CHtml::tag('label', array(), $model->getAttributeLabel($property) . (Cii::get($htmlOptions, 'required', false) ? CHtml::tag('span', array('class' => 'required'), ' *') : NULL));
		echo CHtml::tag('input', $htmlOptions);
	}

	/**
	 * TbActiveForm::textFieldRow() with min/max character length support.
	 * @param  CiiSettingsModel $model       The model that we are operating on
	 * @param  string           $property    The name of the property we are working with
	 * @param  array            $htmlOptions An array of HTML Options
	 * @param  CValidator       $validators  The Validator(s) for this property
	 *                                       Since we already have it, it's worth passing through
	 */
	public function textFieldRowLabelFix($model, $property, $htmlOptions=array(), $validators=NULL)
	{
		if ($validators !== NULL)
		{
			foreach ($validators as $k=>$v)
			{
				if (get_class($v) == "CStringValidator")
				{
					if (isset($v->min))
						$htmlOptions['min']  = $v->min;

					if (isset($v->max))
						$htmlOptions['max']  = $v->max;
				}

				if (get_class($v) == "CRequiredValidator")
					$htmlOptions['required'] = true;
			}
		}

		$htmlOptions['value'] = $model->$property;
		$htmlOptions['type'] = 'text';
		$htmlOptions['id'] = get_class($model) . '_' . $property;
		$htmlOptions['name'] = get_class($model) . '[' . $property .']';

		echo CHtml::tag('label', array(), CiiInflector::underscoretowords($model->getAttributeLabel($property)) . (Cii::get($htmlOptions, 'required', false) ? CHtml::tag('span', array('class' => 'required'), ' *') : NULL));
		echo CHtml::tag('input', $htmlOptions);
	}

	/**
	 * TbActiveForm::textFieldRow() with min/max character length support.
	 * @param  CiiSettingsModel $model       The model that we are operating on
	 * @param  string           $property    The name of the property we are working with
	 * @param  array            $htmlOptions An array of HTML Options
	 * @param  CValidator       $validators  The Validator(s) for this property
	 *                                       Since we already have it, it's worth passing through
	 */
	public function textFieldRow($model, $property, $htmlOptions=array(), $validators=NULL)
	{
		if ($validators !== NULL)
		{
			foreach ($validators as $k=>$v)
			{
				if (get_class($v) == "CStringValidator")
				{
					if (isset($v->min))
						$htmlOptions['min']  = $v->min;

					if (isset($v->max))
						$htmlOptions['max']  = $v->max;
				}

				if (get_class($v) == "CRequiredValidator")
					$htmlOptions['required'] = true;
			}
		}

		echo parent::textFieldRow($model, $property, $htmlOptions);
	}

	/**
	 * passwordFieldRow provides a password box that decrypts the database stored value since it will be encrypted in the db
	 * @param  CiiSettingsModel $model       The model that we are operating on
	 * @param  string           $property    The name of the property we are working with
	 * @param  array            $htmlOptions An array of HTML Options
	 * @param  CValidator       $validators  The Validator(s) for this property
	 *                                       Since we already have it, it's worth passing through
	 */
	public function passwordFieldRow($model, $property, $htmlOptions=array(), $validators=NULL)
	{
		$htmlOptions['value'] = Cii::decrypt($model->$property);
		$htmlOptions['type'] = 'password';
		$htmlOptions['id'] = get_class($model) . '_' . $property;
		$htmlOptions['name'] = get_class($model) . '[' . $property .']';
		echo CHtml::tag('label', array(), $model->getAttributeLabel($property) . (Cii::get($htmlOptions, 'required', false) ? CHtml::tag('span', array('class' => 'required'), ' *') : NULL));
		echo CHtml::tag('input', $htmlOptions);
	}

	/**
	 * numberRow HTML5 number elemtn to work with
	 * @param  CiiSettingsModel $model       The model that we are operating on
	 * @param  string           $property    The name of the property we are working with
	 * @param  array            $htmlOptions An array of HTML Options
	 * @param  CValidator       $validators  The Validator(s) for this property
	 *                                       Since we already have it, it's worth passing through
	 */
	public function numberFieldRow($model, $property, $htmlOptions=array(), $validators=NULL)
	{
		if ($validators !== NULL)
		{
			foreach ($validators as $k=>$v)
			{
				if (get_class($v) == "CNumberValidator")
				{
					$htmlOptions['min']  = $v->min;
					$htmlOptions['step'] = 1;
				}

				if (get_class($v) == "CRequiredValidator")
					$htmlOptions['required'] = true;
			}
		}

		$htmlOptions['value'] = $model->$property;
		$htmlOptions['type'] = 'number';
		$htmlOptions['id'] = get_class($model) . '_' . $property;
		$htmlOptions['name'] = get_class($model) . '[' . $property .']';
		echo CHtml::tag('label', array(), $model->getAttributeLabel($property) . (Cii::get($htmlOptions, 'required', false) ? CHtml::tag('span', array('class' => 'required'), ' *') : NULL));
		echo CHtml::tag('input', $htmlOptions);
	}

	/**
	 * rangeRow provides a pretty ish range slider with view controls
	 * @param  CiiSettingsModel $model       The model that we are operating on
	 * @param  string           $property    The name of the property we are working with
	 * @param  array            $htmlOptions An array of HTML Options
	 * @param  CValidator       $validators  The Validator(s) for this property
	 *                                       Since we already have it, it's worth passing through
	 */
	public function rangeFieldRow($model, $property, $htmlOptions=array(), $validators=NULL)
	{
		if ($validators !== NULL)
		{
			foreach ($validators as $k=>$v)
			{
				if (get_class($v) == "CNumberValidator")
				{
					$htmlOptions['min']  = $v->min;
					$htmlOptions['max']  = $v->max;
					$htmlOptions['step'] = 1;
				}
				
				if (get_class($v) == "CRequiredValidator")
					$htmlOptions['required'] = true;
			}
		}

		echo CHtml::tag('label', array(), $model->getAttributeLabel($property) . (Cii::get($htmlOptions, 'required', false) ? CHtml::tag('span', array('class' => 'required'), ' *') : NULL));
		echo $this->rangeField($model, $property, $htmlOptions);
		echo CHtml::tag('div', array('class' => 'output'), NULL);
	}

	/**
	 * toggleButtonRow provides a checkbox with toggle support via purecss.io and prism.js
	 * @param  CiiSettingsModel $model       The model that we are operating on
	 * @param  string           $property    The name of the property we are working with
	 * @param  array            $htmlOptions An array of HTML Options
	 * @param  CValidator       $validators  The Validator(s) for this property
	 *                                       Since we already have it, it's worth passing through
	 */
	public function toggleButtonRow($model, $property, $htmlOptions=array(), $validators=NULL)
	{
		echo CHtml::tag('label', array(), $model->getAttributeLabel($property));
		echo CHtml::openTag('div', array('class' => Cii::get($htmlOptions, 'class', 'pure-input-2-3'), 'style' => 'display: inline-block'));
			echo CHtml::openTag('label', array('class' => 'switch-light switch-candy'));
				$checked = array();
				if($model->$property == 1)
					$checked = array('checked' => 'checked');

				echo CHtml::openTag('input', CMap::mergeArray(array(
					'type' => 'checkbox',
					'id' => get_class($model) . '_' . $property,
					'name' => get_class($model) . '[' . $property . ']',
					'class' => Cii::get($htmlOptions, 'class', NULL),
					'value' => '1'
				), $checked));

				echo CHtml::openTag('span');
					echo CHtml::tag('span', array(), 'Off');
					echo CHtml::tag('span', array(), 'On');
				echo CHtml::closeTag('span');

				echo CHtml::tag('a', array('class' => 'slide-button'), NULL);
			echo CHtml::closeTag('label');
		echo CHtml::closeTag('div');
	}

	/**
	 * toggleButtonRow provides a checkbox with toggle support via purecss.io and prism.js
	 * @param  CiiSettingsModel $model       The model that we are operating on
	 * @param  string           $property    The name of the property we are working with
	 * @param  array            $htmlOptions An array of HTML Options
	 * @param  CValidator       $validators  The Validator(s) for this property
	 *                                       Since we already have it, it's worth passing through
	 */
	public function toggleButtonRowFix($model, $property, $htmlOptions=array(), $validators=NULL)
	{
		return $this->toggleButtonRow($model, $property, $htmlOptions, $validators);
	}
}
