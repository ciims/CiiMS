<div class="well span6">
		<h4>Forgot Your Password?</h4>
		<br />	
		<?php if(Yii::app()->user->hasFlash('reset-sent')):?>
		    <div><?php echo Yii::app()->user->getFlash('reset-sent'); ?></div>
		<?php endif; ?>
		<?php if(Yii::app()->user->hasFlash('reset-error')):?>
		    <div><?php echo Yii::app()->user->getFlash('reset-error'); ?></div>
		<?php endif; ?>
		<br />
	<?php if ($id == NULL): ?>		
		<?php echo CHtml::beginForm('/forgot'); ?>
		<?php echo CHtml::textField('email', isset($_POST['email']) ? $_POST['email'] : '', array('style'=>'width: 70%', 'placeholder'=>'Your email address (you@example.com)')); ?>
		<?php echo CHtml::submitButton('Reset My Password', array('class'=>'btn btn-primary', 'style'=>'margin-top: -9px')); ?>
		<?php echo CHtml::endForm(); ?>
	<?php else: ?>
		<?php if ($badHash): ?>
			<br />
		    <div class="red-box">The password reset key you provided is either invalid or has expired.</div>
		<?php else: ?>
			<?php echo CHtml::beginForm('/forgot/'.$id, 'POST', array('name'=>'reset', 'id'=>'reset')) ?>
			<?php echo CHtml::passwordField('password',  isset($_POST['password']) ? $_POST['password'] : '', array('placeholder'=>'Your new password', 'style'=>'width: 70%')); ?>
			<br />
			<br />
			<?php echo CHtml::passwordField('password2',  isset($_POST['password2']) ? $_POST['password2'] : '', array('placeholder'=>'Once more with feeling!', 'style'=>'width: 70%')); ?>
			<?php echo CHtml::submitButton('Reset My Password', array('class'=>'search-button')); ?>
			<?php echo CHtml::endForm(); ?>
		<?php endif; ?>
	<?php endif; ?>
</div>
<div class="well span5">
	<h4>Help! I Forgot My Password!</h4>
	<div class="horizontal-rule"></div>
	 <p>Don't worry, we can help you reset it. Simply follow the steps below to reset your password</p>
	 <br />
    	<ul>
	    	<li>Type in the email address you used to register on the site in the form on the left, then click submit.</li>
	    	<li>We will send you an email with a link to reset your password. Click on the link to come to our password reset page</li>
	    	<li>When prompted, provide us with the new password you'd like to use</li>
	    	<li>After resetting your password you'll be redirected to the login screen</li>
	    </ul>
</div>
<?php Yii::app()->clientScript->registerCss('one-half', '.one-half{margin-top: 0px;}'); ?>
