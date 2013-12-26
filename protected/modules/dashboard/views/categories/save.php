<?php $htmlOptions = array('class' => 'pure-input-2-3', 'no-field-change' => 'true'); ?>
<?php $form = $this->beginWidget('cii.widgets.CiiActiveForm', array(
	'enableAjaxValidation'=>false,
    'htmlOptions' => array(
    	'class' => 'pure-form pure-form-aligned'
    )
)); ?>
<div class="header">
	<div class="pull-left">
		<p><?php echo $model->isNewRecord ? Yii::t('Dashboard.views', 'Create a New Category') : Yii::t('Dashboard.views', 'Makes Changes to {{group}}', array('{{group}}' => $model->name)); ?></p>
	</div>
	<div class="pull-right">
		<?php echo CHtml::submitButton('Save Changes', array('id' => 'header-button', 'class' => 'pure-button pure-button-primary pure-button-small pull-right')); ?>
	</div>
	<div class="clearfix"></div>
</div>
<div id="main" class="nano">
	<div class="content">
		<fieldset>
				<legend><?php echo Yii::t('Dashboard.main', 'Category Information'); ?></legend>
				<div class="pure-control-group">
					<?php echo $form->hiddenField($model, 'id'); ?>
				</div>
				<div class="pure-control-group">
					<?php echo $form->textFieldRow($model, 'name', $htmlOptions); ?>
				</div>
				<div class="pure-control-group">
					<?php echo $form->textFieldRow($model, 'slug', $htmlOptions); ?>
				</div>
				<div class="pure-control-group">
					<?php echo $form->textAreaRow($model, 'description', CMap::mergeArray($htmlOptions, array('style' => 'height: 100px; width: 66%;', 'value' => $model->getDescription()))); ?>
				</div>
				<div class="pure-control-group">
					<?php echo $form->dropDownListRow($model, 'parent_id', CHtml::listData(Categories::model()->findAll(), 'id', 'name'), $htmlOptions); ?>
				</div>

			<?php echo CHtml::submitButton(Yii::t('Dashboard.views', 'Save Changes'), array('class' => 'pure-button pure-button-primary pure-button-small pull-right')); ?>
		<fieldset>
	</div>
</div>
<?php $this->endWidget(); ?>
