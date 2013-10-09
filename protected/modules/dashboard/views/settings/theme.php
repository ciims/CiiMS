<fieldset>
	<legend><?php echo Yii::t('Dashboard.views', 'Install a Theme'); ?></legend>
	<div class="alert-secondary alert in alert-block fade alert-error" style="display:none">
		<a class="close" data-dismiss="alert">×</a>
	</div>
	<div class="pure-control-group pure-input-3-4">
		<p class="small-text"><?php echo Yii::t('Dashboard.views', 'Enter the user/repo of where the theme you want to download is located at.'); ?></p>
		<label><?php echo Yii::t('Dashboard.views', 'Repository'); ?></label>
		<input type="text" name="Theme[new]" id="Theme_new" class="pure-input-2-3" no-field-change="true" />
		<a id="submit-form" class="pure-button pure-button-primary pure-button-small pull-right">
			<span id="spinner">
				<span class="icon-spinner icon-spin icon-spinner-form"></span>
				<span class="icon-spacer"></span>
			</span>
			<?php echo Yii::t('Dashboard.views', 'Install Theme'); ?>
		</a>
	</div>
</fieldset>
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
				'data-img-src' => Yii::app()->getBaseUrl(true) . '/themes/'.$themeFolder.'/default.png',
				'selected' => Cii::getConfig($attribute) == $themeFolder ? 'selected' : null
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