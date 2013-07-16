<div class="dashboard">
	<div class="header">
		<div class="content">
			<div class="welcome">
				<strong>Welcome Back, </strong> <?php echo Yii::app()->user->displayName; ?>
			</div>
			<div class="header-nav">
				<?php echo CHtml::link('<span class="icon-pencil"></span> New Post', $this->createUrl('/dashboard/content/save')); ?>
				<?php echo CHtml::link('<span class="icon-bar-chart"></span> Analytics', $this->createUrl('/dashboard/analytics')); ?>
				<?php echo CHtml::link('<span class="icon-search"></span> Search', '#'); ?>
			</div>
		</div>
	</div>
	<div class="clearfix push-header"></div>

	<?php Yii::app()->clientScript->registerScriptFile($this->asset.'/shapeshift/core/vendor/jquery.touch-punch.min.js', CClientScript::POS_END)
								  ->registerScriptFile($this->asset.'/shapeshift/core/jquery.shapeshift.js', CClientScript::POS_END); ?>
	<div class="widget-container">
	    <div style="width: 482px; height:  230px;" data-ss-colspan="2"></div>
	    <div></div>
	    <div></div>
	    <div></div>
	    <div></div>
	    <div></div>
	    <div style="width: 482px; height:  482px;" data-ss-colspan="2"></div>
	    <div></div>
	    <div></div>
	    <div></div>
	    <div></div>
	    <div></div>
	    <div></div>
	</div>
</div>

<?php Yii::app()->clientScript->registerScript('widgetLoader', '
$(document).ready(function() {
      $(".widget-container").shapeshift({
        minColumns: 3,
        gutterX: 20,
        gutterY: 20,
        paddingX: 0,
        paddingY: 0
      });
    })
'); ?>