<div id="posts">
    <?php foreach($data as $content): ?>
    	<?php $this->renderPartial('//content/_post', array('content' => $content)); ?>
    <?php endforeach; ?>
</div>

<?php if (count($data)): ?>
	<?php $this->widget('ext.yiinfinite-scroll.YiinfiniteScroller', array(
	    'url'=>isset($url) ? $url : 'blog',
	    'contentSelector' => '#posts',
	    'pages' => $pages,
	    'defaultCallback' => "js:function(response, data) { 
	    	var url = response.options.path.join(response.options.state.currPage);

	    	// Try GA Tracking
	    	try {
			    _gaq.push(['_trackPageview', url]);
			} catch (e) {
				// Don't do anything if the tracking event failed
			}

			// Try Piwik Tracking
			try {
			    _paq.push(['trackPageView', url]);
			} catch (e) {
				// Don't do anything if the tracking event failed
			}			    
 		}"
	)); ?>
	<?php Yii::app()->clientScript->registerScript('unbind-infinite-scroll', "$(window).unbind('.infscr');"); ?>
<?php else: ?>
	<div class="alert alert-info">
		<strong>Woah!</strong> It looks like there isn't any posts in this category yet. Why don't you check out some of our other pages or check back later?
	</div>
<?php endif; ?>