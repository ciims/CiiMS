<?php
	$notifyUser  = new stdClass;
	$notifyUser->email       = Cii::getConfig('notifyEmail', NULL);
	$notifyUser->displayName = Cii::getConfig('notifyName',  NULL);
	if ($notifyUser->email == NULL && $notifyUser->displayName == NULL)
	    $notifyUser = Users::model()->findByPk(1);
?>
<div class="login-container" style="width: 70%;">
	<div class="sidebar">
		<div class="well-span">
			<h4><?php echo Yii::t('main', 'Thanks for Registering!'); ?></h4>
			<p><?php echo Yii::t('main', "Before you can login to your account we need you to verify your email address. Be on the lookout for an email from {{email}} containing activating instructions.", array(
				'{{email}}' => CHtml::tag('strong', array(), $notifyUser->email)
			)); ?></p>
		</div>
	</div>
</div>
