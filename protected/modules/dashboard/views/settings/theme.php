<fieldset>
<?php
	foreach ($model->getThemes() as $theme=>$options)
	{
		$attribute = ($theme == 'desktop' ? 'theme' : $theme.'Theme');

		$elements = array();
		$elementOptions = array('options' => array());

		// Allow themes to be empty for non desktop theme
		if ($attribute !== 'theme')
		{
			$elements = array(NULL);
			$elementOptions = array('options' => array(array('value' => NULL)));
		}
		

		foreach ($options as $k=>$v)
		{
			$themeFolder = str_replace('webroot.themes.', '', $v['folder']);
			$elements[] = $themeFolder;

			// This image SHOULD be publicly accessible at this location assuming you have a half sane setup
			$elementOptions['options'][] = array(
				'value' => $themeFolder, 
				'data-img-src' => Yii::app()->getBaseUrl(true) . '/themes/'.$themeFolder.'/default.png'
			);
		}		

		echo CHtml::openTag('div', array('class' => 'pure-form-group', 'style' => 'padding-bottom: 20px'));
			echo CHtml::tag('legend', array(), Cii::titleize($attribute));
			echo $form->dropDownListRow($model, $attribute, $elements, $elementOptions);

			if (count($options) == 0)
				echo CHtml::tag('div', array('class' => 'row noItemsMessage'), CHtml::tag('span', array(), Yii::t('Dashboard.views', 'There are no themes installed for this category.')));

		echo CHtml::closeTag('div');		
	}
?>
</fieldset>

<?php
	Yii::app()->getClientScript()->registerCssFile($this->asset.'/css/image-picker.css')
								 ->registerScriptFile($this->asset.'/js/image-picker.min.js', CClientScript::POS_END)
								 ->registerCss('no-labels', 'label { display: none; }');
?>