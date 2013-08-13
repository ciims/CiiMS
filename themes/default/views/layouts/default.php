<?php $this->beginContent('//layouts/main'); ?>
	<div class="span8">
		<?php echo $content; ?>
	</div>
	<div class="span4 sidebar hidden-phone">
		<div class="well">
			<h4><?php echo Yii::t('DefaultTheme', 'Search'); ?></h4>
			<?php echo CHtml::beginForm($this->createUrl('/search'), 'get', array('id' => 'search')); ?>
                <div class="input-append">
                    <?php echo CHtml::textField('q', Cii::get($_GET, 'q', ''), array('type' => 'text', 'style' => 'width: 97%', 'placeholder' => Yii::t('DefaultTheme', 'Type to search, then press enter'))); ?>
                </div>
            <?php echo CHtml::endForm(); ?>
		</div>
		<div class="well">
			<h4><?php echo Yii::t('DefaultTheme', 'Recent Posts'); ?></h4>
			<?php $this->widget('cii.widgets.CiiMenu', array(
                'items' => $this->params['theme']->getRecentPosts()
            )); ?>
		</div>
	</div>
<?php $this->endContent(); ?>