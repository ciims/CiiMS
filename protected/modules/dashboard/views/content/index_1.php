<div class="posts-container">
	<?php Yii::import('application.modules.dashboard.components.ContentListView.ContentListView'); ?>
	<?php $this->widget('ContentListView', array(
	    'dataProvider' => $model->search(),
	    'preview' => isset($preview) ? $preview : NULL,
	    'summaryText' => false,
	    'itemView' => 'post',
	    'sorterHeader' => '<div class="content"><strong>Manage Content</strong>',
	    'itemsCssClass' => 'posts nano',
	    'sorterCssClass' => 'sorter',
	    'beforeAjaxUpdate' => 'js:function() { 
	    	previewPane = $("#preview .content");
	    	scrollTop = $("#preview .content").scrollTop();
	    }',
	    'afterAjaxUpdate' => 'js:function() { 
	    	$("#posts.nano").nanoScroller({ iOSNativeScrolling: true }); 
	    	$(".timeago").timeago(); 
	    	bindPostClick(); 

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
	$(document).ready(function() {
		$("#posts.nano").nanoScroller();
	});
'); ?>

<?php Yii::app()->getClientScript()->registerScript('listview-settings', '
	$(document).ready(function() {
		$(".icon-gears").click(function(e) {
			e.preventDefault();
			return false;
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