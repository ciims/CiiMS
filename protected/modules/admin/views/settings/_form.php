<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
    'id'=>'configuration-form',
    'enableAjaxValidation'=>false,
    'action'=>Yii::app()->createUrl('/admin/settings/save/id/' . $model->key)
)); ?>

    <?php $this->beginWidget('bootstrap.widgets.TbBox', array(
        'title' => 'Add a Setting',
        'headerButtons' => array(
            array(
                'class' => 'bootstrap.widgets.TbButton',
                'buttonType'=>'submit',
                'type'=>'primary',
                'label'=>$model->isNewRecord ? 'Create' : 'Save',
                'htmlOptions' => array(
                    'style' => 'margin-right: 10px;'
                )
            )
        )
    )); ?>
        <p class="help-block">Fields with <span class="required">*</span> are required.</p>
    
        <?php echo $form->errorSummary($model); ?>
    
    
        <?php echo $form->textFieldRow($model,'key',array('class'=>'span11','maxlength'=>150)); ?>
    
        <?php echo $form->textFieldRow($model,'value',array('class'=>'span11','maxlength'=>150)); ?>
    <?php $this->endWidget(); ?>
<?php $this->endWidget(); ?>