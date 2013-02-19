<div class="row-fluid">
    <div class="span6">
        <?php $this->beginWidget('bootstrap.widgets.TbBox', array(
            'title' => 'Modify Profile',
            'headerIcon' => 'icon-user',
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
            <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
                'id'=>'users-form',
                'enableAjaxValidation'=>false,
            )); ?>
            
                <p class="help-block">Fields with <span class="required">*</span> are required. 
                    To change a users password, provide a new one. If left blank the existing password will be retained.</p>
                    
                <?php echo $form->errorSummary($model); ?>
            
                <?php echo $form->textFieldRow($model,'email',array('class'=>'span12','maxlength'=>255)); ?>        
                <?php echo $form->passwordFieldRow($model,'password',array('value'=>'', 'class'=>'span12','maxlength'=>64)); ?>        
                <?php echo $form->textFieldRow($model,'firstName',array('class'=>'span12','maxlength'=>255)); ?>        
                <?php echo $form->textFieldRow($model,'lastName',array('class'=>'span12','maxlength'=>255)); ?>        
                <?php echo $form->textFieldRow($model,'displayName',array('class'=>'span12','maxlength'=>255)); ?>        
                <?php echo $form->dropDownListRow($model,'user_role',CHtml::listdata(UserRoles::model()->findAll(), 'id', 'name'), array('class'=>'span12')); ?>        
                <?php echo $form->dropDownListRow($model,'status', array('1'=>'Active', '0'=>'Inactive'), array('class'=>'span12')); ?>

            
            <?php $this->endWidget(); ?>
        <?php $this->endWidget(); ?>
    </div>
    <div class="span3">
        <?php $this->beginWidget('bootstrap.widgets.TbBox', array(
            'title' => 'Application Data',
            'headerIcon' => 'icon-user',
        )); ?>
            <?php foreach ($model->metadata as $meta): ?>
                <li><?php Cii::debug($meta); ?></li>
            <?php endforeach; ?>
        <?php $this->endWidget(); ?>
    </div>
    <div class="span3">
        <?php $this->beginWidget('bootstrap.widgets.TbBox', array(
            'title' => 'Security Events',
            'headerIcon' => 'icon-user',
        )); ?>
        <?php $this->endWidget(); ?>
    </div>
</div>
