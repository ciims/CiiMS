<div class="posts-container">
	<div id="posts">
		<div class="header">
			<?php echo CHtml::link(NULL, $this->createUrl('/dashboard/content/save'), array('class' => 'icon-plus')); ?>
			All Posts
			<span class="icon-search pull-right"></span>
		</div>
		<?php foreach ($data as $post): ?>
			<?php echo $this->renderPartial('post', array('data' => $post)); ?>
		<?php endforeach; ?>
	</div>
	<div class="preview">
		
	</div>
	<div class="clearfix"></div>
</div>

<?php $this->widget('ext.yiinfinite-scroll.YiinfiniteScroller', array(
    'url' => 'dashboard/content',
    'contentSelector' => '#posts',
    'pages' => $pages
)); ?>
<?php Yii::app()->clientScript->registerScript('unbind-infinite-scroll', "$(window).unbind('.infscr');"); ?>