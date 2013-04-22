<div id="posts">
    <?php foreach($data as $content): ?>
    	<?php $this->renderPartial('//content/_post', array('content' => $content)); ?>
    <?php endforeach; ?>
</div>

<?php if (count($data)): ?>
	<?php $this->widget('ext.yiinfinite-scroll.YiinfiniteScroller', array(
	    'path'=>isset($url) ? $url : 'blog',
	    'pages' => $pages,
	    'errorCallback' => 'js:function() { $(".infinite_navigation").hide(); }',
	    'callback' => '
	    	// Keep the selector shown
	    	$(".infinite_navigation").show();
	    	// Send GA/Piwik Analytics Tracking
	    	//console.log(data);
	    	console.log(this);
	    '
	)); ?>
	<?php //Yii::app()->clientScript->registerScript('unbind-infinite-scroll', "$(window).unbind('.infscr');"); ?>
<?php else: ?>
	<div class="alert alert-info">
		<strong>Woah!</strong> It looks like there isn't any posts in this category yet. Why don't you check out some of our other pages or check back later?
	</div>
<?php endif; ?>