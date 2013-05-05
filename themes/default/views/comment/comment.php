<div class="comment comment-<?php echo $comment->id; ?>" data-attr-id="<?php echo $comment->id; ?>" style="margin-left: <?php echo $depth*4 * 10; ?>px; display:none;">
	<?php echo CHtml::image($comment->author->gravatarImage(30), NULL, array('class' => 'rounded-image avatar')); ?>
	<div class="<?php echo $comment->author->id == $comment->content->author->id ? 'green-indicator author-indicator' : NULL; ?>">
		<div class="comment-body comment-byline">
			<?php echo CHtml::encode($comment->author->name); ?>
			<?php if ($comment->parent_id != 0): ?>
				<span class="icon-share-alt"></span> <?php echo CHtml::encode($comment->parent->author->name); ?> •
			<?php else: ?>
			 •
			<?php endif; ?>
			<time class="timeago" datetime="<?php echo date(DATE_ISO8601, strtotime($comment->created)); ?>">
				<?php echo Cii::formatDate($comment->created); ?>
			</time>
		</div>
		<div class="comment-body">
		    <?php if ($comment->approved == -2): ?>
		        <em class="flagged">Comment has been redacted</em>
		    <?php else: ?>
			    <?php echo $md->safeTransform($comment->comment); ?>
			<?php endif; ?>
		</div>
		<div class="comment-body comment-byline comment-byline-footer">
			<?php if (!Yii::app()->user->isGuest && $comment->approved != -2 && $comment->created != "now"): ?>
			    <?php if ($comment->content->commentable): ?>
				    <span class="reply">reply</span>
				<?php endif; ?>
				 • <span class="flag <?php echo $comment->approved == -1 ? 'flagged' : NULL; ?>" data-attr-id="<?php echo $comment->id; ?>"><?php echo $comment->approved == -1 ? 'flagged' : 'flag'; ?></span>
			<?php endif; ?>
		</div>
	</div>
		<?php $model = new Comments(); ?>
		<?php $comment->parent_id = $comment->parent_id; ?>
		<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		    'id'=>'comment-form',
		    'htmlOptions' => array('class' => 'comment-form')
		)); ?>
			<div id="sharebox-<?php echo $comment->id; ?>" class="comment-box">
                <div id="a-<?php echo $comment->id; ?>">
                    <div id="textbox-<?php echo $comment->id; ?>" contenteditable="true"></div>
                    <div id="close-<?php echo $comment->id; ?>"></div>
                    <div style="clearfix"></div>
                </div>
                <div id="b-<?php echo $comment->id; ?>" style="color:#999">Comment on this post</div> 
            </div>
			<?php $this->widget('bootstrap.widgets.TbButton', array(
                'type' => 'success',
                'label' => 'Submit',
                'url' => '#',
                'htmlOptions' => array(
                    'id' => 'submit-comment-' . $comment->id,
                    'class' => 'sharebox-submit',
            ))); ?>
		<?php $this->endWidget(); ?>
	<div class="clearfix"></div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		// Timeago
		$(".timeago").timeago();

		// Comment Form
		$("#b-<?php echo $comment->id; ?>").click( function () {
	        $(this).html("");
	        $("#a-<?php echo $comment->id; ?>").slideDown("fast");
	        $("#submit-comment-<?php echo $comment->id; ?>").show();
	        setTimeout(function() {
	            $("#textbox-<?php echo $comment->id; ?>").focus();
	        }, 100);
	    });
	    $("#textbox-<?php echo $comment->id; ?>").keydown( function() {
	        if($(this).text() != "")
	            $("#submit-comment-<?php echo $comment->id; ?>").css("background","#3b9000");
	        else
	            $("#submit-comment-<?php echo $comment->id; ?>").css("background","#9eca80");
	        });
	    $("#close-<?php echo $comment->id; ?>").click( function () {
	        $("#b-<?php echo $comment->id; ?>").html("Comment on this post");
	        $("#textbox-<?php echo $comment->id; ?>").html("");
	        $("#a-<?php echo $comment->id; ?>").slideUp("fast");
	        $("#submit-comment-<?php echo $comment->id; ?>").hide();
	    });

	    // Submit
	    $("#submit-comment-<?php echo $comment->id; ?>").click(function(e) {
	    	var elementId = $(this).attr('id').replace('submit-comment-', '');
        	e.preventDefault();
	        if ($("#textbox-<?php echo $comment->id; ?>").text() == "")
	            return;

	        $.post("/comment/comment", 
	        	{ 
	        		"Comments" : 
	        		{ 
	        			"comment" : $("#textbox-<?php echo $comment->id; ?>").text(), 
	        			"content_id" : $(".content").attr("data-attr-id"),
	        			"parent_id" : elementId
	        		}
	        	}, 
	        	function(data, textStatus, jqXHR) { 
	        		$("#textbox-<?php echo $comment->id; ?>").text("");  
	        		// PREPEND DATA
	        		var newElementId = jqXHR.getResponseHeader("X-Attribute-Id");
	        		$(".comment-" + elementId).append(data);
	        		$(".comment-" + newElementId).fadeIn();

	        		$("#close-<?php echo $comment->id; ?>").click();
	        		$(".comment-count").text((parseInt($(".comment-count").text().replace(" Comment", "").replace(" Comments", "")) + 1) + " Comments");
	        	}
	        );
	    });
	});
</script>