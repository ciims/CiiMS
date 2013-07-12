<div class="header secondary">
	<div class="pure-control-group">
		<p>Use this form to send a test email address to verify that CiiMS can send emails.</p>
		<label for="EmailSettings_Test">Email To Test</label>
		<input class="pure-input-1-2" id="EmailSettings_Test" type="email" placeholder="user@example.com" />
		<?php echo CHtml::tag('span', array('id' => 'test-email', 'class' => 'pure-button pure-button-error pure-button-small pure-button-link'), 'Test Email'); ?>
	</div>
</div>

<?php Yii::app()->getClientScript()->registerScript('test-email', '
	$("#test-email").click(function() {
		var testaddress = $("#EmailSettings_Test").val();
		$.post("' . $this->createUrl('/dashboard/settings/emailtest') . '", { email : testaddress }, function(data, textStatus, jqXHR) { 
			console.log(data);
		});
	});
'); ?>