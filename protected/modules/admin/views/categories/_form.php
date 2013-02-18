<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
    'id'=>'categories-form',
    'enableAjaxValidation'=>false,
    'action'=>Yii::app()->createUrl('/admin/categories/save')
)); ?>

    <p class="help-block">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->dropDownListRow($model,'parent_id',CHtml::listData(Categories::model()->findAll(), 'id', 'name'), array('class'=>'span12')); ?>

    <?php echo $form->textFieldRow($model,'name',array('class'=>'span12','maxlength'=>150)); ?>

    <?php echo $form->textFieldRow($model,'slug',array('class'=>'span12','maxlength'=>150)); ?>

    <div class="form-actions">
        <?php $this->widget('bootstrap.widgets.TbButton', array(
            'buttonType'=>'submit',
            'type'=>'primary',
            'label'=>$model->isNewRecord ? 'Create' : 'Save',
        )); ?>
    </div>

<?php $this->endWidget(); ?>