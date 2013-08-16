<?php $form = $this->beginWidget('cii.widgets.CiiActiveForm'); ?>
	<div class="header">
		<div class="pull-left">
			<h3><?php echo $header['h3']; ?></h3>
			<p><?php echo $header['p']; ?></p>
		</div>
		<div class="pull-right">
			<span id="header-button" class="pure-button pure-button-error pure-button-link pure-button-small">
				<span id="spinner">
					<span class="icon-spinner icon-spin icon-spinner-form"></span>
					<span class="icon-spacer"></span>
				</span>
				<?php echo Yii::t('Dashboard.views', 'Flush CiiMS Cache'); ?></span>
		</div>
		<div class="clearfix"></div>
	</div>

	<div id="main" class="nano pure-form pure-form-aligned">
		<div class="content">

			<!-- System Information -->
			<div class="pull-left span7">
				<legend><span class="icon-info-sign"></span> <?php echo Yii::t('Dashboard.views', 'System Information'); ?></legend>
				<div class="pull-left">
					<div class="pure-control-group">
						<label><?php echo Yii::t('Dashboard.views', 'PHP Version'); ?></label> 
						<span class="inline"><?php echo phpversion(); ?></span>
					</div>
					<div class="pure-control-group">
						<label><?php echo Yii::t('Dashboard.views', 'Web Server'); ?></label>
						<span class="inline"><?php echo $_SERVER['SERVER_SOFTWARE']; ?></span>
					</div>
					<div class="pure-control-group">
						<label><?php echo Yii::t('Dashboard.views', 'Database'); ?></label>
						<span class="inline"><?php echo ucwords(Yii::app()->db->driverName) . ' ' . Yii::app()->db->serverVersion; ?></span>
					</div>
				</div>
				<div class="pull-left">
					<div class="pure-control-group">
						<label><?php echo Yii::t('Dashboard.views', 'Yii Version'); ?></label>
						<span class="inline"><?php echo Yii::getVersion(); ?></span>
					</div>
					<div class="pure-control-group">
						<label><?php echo Yii::t('Dashboard.views', 'CiiMS Version'); ?></label>
						<span class="inline"><?php echo Cii::getVersion(); ?></span>
					</div>
					<div class="pure-control-group">
						<label><?php echo Yii::t('Dashboard.views', 'Cache'); ?></label>
						<span class="inline"><?php echo get_class(Yii::app()->cache); ?></span>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>

			<div class="pull-left span7">
				<legend><span class="icon-warning-sign"></span> <?php echo Yii::t('Dashboard.views', 'Issues With CiiMS'); ?></legend>
				<div class="issues"></div>
			</div>
		</div>
	</div>
<?php $this->endWidget(); ?>