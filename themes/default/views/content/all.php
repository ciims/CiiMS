<div id="posts">
	<?php $md = new CMarkdownParser; ?>
    <?php foreach($data as $content): ?>
    	<?php $this->renderPartial('//content/_post', array('content' => $content, 'md' => $md)); ?>
    <?php endforeach; ?>
</div>

<?php if (count($data)): ?>
	<?php $this->widget('ext.yiinfinite-scroll.YiinfiniteScroller', array(
	    'url'=>isset($url) ? $url : 'blog',
	    'contentSelector' => '#posts',
	    'pages' => $pages,
	    'defaultCallback' => "js:function(response, data) {
	    	DefaultTheme.infScroll(response, data);
	    	setTimeout(function() {
	    		DefaultTheme.Blog.loadDisqusCommentCount(disqus_shortname); 
	    	}, 500);
 		}"
	)); ?>
	<?php Yii::app()->clientScript->registerScript('unbind-infinite-scroll', "Theme.loadAll();"); ?>
	<?php if (Cii::getConfig('useDisqusComments')): ?>
		<?php $shortname = Cii::getConfig('disqus_shortname'); ?>
		<?php Yii::app()->clientScript->registerScript('loadComments', "Theme.Blog.loadDisqusCommentCount(\"{$shortname}\");"); ?>
	<?php endif; ?>
<?php else: ?>
	<div class="alert alert-info">
		<?php echo Yii::t('DefaultTheme', "{{woah}} It looks like there aren't any posts in this category yet. Why don't you check out some of our other pages or check back later?", 
			array(
				'{{woah}}' => CHtml::tag('strong', array(), Yii::t('DefaultTheme', 'Woah!'))
		)); ?>
	</div>
<?php endif; ?>