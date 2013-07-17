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

	<div id="main" class="nano pure-form pure-form-aligned">
		<div class="content">

			<!-- System Information -->
			<div class="pull-left span7">
				<legend><span class="icon-info-sign"></span> System Information</legend>
				<div class="pull-left">
					<div class="pure-control-group">
						<label>PHP Version</label> 
						<span class="inline"><?php echo phpversion(); ?></span>
					</div>
					<div class="pure-control-group">
						<label>Web Server</label>
						<span class="inline"><?php echo $_SERVER['SERVER_SOFTWARE']; ?></span>
					</div>
					<div class="pure-control-group">
						<label>Database</label>
						<span class="inline"><?php echo ucwords(Yii::app()->db->driverName) . ' ' . Yii::app()->db->serverVersion; ?></span>
					</div>
				</div>
				<div class="pull-left">
					<div class="pure-control-group">
						<label>Yii Version</label>
						<span class="inline"><?php echo Yii::getVersion(); ?></span>
					</div>
					<div class="pure-control-group">
						<label>CiiMS Version</label>
						<span class="inline"><?php echo Cii::getVersion(); ?></span>
					</div>
					<div class="pure-control-group">
						<label>Cache</label>
						<span class="inline"><?php echo get_class(Yii::app()->cache); ?></span>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>

			<div class="pull-left span7">
				<legend><span class="icon-warning-sign"></span> Issues With CiiMS</legend>
				<div class="issues"></div>
			</div>
		</div>
	</div>
<?php $this->endWidget(); ?>

<?php Yii::app()->getClientScript()->registerScript('system-settings', '
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

	$(document).ready(function() {
		$.get("getissues", function(data) {
			$(".issues").html(data);
		});
	});
'); ?>
