<div class="header secondary">
	<fieldset>
		<legend>Send a Test Email</legend>
		<div class="pure-control-group">
			<label for="EmailSettings_Test">Email To Test</label>
			<input class="pure-input-1-2" id="EmailSettings_Test" type="email" placeholder="user@example.com" />
			<?php echo CHtml::tag('span', array('id' => 'test-email', 'class' => 'pure-button pure-button-error pure-button-small pure-button-link'), 'Test Email'); ?>
		</div>
	</fieldset>
</div>

<?php Yii::app()->getClientScript()->registerScript('test-email', '
	$("#test-email").click(function() {
		var testaddress = $("#EmailSettings_Test").val();
		$.post("' . $this->createUrl('/dashboard/settings/emailtest') . '", { email : testaddress }, function(data, textStatus, jqXHR) { 
			console.log(data);
		});
	});
'); ?>