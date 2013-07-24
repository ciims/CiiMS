<div class="dashboard">
	<div class="header">
		<div class="content">
			<div class="welcome">
				<strong>Welcome Back, </strong> <?php echo Yii::app()->user->displayName; ?>
			</div>
			<div class="header-nav">
				<?php echo CHtml::link('<span class="icon-pencil"></span> New Post', $this->createUrl('/dashboard/content/save')); ?>
				<?php echo CHtml::link('<span class="icon-search"></span> Search', '#'); ?>
			</div>
			<?php echo CHtml::tag('span', array('id' => 'add-card', 'class' => 'icon-plus pull-right'), NULL); ?>
		</div>
	</div>
	<div class="clearfix push-header"></div>

	<?php Yii::app()->clientScript->registerScriptFile($this->asset.'/shapeshift/core/vendor/jquery.touch-punch.min.js', CClientScript::POS_END)
								  ->registerScriptFile($this->asset.'/shapeshift/core/jquery.shapeshift.js', CClientScript::POS_END); ?>

	<div class="widget-container">

		<!-- Rectangle -->
	    <div class="card-rectangle" data-ss-colspan="2" id="widget-unique-id">
	    	<?php echo CHtml::openTag('div', array('class' => 'body')); ?>
	    		<!-- Body content goes here -->
	    	<?php echo CHtml::closeTag('div'); ?>
	    	<?php echo CHtml::openTag('div', array('class' => 'footer')); ?>
	    		<?php echo CHtml::tag('span', array('class' => 'pull-left footer-text'), 'Forecast.io'); ?>
	    		<?php echo CHtml::tag('span', array('class' => 'icon-resize-full pull-right icon-padding'), NULL); ?>
	    		<?php echo CHtml::tag('span', array('class' => 'icon-trash pull-right icon-padding'), NULL); ?>
	    		<?php echo CHtml::tag('span', array('class' => 'icon-pencil pull-right icon-padding'), NULL); ?>
	    	<?php echo CHtml::closeTag('div'); ?>
	    <?php echo CHtml::closeTag('div'); ?>

	    <!-- Large Box -->
	    <div class="card-huge" data-ss-colspan="2"></div>

	    <!-- Default -->
	    <div class="card-normal" data-ss-colspan="1"></div>
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
    });

'); ?>