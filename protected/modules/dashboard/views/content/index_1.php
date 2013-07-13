<div class="posts-container">
	<?php Yii::setPathOfAlias('bootstrap', Yii::getPathOfAlias('ext.bootstrap')); ?>
	<?php Yii::import('application.modules.dashboard.components.ContentListView.ContentListView'); ?>
	<?php $this->widget('ContentListView', array(
	    'dataProvider' => $model->search(),
	    'preview' => isset($preview) ? $preview : NULL,
	    'summaryText' => false,
	    'itemView' => 'post',
	    'sorterHeader' => '<div class="content"><strong>Manage Content</strong>',
	    'itemsCssClass' => 'posts nano',
	    'pagerCssClass' => 'pagination',
	    'pager' => array('class'=>'bootstrap.widgets.TbPager'),
	    'sorterCssClass' => 'sorter',
	    'beforeAjaxUpdate' => 'js:function() {
	    	previewPane = $("#preview .content");
	    	scrollTop = $("#preview .content").scrollTop();
	    }',
	    'afterAjaxUpdate' => 'js:function() { 
	    	// Change perspective
			$("#perspective").click(function(e) {
				console.log("BindClick");
				$.post("' . $this->createUrl('/dashboard/content/index/perspective/2') . '", function() { 
					window.location = "' .  $this->createUrl('/dashboard/content') .'";
				});
			});

			// NanoScroller for main div
	    	$("#posts.nano").nanoScroller({ iOSNativeScrolling: true }); 

	    	// Timeago
	    	$(".timeago").timeago(); 

	    	// Post Click Behavior
	    	bindPostClick(); 

	    	// Reset Preview Pane
	    	$(".preview").remove();
			$(".posts").after("<div class=\"preview nano\" id=\"preview\"></div>");
			$(".preview").html(contentPane).removeClass("has-scrollbar");
			$("#preview.nano").nanoScroller({ OSNativeScrolling: true}); 
			$("#preview .content").animate({
				scrollTop : scrollTop
			}, 0);
		}',
	    'sortableAttributes' => array(
	        'title',
	        'author_id',
	        'like_count',
	        'comment_count',
	        'category_id',
	        'status',
	        'created',
	        'updated',
	    )    
	));
	?>
	<div class="clearfix"></div>
</div>
<?php $asset=Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('application.modules.dashboard.assets'), true, -1, YII_DEBUG); ?>
<?php Yii::app()->getClientScript()->registerScriptFile($asset.'/js/jquery.nanoscroller.min.js', CClientScript::POS_END); ?>
<?php Yii::app()->getClientScript()->registerScript('nano-scroller', '
		$("#posts.nano").nanoScroller();
'); ?>

<?php Yii::app()->getClientScript()->registerScript('listview-perspective', '
	$(document).ready(function() {
		$("#perspective").click(function(e) {
			$.post("' . $this->createUrl('/dashboard/content/index/perspective/2') . '", function() { 
				window.location = "' .  $this->createUrl('/dashboard/content') .'";
			});
		});
	});
'); ?>

<?php Yii::app()->getClientScript()->registerScript('clickBinding', '
	function bindPostClick() {
		$(".post").click(function() { 
			if ($(this).hasClass("post-header"))
				return;
			
			$(".post").removeClass("active");
			$(this).addClass("active"); 
			var id = $(this).attr("data-attr-id");

			$.get("' . $this->createUrl('/dashboard/content/index/id/') . '/" + id, function(data, textStatus, jqXHR) {
				contentPane = $($.parseHTML(data)).find(".preview").html();
				$(".preview").remove();
				$(".posts").after("<div class=\"preview nano\" id=\"preview\"></div>");
				$(".preview").html(contentPane).removeClass("has-scrollbar");
				$("#preview.nano").nanoScroller({ OSNativeScrolling: true});
			});
		});
	}

	$(document).ready(function() {
		bindPostClick();
	});
'); ?>
<?php $this->widget('ext.timeago.JTimeAgo', array(
    'selector' => '.timeago',
)); ?>