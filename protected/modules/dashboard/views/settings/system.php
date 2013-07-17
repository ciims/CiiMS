<?php $form = $this->beginWidget('cii.widgets.CiiActiveForm'); ?>
	<div class="header">
		<div class="pull-left">
			<h3><?php echo $header['h3']; ?></h3>
			<p><?php echo $header['p']; ?></p>
		</div>
		<div class="pull-right">
			<span id="header-button" class="pure-button pure-button-error pure-button-link pure-button-small">Flush CiiMS Cache</span>
		</div>
		<div class="clearfix"></div>
	</div>

	<div id="main" class="nano">
		<div class="content">

		</div>
	</div>
<?php $this->endWidget(); ?>

<?php Yii::app()->getClientScript()->registerScript('flush-cache-button', '
	$("#header-button").click(function() {
		// Fire off an in progress behavior
		
		$.post("flushcache", function(data, textStatus) {
			if (textStatus == "success")
			{
				// Do something to indicate it was successful
			}
			else
			{
				// Do something to indicate it failed
			}

			// Stop the "in progress" behavior
		});
	});
'); ?>
