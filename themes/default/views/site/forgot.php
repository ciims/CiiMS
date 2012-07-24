<div class="well span6">
		<h4>Forgot Your Password?</h4>
		<br />	
		<? if(Yii::app()->user->hasFlash('reset-sent')):?>
		    <div><? echo Yii::app()->user->getFlash('reset-sent'); ?></div>
		<? endif; ?>
		<? if(Yii::app()->user->hasFlash('reset-error')):?>
		    <div><? echo Yii::app()->user->getFlash('reset-error'); ?></div>
		<? endif; ?>
		<br />
	<? if ($id == NULL): ?>		
		<? echo CHtml::beginForm('/forgot'); ?>
		<? echo CHtml::textField('email', $this->displayVar($_POST['email']), array('style'=>'width: 70%', 'placeholder'=>'Your email address (you@example.com)')); ?>
		<? echo CHtml::submitButton('Reset My Password', array('class'=>'btn btn-primary', 'style'=>'margin-top: -9px')); ?>
		<? echo CHtml::endForm(); ?>
	<? else: ?>
		<? if ($badHash): ?>
			<br />
		    <div class="red-box">The password reset key you provided is either invalid or has expired.</div>
		<? else: ?>
			<? echo CHtml::beginForm('/forgot/'.$id, 'POST', array('name'=>'reset', 'id'=>'reset')) ?>
			<? echo CHtml::passwordField('password', $this->displayVar($_POST['password']), array('placeholder'=>'Your new password', 'style'=>'width: 70%')); ?>
			<br />
			<br />
			<? echo CHtml::passwordField('password2', $this->displayVar($_POST['password2']), array('placeholder'=>'Once more with feeling!', 'style'=>'width: 70%')); ?>
			<? echo CHtml::submitButton('Reset My Password', array('class'=>'search-button')); ?>
			<? echo CHtml::endForm(); ?>
		<? endif; ?>
	<? endif; ?>
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
<? Yii::app()->clientScript->registerCss('one-half', '.one-half{margin-top: 0px;}'); ?>