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
		<?php 
			$addThisExtension = Configuration::model()->findByAttributes(array('key'=>'addThisExtension'));
			if (isset($addThisExtension->value) && $addThisExtension->value == 1): ?>
				<div class="well">
					<h4>Share This</h4>
				<?php $this->widget('ext.analytics.EAddThisWidget', 
					array(
						'account'=>Configuration::model()->findByAttributes(array('key'=>'addThisAccount'))->value,
					)); ?>
				</div>
		<?php endif; ?>
	</div>
<?php $this->endContent(); ?>
