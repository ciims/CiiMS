<?php $form=$this->beginWidget('bootstrap.widgets.BootActiveForm',array(
	'id'=>'categories-form',
	'enableAjaxValidation'=>false,
	'action'=>Yii::app()->createUrl('/admin/categories/save')
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->dropDownListRow($model,'parent_id',CHtml::listData(Categories::model()->findAll(), 'id', 'name'), array('class'=>'span5')); ?>

	<?php echo $form->textFieldRow($model,'name',array('class'=>'span5','maxlength'=>150)); ?>

	<?php echo $form->textFieldRow($model,'slug',array('class'=>'span5','maxlength'=>150)); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.BootButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
