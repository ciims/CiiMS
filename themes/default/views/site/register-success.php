<div class="login-container" style="width: 70%;">
	<div class="sidebar">
		<div class="well-span">
			<h4>Thanks for Registering!</h4>
			<p>Before you can login to your account we need you to verify your email address. Be on the lookout for an email from: <?php echo Users::model()->findByPk(1)->email; ?> containing activating instructions.</p>
		</div>
	</div>
</div>
