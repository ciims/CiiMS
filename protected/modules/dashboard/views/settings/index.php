<div class="header">
	<h3>General Settings</h3>
	<p>Set basic information about your site and change global settings.</p>
</div>

<?php $form = $this->beginWidget('CActiveForm', array(
    'id'=>'GeneralSettings',
    'enableAjaxValidation'=>true,
)); ?>
	<?php echo $form->textField($model, 'bcrypt_cost'); ?>
<?php $this->endWidget(); ?>