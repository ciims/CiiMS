<div class="login-container">
	<div class="sidebar">
		<div class="well">
			<h4>Register An Account</h4>
			<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
						'id'					=>	'login-form',
						'focus'					=>'	input[type="text"]:first',
						'enableAjaxValidation'	=>	true
					)); ?>
				<div class="login-form-container">
					<?php if (!Yii::app()->user->isGuest): ?>
						<div class="alert alert-info" style="margin-top: 20px;">
						  	<button type="button" class="close" data-dismiss="alert">&times;</button>
						  	<strong>Heads Up!</strong> Looks like you're already logged in as <strong><?php echo Yii::app()->user->email; ?></strong>. You should <strong><?php echo CHtml::link('logout', $this->createUrl('/logout')); ?></strong> before logging in to another account.
						</div>
					<?php else: ?>
						<?php if ($model->hasErrors()): ?>
							<div class="alert alert-error" style="margin-bottom: -5px;">
							  	<button type="button" class="close" data-dismiss="alert">&times;</button>
							  	<strong>Oops!</strong> Looks like there were a few errors in your submission.
							</div>
						<?php endif; ?>
						<?php echo $form->TextField($model, 'email', array('id'=>'email', 'placeholder'=>'Email Address')); ?>
						<?php echo $form->TextField($model, 'displayName', array('id'=>'email', 'placeholder'=>'Username')); ?>
						<?php echo $form->PasswordField($model, 'password', array('id'=>'password', 'placeholder'=>'Password')); ?>
						<?php echo $form->PasswordField($model, 'password2', array('id'=>'password', 'placeholder'=>'Password (again)')); ?>
					</div>
					<div class="login-form-footer">
						<?php echo CHtml::link('login', Yii::app()->createUrl('/login'), array('class' => 'login-form-links')); ?>
						<span class="login-form-links"> | </span>
						<?php echo CHtml::link('forgot', Yii::app()->createUrl('/forgot'), array('class' => 'login-form-links')); ?>
						<?php $this->widget('bootstrap.widgets.TbButton', array(
								'buttonType' => 'submit',
	    	                    'type' => 'success',
	    	                    'label' => 'Register',
	    	                    'htmlOptions' => array(
	    	                        'id' => 'submit-comment',
	    	                        'class' => 'sharebox-submit pull-right',
	    	                        'style' => 'margin-top: -4px'
	    	                    )
	    	                )); ?>
    	            <?php endif; ?>
				</div>
			<?php $this->endWidget(); ?>
		</div>
	</div>
</div>