<div class="login-container">
	<div class="sidebar">
		<div class="well-span">
			<h4><?php echo Yii::t('DefaultTheme', 'Create Your Account'); ?></h4>
			<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
					'id'					=>	'login-form',
					'focus'					=>'	input[type="text"]:first',
					'enableAjaxValidation'	=>	true,
				)); ?>
				<?php if ($model->hasErrors()): ?>
						<div class="alert alert-error" style="margin-bottom: -5px;">
						  	<button type="button" class="close" data-dismiss="alert">&times;</button>
						  	<?php echo $form->errorSummary($model); ?> 
						</div>
					<?php endif; ?>
				<div class="login-form-container">
					<?php echo $form->textField($model, 'email', array('placeholder' => Yii::t('DefaultTheme', 'Email Address'))); ?>
					<?php echo $form->textField($model, 'firstName', array('placeholder' => Yii::t('DefaultTheme', 'First Name'))); ?>
					<?php echo $form->textField($model, 'lastName', array('placeholder' => Yii::t('DefaultTheme', 'Last Name'))); ?>
					<?php echo $form->textField($model, 'displayName', array('placeholder' => Yii::t('DefaultTheme', 'Display Name'))); ?>
					<?php echo $form->passwordField($model, 'password', array('placeholder' => Yii::t('DefaultTheme', 'Password'))); ?>
				</div>
				<div class="login-form-footer">
					<?php $this->widget('bootstrap.widgets.TbButton', array(
								'buttonType' => 'submit',
	    	                    'type' => 'success',
	    	                    'label' => Yii::t('DefaultTheme', 'Submit'),
	    	                    'htmlOptions' => array(
	    	                        'id' => 'submit-comment',
	    	                        'class' => 'sharebox-submit pull-right',
	    	                        'style' => 'margin-top: -4px'
	    	                    )
	    	                )); ?>
				</div>
			<?php $this->endWidget(); ?>
		</div>
	</div>
</div>