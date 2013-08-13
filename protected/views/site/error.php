<div class="sidebar">
	<div class="well">
		<h1><?php echo Yii::t('ciims.Controllers.Site', 'Error {{code}}', array('{{code}}' => $error['code'])); ?></h1>
		<p><?php echo $error['message']; ?></p>
	</div>
</div>