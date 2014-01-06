<?php $this->beginContent('//layouts/main'); ?>
	<div class="content">
		<div class="pure-u-2-3 content-inner">
			<?php echo $content; ?>
		</div>
		<div class="sidebar pure-u-1-3 pull-right">
			<?php echo CHtml::beginForm($this->createUrl('/search'), 'get', array('id' => 'search', 'class' => 'pure-form')); ?>
            	<?php echo CHtml::textField('q', Cii::get($_GET, 'q', ''), array('type' => 'text', 'class' => 'pure-u-1', 'placeholder' => Yii::t('DefaultTheme', 'Type to search, then press enter'))); ?>
            <?php echo CHtml::endForm(); ?>


            <h4><?php echo Yii::t('DefaultTheme', 'Categories'); ?></h4>
			<?php $this->widget('zii.widgets.CMenu', array(
                'items' => $this->params['theme']->getCategories()
            )); ?>

            <h4><?php echo Yii::t('DefaultTheme', 'Recent Posts'); ?></h4>
			<?php $this->widget('zii.widgets.CMenu', array(
                'items' => $this->params['theme']->getRecentPosts()
            )); ?>
		</div>
	</div>
	<div class="clearfix"></div>
<?php $this->endContent(); ?>