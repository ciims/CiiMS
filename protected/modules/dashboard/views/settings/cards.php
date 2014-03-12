<?php $form = $this->beginWidget('cii.widgets.CiiActiveForm', array(
	'htmlOptions' => array(
		'class' => 'pure-form pure-form-aligned form-vertical'
	)
)); ?>
	<div class="header">
		<div class="pull-left">
			<p><?php echo $header['h3']; ?></p>
		</div>
		<div class="clearfix"></div>
	</div>

	<div id="main" class="nano">
		<div class="content">

			<legend><?php echo Yii::t('Dashboard.views', 'Active Cards'); ?></legend>
			<div class="meta-container">
				<?php foreach($cards as $card): ?>
					<?php $card->value = CJSON::decode($card->value); ?>
					<div class="pure-control-group">
						<?php echo CHtml::tag('label', array('class' => 'inline'), Cii::titleize($card->value['class'])); ?>
						<?php $count = Cards::model()->countByAttributes(array('name' => $card->key)); ?>
						<p class="text-small inline" style="top: -8px;"><?php echo $card->value['name']; ?></p>
						<span class="pure-button pure-button-error pure-button-xsmall pure-button-link-xs pull-right remove-button" id="<?php echo $card->key; ?>">
							<span class="icon-remove"></span>
						</span>
						<span class="pure-button pure-button-warning pure-button-xsmall pure-button-link-xs pull-right">
							<?php echo $count; ?>
						</span>
						<span class="pure-button pure-button-primary pure-button-xsmall pure-button-link-xs pull-right" id="updater" data-attr-id="<?php echo $card->key; ?>">
							<span class="icon-spinner icon-spin"></span>
							<span class="checking"><?php echo Yii::t('Dashboard.main', 'Checking for Updates'); ?></span>
							<span class="uptodate" style="display:none;"><?php echo Yii::t('Dashboard.main', 'Up to Date!'); ?></span>
							<span class="available" style="display:none;"><?php echo Yii::t('Dashboard.main', 'Click to Update'); ?></span>
							<span class="updating" style="display:none;"><?php echo Yii::t('Dashboard.main', 'Updating...'); ?></span>
							<span class="updating-error" style="display:none;"><?php echo Yii::t('Dashboard.main', 'Unable to Update'); ?></span>
						</span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
<?php $this->endWidget(); ?>