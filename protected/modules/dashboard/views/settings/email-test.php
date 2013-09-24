<div class="header secondary">
	<fieldset>
		<div class="alert-secondary alert in alert-block fade alert-error" style="display:none">
			<a class="close" data-dismiss="alert">×</a>
		</div>
		<legend><?php echo Yii::t('Dashboard.views', 'Send a Test Email'); ?></legend>
		<div class="pure-control-group">
			<label for="EmailSettings_Test"><?php echo Yii::t('Dashboard.views', 'Email To Test'); ?></label>
			<input class="pure-input-1-2" id="EmailSettings_Test" type="email" placeholder="user@example.com" no-field-change="true"/>
			<?php echo CHtml::tag('span', array('id' => 'test-email', 'class' => 'pure-button pure-button-error pure-button-small pure-button-link'), Yii::t('Dashboard.views', 'Test Email')); ?>
		</div>
	</fieldset>
</div>