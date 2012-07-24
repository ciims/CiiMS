<div class="well span6">
	<h4>Login to Your Account</h4>
	<div class="horizontal-rule"></div>
	<?php if(Yii::app()->user->hasFlash('reset')):?>
		    <br /><div><?php echo Yii::app()->user->getFlash('reset'); ?></div>
	<?php endif; ?>
	<br />
	<?
		$form=$this->beginWidget('CActiveForm', array(
					'id'=>'contact',
					'focus'=>array($model,'username'),
					'enableAjaxValidation'=>true,
					'errorMessageCssClass'=>'alertBox-alert',
				));
			echo $form->errorSummary($model, '', '', array('class'=>'red-box')); ?>
		<br />		
	<?		echo $form->TextField($model, 'username', array('id'=>'email', 'placeholder'=>'Email', 'style'=>'margin-right: 15px;'));
			echo $form->PasswordField($model, 'password', array('id'=>'password', 'placeholder'=>'Password'));
			echo CHtml::submitButton('Login', array('class'=>'btn btn-primary', 'style'=>'margin-top: -9px; margin-left: 10px;'));
		$this->endWidget(); 
	?>
	<?php echo CHtml::link('Forgot your password?', Yii::app()->createUrl('/forgot'), array('style'=>'float:right;')); ?>
</div>

<div class="well span5">
	<h4>Why do I need an account?</h4>
	<div class="horizontal-rule"></div>
	 <p>Registering on Erianna.com gives you access to many features not available to unregistered users. Registered users can:</p>
	 <br />
    	<ul class="green-tick">
	    	<li>Participate on community discussions.</li>
	    	<li>Comment on new articles.</li>
	    	<li>And much more!</li>
	    </ul>
    <hr />
    <p>What are you waiting for? <strong><?php echo CHtml::link('Sign up', Yii::app()->createUrl('/register')); ?></strong> for an account today!</p>

</div>

<?php Yii::app()->clientScript->registerCss('one-half', '.one-half{margin-top: 0px;}'); ?>