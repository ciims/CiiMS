<?php $form=$this->beginWidget('bootstrap.widgets.BootActiveForm',array(
	'id'=>'users-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="help-block">Fields with <span class="required">*</span> are required. To change a users password, provide a new one. If left blank the old password will be retained.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php echo $form->textFieldRow($model,'email',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->passwordFieldRow($model,'password',array('value'=>'', 'class'=>'span5','maxlength'=>64)); ?>

	<?php echo $form->textFieldRow($model,'firstName',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'lastName',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->textFieldRow($model,'displayName',array('class'=>'span5','maxlength'=>255)); ?>

	<?php echo $form->dropDownListRow($model,'user_role',CHtml::listdata(UserRoles::model()->findAll(), 'id', 'name'), array('class'=>'span5')); ?>

	<?php echo $form->dropDownListRow($model,'status', array('1'=>'Active', '0'=>'Inactive'), array('class'=>'span5')); ?>

	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.BootButton', array(
			'buttonType'=>'submit',
			'type'=>'primary',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
	</div>

<?php $this->endWidget(); ?>
