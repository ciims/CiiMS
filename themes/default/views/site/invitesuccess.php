<div class="login-container" style="width: 70%;">
	<div class="sidebar">
		<div class="well-span">
			<h4><?php echo Yii::t('DefaultTheme', 'Thanks for Activating Your Account!'); ?></h4>
			<p><?php echo Yii::t('DefaultTheme', "You may now {{login}} and head to the {{dashboard}}", array(
				'{{login}}' => CHtml::link(Yii::t('DefaultTheme', 'login'), $this->createUrl('/login')),
				'{{dashboard}}' => CHtml::link(Yii::t('DefaultTheme', 'dashboard'), $this->createUrl('/dashboard'))
			)); ?></p>
		</div>
	</div>
</div>
