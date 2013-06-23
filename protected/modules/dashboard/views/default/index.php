<div class="dashboard">
	<div class="header">
		<div class="content">
			<div class="welcome">
				<strong>Welcome Back, </strong> <?php echo Yii::app()->user->displayName; ?>
			</div>
			<div class="header-nav">
				<?php echo CHtml::link('<span class="icon-pencil"></span> New Post', $this->createUrl('/dashboard/content/save')); ?>
				<?php echo CHtml::link('<span class="icon-group"></span> Authors', $this->createUrl('/dashboard/authors')); ?>
				<?php echo CHtml::link('<span class="icon-bar-chart"></span> Analytics', $this->createUrl('/dashboard/analytics')); ?>
				<?php echo CHtml::link('<span class="icon-search"></span> Search', '#'); ?>
			</div>
		</div>
	</div>
	<div class="clearfix push-header"></div>

	<?php $asset=Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('application.modules.dashboard.assets'), true, -1, YII_DEBUG); ?>
	<?php Yii::app()->clientScript
								  ->registerScriptFile($asset.'/shapeshift/core/vendor/jquery.touch-punch.min.js', CClientScript::POS_END)
								  ->registerScriptFile($asset.'/shapeshift/core/jquery.shapeshift.js', CClientScript::POS_END); ?>

  <!-- Javascript -->
    <script>
    $(document).ready(function() {
      $(".widget-container").shapeshift({
        minColumns: 3,
        gutterX: 20,
        gutterY: 20,
        paddingX: 0,
        paddingY: 0
      });
    })
    </script>
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