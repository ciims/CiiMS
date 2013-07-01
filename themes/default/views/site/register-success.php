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
			<h4>Thanks for Registering!</h4>
			<p>Before you can login to your account we need you to verify your email address. Be on the lookout for an email from: <?php echo $notifyUser->email; ?> containing activating instructions.</p>
		</div>
	</div>
</div>
