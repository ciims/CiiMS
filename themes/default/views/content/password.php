<div class="well span20">
	<h2>This post requires a password to view</h2>
	<br />
	<center>
		<? echo CHtml::beginForm(); ?>

			<? echo CHtml::PasswordField('password', '', array('id'=>'password', 'placeholder'=>'Password')); ?>
			<? echo CHtml::hiddenField('id', $id); ?>
			<? echo CHtml::submitButton('Authenticate', array('class'=>'btn btn-primary', 'escape'=>false, 'style'=>'margin-top: -9px; margin-left: 10px;')); ?>
		
		<? echo CHtml::endForm(); ?>
	</center>
		
</div>