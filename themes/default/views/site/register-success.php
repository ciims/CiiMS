<div class="login-container" style="width: 70%;">
	<div class="sidebar">
		<div class="well-span">
			<h4><?php echo Yii::t('DefaultTheme', 'Thanks for Registering!'); ?></h4>
			<p><?php echo Yii::t('DefaultTheme', "Before you can login to your account we need you to verify your email address. Be on the lookout for an email from {{email}} containing activating instructions.", array(
				'{{email}}' => CHtml::tag('strong', array(), $notifyUser->email)
			)); ?></p>
		</div>
	</div>
</div>
