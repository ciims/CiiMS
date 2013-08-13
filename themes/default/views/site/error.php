<div class="login-container">
	<div class="sidebar">
		<div class="well-span">
			<h1><?php echo Yii::t('main', 'Error {{code}}', array('{{code}}' => $error['code'])); ?></h1>
		<p><?php echo $error['message']; ?></p>
		</div>
	</div>
</div>