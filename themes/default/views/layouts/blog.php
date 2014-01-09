<?php $this->beginContent('//layouts/main'); ?>
	<div class="content">
		<div class="pure-u-2-3 content-inner">
			<?php echo $content; ?>
		</div>
		<div class="sidebar pure-u-1-3 pull-right">
			<?php echo CHtml::beginForm($this->createUrl('/search'), 'get', array('id' => 'search', 'class' => 'pure-form')); ?>
            	<?php echo CHtml::textField('q', Cii::get($_GET, 'q', ''), array('type' => 'text', 'class' => 'pure-u-1', 'placeholder' => Yii::t('DefaultTheme.main', 'Type to search, then press enter'))); ?>
            <?php echo CHtml::endForm(); ?>

			<h4><?php echo Yii::t('DefaultTheme.main', 'Related Posts'); ?></h4>
			<?php $this->widget('zii.widgets.CMenu', array('items' => $this->params['theme']->getRelatedPosts($this->params['data']['id'], $this->params['data']['category_id']))); ?>
		
			<!-- Tag Cloud -->
			<?php if ($items = $this->getContentTags($this->params['data']['id'])): ?>
				<h4><?php echo Yii::t('DefaultTheme.main', 'Tags'); ?></h4>
				<?php $this->widget('zii.widgets.CMenu', array('items' => $items)); ?>
			<?php endif; ?>

            <div class="addthis">
				<?php if( Cii::getConfig('addThisPublisherID') != ''): ?>
					<!-- AddThis Smart Layers BEGIN -->
					<!-- Go to http://www.addthis.com/get/smart-layers to customize -->
					<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Cii::getConfig('addThisPublisherID'); ?>"></script>
					<script type="text/javascript">
					  addthis.layers({
					    'theme' : 'light',
					    'share' : {
					      'position' : 'right',
					      'numPreferredServices' : 5,
					      'services' : 'facebook,twitter,linkedin,google_plusone_share,more'
					    },
					    'visible' : 'smart'
					  });
					</script>
					<!-- AddThis Smart Layers END -->
				<?php endif; ?>
			</div>

		</div>
	</div>
	<div class="clearfix"></div>
<?php $this->endContent(); ?>