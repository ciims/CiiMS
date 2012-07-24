<?php echo $form->dropDownListRow($model,'status', array(1=>'Published', 0=>'Draft'), array('class'=>'span5')); ?>

<?php echo $form->dropDownListRow($model,'commentable', array(1=>'Yes', 0=>'No'), array('class'=>'span5')); ?>

<?php echo $form->dropDownListRow($model,'category_id', CHtml::listData(Categories::model()->findAll(), 'id', 'name'), array('class'=>'span5')); ?>

<?php echo $form->dropDownListRow($model,'type_id', array(2=>'Blog Post', 1=>'Page;'),array('class'=>'span5')); ?>

<?php echo $form->textFieldRow($model,'password',array('class'=>'span5','maxlength'=>150)); ?>

<?php echo $form->textFieldRow($model,'slug',array('class'=>'span5','maxlength'=>150)); ?>
