<div id="posts">
    <?php foreach($data as $content): ?>
    	<?php $this->renderPartial('//content/_post', array('content' => $content)); ?>
    <?php endforeach; ?>
</div>

<?php if (count($data)): ?>
	<?php $this->widget('ext.yiinfinite-scroll.YiinfiniteScroller', array(
	    'url'=>isset($url) ? $url : 'blog',
	    'contentSelector' => '#posts',
	    'itemSelector' => 'div.post',
	    'loadingText' => 'Loading...',
	    'donetext' => '&nbsp;',
	    'pages' => $pages,
	)); ?>
	<?php Yii::app()->clientScript->registerScript('unbind-infinite-scroll', "$(window).unbind('.infscr');"); ?>
<?php else: ?>
	<div class="post">
		<?php if (Cii::get(Cii::get($meta, 'blog-image', array()), 'value', '') != ""): ?>
			<p style="text-align:center;"><?php echo CHtml::image(Yii::app()->baseUrl . $meta['blog-image']['value'], NULL, array('class'=>'image')); ?></p>
		<?php endif; ?>
		<div class="post-inner">
			<div class="post-header">
				<h4>There are no posts in this category.</h4>
			</div>
		</div>
	    <div style="clear:both;"><br /></div>
	</div>
<?php endif; ?>