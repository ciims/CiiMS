<?php $content = &$data; ?>
<?php $meta = Content::model()->parseMeta($content->metadata); ?>

<div class="content">
	<div class="post">
		<?php if (Cii::get(Cii::get($meta, 'blog-image', array()), 'value', '') != ""): ?>
			<p style="text-align:center;"><?php echo CHtml::image(Yii::app()->baseUrl . $meta['blog-image']['value'], NULL, array('class'=>'image')); ?></p>
		<?php endif; ?>
		<div class="post-inner">
			<div class="post-header">
				<h3><?php echo CHtml::link($content->title, Yii::app()->createUrl($content->slug)); ?></h3>
			</div>
			<div class="blog-meta inline">
				<span class="date"><?php echo $content->getCreatedFormatted() ?></span>
				<span class="separator">⋅</span>
				<span class="blog-author minor-meta"><strong>by </strong>
					<span>
						<?php echo $content->author->displayName; ?>
					</span>
					<span class="separator">⋅</span> 
				</span> 
				<span class="minor-meta-wrap">
					<span class="blog-categories minor-meta"><strong>in </strong>
					<span>
						<?php echo CHtml::link($content->category->name, Yii::app()->createUrl($content->category->slug)); ?>
					</span> 
					<span class="separator">⋅</span> 
				</span> 					
				<span class="comment-container">
					<?php echo $content->comment_count; ?> Comments</a>					
				</span>
			</div>
			<div class="clearfix"></div>
				<?php $md = new CMarkdownParser; echo $md->safeTransform($content->content); ?>
		</div>
	    <div style="clear:both;"><br /></div>
	</div>
</div>

<div class="comments">
	<?php if ($data->commentable): $count = 0;?>
		
		<?php echo CHtml::link(NULL, NULL, array('name'=>'comments')); ?>
		<div class="post">
			<div class="post-inner">
				<div class="post-header post-header-comments">
					<h3 class="pull-left left-header"><?php echo Yii::t('comments', 'n==0#No Comments|n==1#{n} Comment|n>1#{n} Comments', $comments); ?></h3>
					
					<div class="likes-container pull-right">
						<div class="likes">     
						    <a href="#" data-action="upvote" title="Like this discussion">
						    	<span class="icon-heart icon-red"></span>
						        <span class="counter">
						            <span data-role="like-count"><?php echo $content->like_count; ?></span>
						        </span>      
						    </a>
						</div>
					</div>
				</div>
				<div class="clearfix"></div>
				<a id="comment-box"></a>
                <div id="sharebox" class="comment-box">
                    <div id="a">
                        <div id="textbox" contenteditable="true"></div>
                        <div id="close"></div>
                        <div style="clear:both"></div>
                    </div>
                    <div id="b" style="color:#999">Comment on this post</div> 
                </div>
                <?php $this->widget('bootstrap.widgets.TbButton', array(
                    'type' => 'success',
                    'label' => 'Submit',
                    'url' => '#',
                    'htmlOptions' => array(
                        'id' => 'submit',
                        'class' => 'sharebox-submit',
                        'style' => 'display:none'
                    )
                )); ?>
                <div id="comment-container"></div>
				<div id="new-comment" style="display:none;"></div>

                <div class="clearfix"></div>
			</div>
		</div>
		<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/' . Yii::app()->theme->name .'/commentform.js'); ?>
	<?php endif; ?>
</div>


<?php Yii::app()->clientScript->registerScript('comment-box', '
    $("#b").click( function () {
        $(this).html("");
        $("#a").slideDown("fast");
        $("#submit").show();
        setTimeout(function() {
            $("#textbox").focus();
        }, 100);
    });
    $("#textbox").keydown( function() {
        if($(this).text() != "")
            $("#submit").css("background","#3b9000");
        else
            $("#submit").css("background","#9eca80");
        });
    $("#close").click( function () {
        $("#b").html("Comment on this post");
        $("#textbox").html("");
        $("#a").slideUp("fast");
        $("#submit").hide();
    });
    
    $("#submit").click(function(e) {
        e.preventDefault();
        if ($("#textbox").text() == "")
            return;
    });
'); ?>