<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->baseUrl.$asset; ?>/css/card.css" />
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->baseUrl.$asset; ?>/climacons/webfont/climacons-font.css" />

<div id="FcAlexkTPM" data-attr-id="<?php echo $model->id; ?>">
	<div class="card-header">
		<span class="location"></span>
		<div class="temperature">
			<span class="degrees"></span>
			<span class="icon climacon farenheit"></span>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="card-body">
		<div class="climacon weather"></div>
		<div class="details-container">
			<span class="details"></span>
		</div>
	</div>
</div>