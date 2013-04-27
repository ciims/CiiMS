<?php if (isset($_GET['q'])): ?>
	<?php if ($itemCount == 0): ?>
		<div class="post">
			<div class="post-inner">
				<div class="post-header">
					<h2 style="text-align: center;">No Results Found</h2>
				</div>
			
			<p style="text-align:center;">Sorry, we tried looking but we didn't find a match for the specified criteria. Try refining your search.</p>
			</div>
		</div>
	<?php endif; ?>
<?php endif; ?>

<div id="posts">
    <?php foreach ($data as $k=>$v): ?>
        <?php $this->renderPartial('//content/_post', array('content' => $v)); ?>
    <?php endforeach; ?>
</div>

<?php if ($itemCount != 0): ?>
	<?php $this->widget('ext.yiinfinite-scroll.YiinfiniteScroller', array(
	    'url'=>isset($url) ? $url : 'blog',
	    'contentSelector' => '#posts',
	    'pages' => $pages,
	    'param'=>array(
		    'getParam'=>'q',
		    'param'     => Cii::get($_GET, 'q', '')
		),
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

<?php endif; ?>
<META NAME="robots" CONTENT="noindex,nofollow">
