<h3>
	<?php echo Yii::t('Install.main', '{install} Yii Framework', array(
		'{install}' => Yii::tag('span', array('class' => 'highlight'), 'Installing')
	)); ?>
</h3>
<hr />

<p>
	<?php echo Yii::t('Install.main', 'CiiMS is now installing Yii Framework for you. This may take a few minutes depending upon your connection speed.'); ?>
</p>

<hr />
<h3 id="inprogress">
	<?php echo Yii::t('Install.main', 'Installation in Progress...'); ?>
</h3>
<div id="done" style="display:none">
	<h3>
		<?php echo Yii::t('Install.main', 'Installation Complete!'); ?>
	</h3>
	<p>
		<?php echo Yii::t('Install.main', 'Please press the button below to continue with the installation.'); ?>
	</p>
</div>
<div class="progress progress-striped active">
	<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 1%">
	</div>
</div>

<a class="pure-button pure-button-disabled" href="?stage=4">Continue installation</a>

<script type="text/javascript">
$(document).ready(function() {
	progress = 0;
	// Initiate the download
	$.post('', { 
	    	_ajax : true, 
	        _method : 'initYiiDownload', 
	        data : { 
	            remote : "<?php echo $GLOBALS['helper']->config['params']['yiiDownloadPath']; ?>",
	            version: "<?php echo $GLOBALS['helper']->config['params']['yiiVersionPath']; ?>",
                hostname: window.location.origin
	        }
	    },
	    function(data) {
	        progress = 100;
	        $(".progress-bar").css('width', progress + '%');
	        if (!data.completed)
	            window.location = '?stage=10';
	        else 
	        {
	            $(".pure-button").removeClass('pure-button-disabled').addClass('pure-button-primary');
	            $(".progress-bar").removeClass("progress-bar-warning").addClass("progress-bar-success")
	            $("#inprogress").hide();
	            $("#done").show();
	        }
	        
	        window.clearInterval(interval);
	});

	interval = setInterval(function() {
	  progress++;
	  if (progress >= 98)
	    progress = 98;
	  $(".progress-bar").css('width', progress + '%');
	}, 200);

});
</script>
