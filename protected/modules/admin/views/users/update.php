<div class="row-fluid">
    <div class="span8">
        
            <?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm',array(
                'id'=>'users-form',
                'enableAjaxValidation'=>false,
            )); ?>
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
            
                <p class="help-block">Fields with <span class="required">*</span> are required. 
                    To change a users password, provide a new one. If left blank the existing password will be retained.</p>
                    
                <?php echo $form->errorSummary($model); ?>
            
                <?php echo $form->textFieldRow($model,'email',array('class'=>'span12','maxlength'=>255)); ?>        
                <?php echo $form->passwordFieldRow($model,'password',array('value'=>'', 'class'=>'span12','maxlength'=>64, 'placeholder' => 'Set a password for the user here. Leave blank to keep existing password.')); ?>        
                <?php echo $form->textFieldRow($model,'firstName',array('class'=>'span12','maxlength'=>255)); ?>        
                <?php echo $form->textFieldRow($model,'lastName',array('class'=>'span12','maxlength'=>255)); ?>        
                <?php echo $form->textFieldRow($model,'displayName',array('class'=>'span12','maxlength'=>255)); ?>        
                <?php echo $form->dropDownListRow($model,'user_role',CHtml::listdata(UserRoles::model()->findAll(), 'id', 'name'), array('class'=>'span12')); ?>        
                <?php echo $form->dropDownListRow($model,'status', array('1'=>'Active', '0'=>'Inactive'), array('class'=>'span12')); ?>
                <?php echo $form->textAreaRow($model, 'about', array('class' => 'span12')); ?>
            <?php $this->endWidget(); ?>
        <?php $this->endWidget(); ?>
    </div>
    <div class="span4">
        <?php $this->beginWidget('bootstrap.widgets.TbBox', array(
            'title' => 'Application Data',
            'headerIcon' => 'icon-cog',
        )); ?>
            <p><small>Any application data that is stored with this user is displayed here. Don't remove this data unless you know what you are doing.</small></p>
            <table class="detail-view table table-striped table-condensed" id="yw3">
                <tbody>
                    <?php $i = 0; foreach ($model->metadata as $meta): $i++; ?>
                    <tr>
                        <th class="<?php echo $i % 2 == 0 ? 'even' : 'odd'; ?>" style="width: auto; text-align:left;"><?php echo $meta->key; ?></th>
                        <td><?php echo $meta->value; ?><i id="<?php echo $meta->key; ?>" class="icon-remove" style="float: right;"></i></td>
                        
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php $this->endWidget(); ?>
    </div>
    <!--
    <div class="span3">
        <?php $this->beginWidget('bootstrap.widgets.TbBox', array(
            'title' => 'Security Events',
            'headerIcon' => 'icon-user',
        )); ?>
            <p><small>Any security events attached to this user will be displayed here.</small></p>
        <?php $this->endWidget(); ?>
    </div>
    -->
</div>

<?php Yii::app()->clientScript->registerScript('delete_meta', '
    $(".icon-remove").click(function() {
        $(this).parent().parent().fadeOut();
        $.post("../../removeMeta", { key : $(this).attr("id"), user_id : ' . $model->id. '}); 
    });
'); ?>
