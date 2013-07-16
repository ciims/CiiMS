<fieldset>
<?php
	foreach ($model->getThemes() as $theme=>$options)
	{
		$elements = array();
		$elementOptions = array('options' => array());

		foreach ($options as $k=>$v)
		{
			$themeFolder = str_replace('webroot.themes.', '', $v['folder']);
			$elements[] = $themeFolder;
			$elementOptions['options'][] = array('value' => $themeFolder, 'data-img-src' => Yii::app()->getBaseUrl(true) . '/themes/' . $themeFolder .'/default.png');
		}

		$attribute = ($theme == 'desktop' ? 'theme' : $theme.'Theme');
		echo CHtml::openTag('div', array('class' => 'pure-form-group', 'style' => 'padding-bottom: 20px'));
			echo CHtml::tag('legend', array(), Cii::titleize($attribute));
			echo $form->dropDownListRow($model, $attribute, $elements, $elementOptions);
			if (count($options) == 0)
				echo CHtml::tag('span', array('class' => 'noItemsMessage'), 'There are no themes installed for this category.');
		echo CHtml::closeTag('div');
		
	}
?>
</fieldset>

<?php
	Yii::app()->getClientScript()->registerCssFile($this->asset.'/css/image-picker.css')
								 ->registerScriptFile($this->asset.'/js/image-picker.min.js', CClientScript::POS_END)
								 ->registerScript('image-picker', '$("select").imagepicker();')
								 ->registerCss('no-labels', 'label { display: none; }');
?>