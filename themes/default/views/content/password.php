<div class="well span20">
	<h2>This post requires a password to view</h2>
	<br />
	<center>
		<?php echo CHtml::beginForm(); ?>

			<?php echo CHtml::PasswordField('password', '', array('id'=>'password', 'placeholder'=>'Password')); ?>
			<?php echo CHtml::hiddenField('id', $id); ?>
			<?php echo CHtml::submitButton('Authenticate', array('class'=>'btn btn-primary', 'escape'=>false, 'style'=>'margin-top: -9px; margin-left: 10px;')); ?>
		
		<?php echo CHtml::endForm(); ?>
	</center>
		
</div>