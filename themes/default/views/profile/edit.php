<div class="modal-container">
    <h2 class="pull-left"><?php echo Yii::t('DefaultTheme.main', 'Login to Your Account'); ?></h3>
    <hr class="clearfix"/>
    <?php $form=$this->beginWidget('cii.widgets.CiiActiveForm', array(
            'id'					=>	'login-form',
            'focus'					=> 'input[type="text"]:first',
            'registerPureCss'       => false,
            'enableAjaxValidation'	=>	false,
            'htmlOptions' => array(
                'class' => 'pure-form pure-form-stacked'
            )
        )); ?>

        <?php echo $form->textField($model,'email',array('class'=>'pure-u-1','maxlength'=>255)); ?>        
		<?php echo $form->passwordField($model,'password',array('value'=>'', 'class'=>'pure-u-1','maxlength'=>64, 'placeholder' => Yii::t('DefaultTheme.main', 'Set a password for the user here. Leave blank to keep existing password.'))); ?>    
		<?php echo $form->textField($model,'displayName',array('class'=>'pure-u-1','mlaxlength'=>255)); ?>    
		<?php echo $form->textField($model,'firstName',array('class'=>'pure-u-1','maxlength'=>255)); ?>        
		<?php echo $form->textField($model,'lastName',array('class'=>'pure-u-1','maxlength'=>255)); ?>
		<?php echo $form->textArea($model, 'about', array('class' => 'pure-u-1', 'placeholder' => Yii::t('DefaultTheme.main', 'Tell us about yourself'))); ?>

        <button type="submit" class="pull-right pure-button pure-button-primary"><?php echo Yii::t('DefaultTheme.main', 'Submit'); ?></button>
        <div class="clearfix"></div>

    <?php $this->endWidget(); ?>
    <div class="clearfix"></div>
</div>
