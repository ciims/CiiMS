<div class="login-container">
	<div class="sidebar">
		<div class="well-span">
			<h4><?php echo Yii::t('DefaultTheme', 'Change Your Email Address'); ?></h4>
			<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
						'id'					=> 'login-form',
						'focus'					=> 'input[type="text"]:first',
						'enableAjaxValidation'	=>	true
					)); ?>
			<div class="login-form-container">
				<?php if(Yii::app()->user->hasFlash('authenticate-error')):?>
					<div class="alert alert-error" style="margin-top: 20px;">
					  	<?php echo Yii::app()->user->getFlash('authenticate-error'); ?>
					</div>
				<?php endif; ?>

				<?php if($success): ?>
					<div class="alert alert-success" style="margin-top: 20px;">
					  	<?php echo $success; ?>
					</div>
				<?php endif; ?>

				<?php if (!$success): ?>
					<p>
						<?php echo Yii::t('DefaultTheme', 'To change the email address associated to your account, please enter your current password.'); ?>
					</p>
					<?php echo CHtml::passwordField('password',  isset($_POST['password']) ? $_POST['password'] : '', array('placeholder'=>Yii::t('DefaultTheme', 'Your current password'))); ?>
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
	            <?php endif; ?>
			</div>
			
			<?php $this->endWidget(); ?>
		</div>
	</div>
</div>