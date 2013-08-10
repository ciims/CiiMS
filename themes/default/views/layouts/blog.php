<?php $this->beginContent('//layouts/main'); ?>
	<div class="span8">
		<?php echo $content; ?>
	</div>
	<div class="span4 sidebar hidden-phone">
		<div class="well">
			<h4>Search</h4>
			<?php echo CHtml::beginForm($this->createUrl('/search'), 'get', array('id' => 'search')); ?>
                <div class="input-append">
                    <?php echo CHtml::textField('q', Cii::get($_GET, 'q', ''), array('type' => 'text', 'style' => 'width: 97%', 'placeholder' => 'Type to search, then press enter')); ?>
                </div>
            <?php echo CHtml::endForm(); ?>
		</div>
		
		<!-- AddThis -->
		<div class="addthis">
			<?php if( Cii::getConfig('addThisPublisherID') != ''): ?>
				<!-- AddThis Smart Layers BEGIN -->
				<!-- Go to http://www.addthis.com/get/smart-layers to customize -->
				<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Cii::getConfig('addThisPublisherID'); ?>"></script>
				<script type="text/javascript">
				  addthis.layers({
				    'theme' : 'dark',
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
		<!-- Related Posts -->
		<div class="well">
			<h4>Related Posts</h4>
			<?php $this->widget('cii.widgets.CiiMenu', array('items' => $this->getRelatedPosts())); ?>
		</div>
		
		<!-- Tag Cloud -->
		<?php if ($this->getContentTags()): ?>
			<div class="well tags">
				<h4>Tags</h4>
				<?php $this->widget('bootstrap.widgets.TbMenu', array('items' => $this->getContentTags())); ?>
			</div>
		<?php endif; ?>
		
	</div>
<?php $this->endContent(); ?>
