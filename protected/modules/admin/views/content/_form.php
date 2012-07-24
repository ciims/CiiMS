<p class="help-block">Fields with <span class="required">*</span> are required.</p>

<?php echo $form->errorSummary($model); ?>

<?php echo $form->hiddenField($model,'vid'); ?>
<?php echo $form->hiddenField($model,'id'); ?>
<?php echo $form->hiddenField($model,'parent_id',array('value'=>1)); ?>
<?php echo $form->hiddenField($model,'author_id',array('value'=>Yii::app()->user->id,)); ?>

<?php echo $form->textFieldRow($model,'title',array('class'=>'span12','maxlength'=>150)); ?>

<?php echo $form->textAreaRow($model,'content',array('rows'=>12, 'cols'=>50, 'class'=>'span12')); ?>

<div id="extractForm" class="hidden"><?php echo $form->textAreaRow($model,'extract',array('rows'=>6, 'cols'=>50, 'class'=>'span12')); ?></div>
		