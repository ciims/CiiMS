<script type="text/javascript" src="<?php echo Yii::app()->baseUrl.$asset; ?>/js/card.js" async></script>
<script type="text/javascript">
	$(document).ready(function() {
		FcAlexkTPM.load(<?php echo $model->id; ?>);
	});
</script>
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->baseUrl.$asset; ?>/css/card.css" />
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->baseUrl.$asset; ?>/climacons/webfont/climacons-font.css" />


<div id="FcAlexkTPM" data-attr-id="<?php echo $model->id; ?>">
	<div class="card-header">
		<span class="location"></span>
		<span class="temperature">
			<span class="icon climacon farenheit"></span>
			<span class="degrees"></span>
		</span>
	</div>

	<div class="card-body">

	</div>
</div>