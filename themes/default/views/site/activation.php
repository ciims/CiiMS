<?php $info = Yii::app()->user->hasFlash('activation-info'); ?>
<div class="login-container">
	<div class="sidebar">
		<div class="well-span">
			<h4>Activate your Account</h4>
			<?php if(Yii::app()->user->hasFlash('activation-error')):?>
				<div class="alert alert-error" style="margin-top: 20px;">
				  	<strong>Oops!</strong> <?php echo Yii::app()->user->getFlash('activation-error'); ?>
				</div>
			<?php endif; ?>
			
			<?php if(Yii::app()->user->hasFlash('activation-success')):?>
				<div class="alert alert-success" style="margin-top: 20px;">
				  	<strong>All Right!</strong> <?php echo Yii::app()->user->getFlash('activation-success'); ?>
				</div>
			<?php endif; ?>

			<?php if($info):?>
				<div class="alert alert-info" style="margin-top: 20px;">
				  	<?php echo Yii::app()->user->getFlash('activation-info'); ?>
				</div>
			<?php endif; ?>

			<?php if ($info):?>
				<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
							'id'					=> 'login-form',
							'focus'					=> 'input[type="text"]:first',
							'enableAjaxValidation'	=>	true
						)); ?>
				<div class="login-form-container">
					<?php echo CHtml::passwordField('password', Cii::get($_POST, 'password', NULL), array('placeholder' => 'Password')); ?>
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
				</div>
				<?php $this->endWidget(); ?>
			<?php endif; ?>
		</div>
	</div>
</div>