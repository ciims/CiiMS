<div class="well span6">
	<?php $form=$this->beginWidget('CActiveForm', array(
					'id'=>'contact',
					'focus'=>array($model,'email'),
				    	'enableAjaxValidation'=>true,
						'errorMessageCssClass'=>'red-box',
				)); ?>

	<?php if ($error != ''): ?>
		<div><?php echo $error; ?></div>
	<?php endif; ?>
        	
    <?php echo $form->errorSummary($model,'','',array('class'=>'red-box')); ?>
    <br />
	<?php echo $form->textField($model,'email', array('id'=>'email', 'placeholder'=>'Your Email Address', 'style'=>'width: 438px;')); ?><br /><br />
	<div class="clear:both;"></div>
	<?php echo $form->PasswordField($model,'password', array('id'=>'password', 'placeholder'=>'Password', 'style'=>'width: 438px;')); ?><br /><br />
	<?php echo $form->PasswordField($model,'password2', array('id'=>'password2', 'placeholder'=>' Password (again)', 'style'=>'width: 438px;')); ?><br /><br />
	<?php echo $form->textField($model,'firstName', array('id'=>'firstName', 'placeholder'=>'Your First Name', 'style'=>'width: 438px;')); ?><br /><br />
	<?php echo $form->textField($model,'lastName', array('id'=>'lastName', 'placeholder'=>'Your Last Name', 'style'=>'width: 438px;')); ?><br /><br />
	<?php echo $form->textField($model,'displayName', array('id'=>'displayName', 'placeholder'=>'Display Name', 'style'=>'width: 438px;')); ?><br /><br />
    <?php echo $captcha->recaptcha_get_html(Yii::app()->params['reCaptchaPublicKey'], array()); ?>
    <br />
    <br />
    <br />
	<?php echo CHtml::submitButton('Signup', array('class'=>'btn btn-primary', 'style'=>'float:right;')); ?>

	<?php $this->endWidget(); ?>
	
</div>

<div class="well span5">
	<h3>The Information We Collect</h3>
    <p>Is used only to uniquely identify you in our system. Your information will never be:</p>
    <ul class="green-tick">
    	<li>Sold to third party companies.</li>
    	<li>Surrendered unless applicable by law.</li>
    	<li>Used to generate custom advertisements.</li>
    </ul>
    <p>Your information will be:</p>
    <hr />
    <p>Already have an account? <strong><?php echo CHtml::link('Login', Yii::app()->createUrl('/login')); ?></strong> instead!</p>
		   
	
</div>

<?php Yii::app()->clientScript->registerScript('captchaOptions', "var RecaptchaOptions = { theme : 'clean' };", CClientScript::POS_HEAD); ?>

