<div class="login-container">
	<div class="sidebar">
		<div class="well-span">
			<h4>Forgot Your Password?</h4>
			<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
						'id'					=> 'login-form',
						'focus'					=> 'input[type="text"]:first',
						'enableAjaxValidation'	=>	true
					)); ?>
			<div class="login-form-container">
				<?php if(Yii::app()->user->hasFlash('reset-sent')):?>
					<div class="alert alert-success" style="margin-bottom: -5px;">
					  	<button type="button" class="close" data-dismiss="alert">&times;</button>
					  	<?php echo Yii::app()->user->getFlash('reset-sent'); ?>
					</div>
				<?php endif; ?>

				<?php if ($id == NULL): ?>		
					<?php echo CHtml::textField('email', isset($_POST['email']) ? $_POST['email'] : '', array('placeholder'=>'Your email address (you@example.com)')); ?>

				<?php else: ?>
					<?php if ($badHash): ?>
						<br />
					    <div class="red-box">The password reset key you provided is either invalid or has expired.</div>
					<?php else: ?>
						
						<?php echo CHtml::passwordField('password',  isset($_POST['password']) ? $_POST['password'] : '', array('placeholder'=>'Your new password')); ?>
						<br />
						<br />
						<?php echo CHtml::passwordField('password2',  isset($_POST['password2']) ? $_POST['password2'] : '', array('placeholder'=>'Once more with feeling!')); ?>
					<?php endif; ?>
				<?php endif; ?>
			</div>
			<div class="login-form-footer">
				<?php echo CHtml::link('register', Yii::app()->createUrl('/register'), array('class' => 'login-form-links')); ?>
				<span class="login-form-links"> | </span>
				<?php echo CHtml::link('login', Yii::app()->createUrl('/login'), array('class' => 'login-form-links')); ?>
				<?php $this->widget('bootstrap.widgets.TbButton', array(
								'buttonType' => 'submit',
	    	                    'type' => 'success',
	    	                    'label' => 'Submit',
	    	                    'htmlOptions' => array(
	    	                        'id' => 'submit-comment',
	    	                        'class' => 'sharebox-submit pull-right',
	    	                        'style' => 'margin-top: -4px'
	    	                    )
	    	                )); ?>

				<?php $this->endWidget(); ?>
			</div>
		</div>
	</div>
</div>