<div class="form">
	<div class="header">
		<div class="pull-left">
			<p><?php echo $header['h3']; ?></p>
		</div>
	    <form class="pure-form pull-right header-form">
			<span class="fa fa-search pull-right icon-legend"></span>
			<input id="text" name="text" class="pull-right pure-input pure-search pure-search-alt" placeholder="<?php echo Yii::t('Dashboard.views', 'Search for Cards...'); ?>" type="text">
		</form>
		<div class="clearfix"></div>
	</div>

	<div id="main" class="nano pure-form pure-form-aligned">
		<div class="content pure-form">
            <!-- Carousel Slider for Cards -->
            <div class="carousel-container">
	            <a href="#" class="jcarousel-control-prev">&lsaquo;</a>
		            <div class="jcarousel-wrapper">
		                <div class="jcarousel">
		                    <div class="loading"><?php echo CHtml::image($this->asset . '/jcarousel-master/carousel-preloader.gif'); ?></div>
		                </div>
		            </div>
				<a href="#" class="jcarousel-control-next">&rsaquo;</a>
			</div>
			<div class="clearfix"></div>

			<legend><?php echo Yii::t('Dashboard.views', 'Uninstalled Cards'); ?></legend>
			<div class="meta-container" id="uninstalled-container">

				<div class="no-items-notification center" id="uninstalled-notifier" style="display:none;"><?php echo Yii::t('Dashboard.main', "All cards associated to this instance are currently installed."); ?></div>
				<span class="install" style="display:none;"><?php echo Yii::t('Dashboard.main', 'Install Card'); ?></span>
				<span class="installing" style="display:none;"><?php echo Yii::t('Dashboard.main', 'Installing Card...'); ?></span>
				<span class="unregister" style="display:none;"><?php echo Yii::t('Dashboard.main', 'Unregister'); ?></span>
			</div>
			
            <!-- other stuff -->
			<legend><?php echo Yii::t('Dashboard.views', 'Active Cards'); ?></legend>
			<div class="meta-container">

				<div class="no-items-notification center" id="reload-notifier" style="display:none;">
					<?php echo Yii::t('Dashboard.main', 'New cards have been installed! Reload the page to manage these new cards.'); ?>
					<div class="clearfix"></div>
				</div>

				<?php if (empty($cards)): ?>
					<div class="no-items-notification center" id="installed-notifier"><?php echo Yii::t('Dashboard.main', "There are currently no cards installed. Search for cards above to add them!"); ?></div>
				<?php endif; ?>
				<div class="clearfix"></div>

				<?php foreach($cards as $card): ?>
					<?php $card->value = CJSON::decode($card->value); ?>
					<div class="pure-control-group">
						<p class="text-small text-small-inline inline"><?php echo $card->value['name']; ?></p>
						<span class="pure-button pure-button-error pure-button-xsmall pure-button-link-xs pull-right remove-button" id="<?php echo $card->key; ?>">
							<span class="fa fa-times"></span>
						</span>
						<span class="pure-button pure-button-warning pure-button-xsmall pure-button-link-xs pull-right">
							<?php echo Cards::model()->countByAttributes(array('name' => $card->key)); ?>
						</span>
						<span class="pure-button pure-button-primary pure-button-xsmall pure-button-link-xs pull-right" id="updater" data-attr-id="<?php echo $card->key; ?>">
							<span class="fa fa-spinner fa-spin icon-spinner"></span>
							<span class="checking"><?php echo Yii::t('Dashboard.main', 'Checking for Updates'); ?></span>
							<span class="uptodate" style="display:none;"><?php echo Yii::t('Dashboard.main', 'Up to Date!'); ?></span>
							<span class="available" style="display:none;"><?php echo Yii::t('Dashboard.main', 'Click to Update'); ?></span>
							<span class="updating" style="display:none;"><?php echo Yii::t('Dashboard.main', 'Updating...'); ?></span>
							<span class="updating-error" style="display:none;"><?php echo Yii::t('Dashboard.main', 'Unable to Update'); ?></span>
						</span>
						<div class="clearfix"></div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>

<div class="ow-overlay ow-closed"></div> 
<div class="modal ow-closed"></div>
<?php Yii::app()->clientScript->registerScriptFile($this->asset.'/jcarousel-master/dist/jquery.jcarousel.min.js'); ?>
<?php Yii::app()->clientScript->registerScriptFile($this->asset.'/js/jquery.omniwindow.min.js'); ?>
<?php Yii::app()->clientScript->registerCssFile($this->asset.'/css/pure.css');  ?>
