<div class="comment comment-<?php echo $comment->id; ?>" style="margin-left: <?php echo $depth*4 * 10; ?>px">
	<?php echo CHtml::image($comment->author->gravatarImage(30), NULL, array('class' => 'rounded-image avatar')); ?>
	<div class="<?php echo $comment->author->id == $comment->content->author->id ? 'green-indicator author-indicator' : NULL; ?>">
		<div class="comment-body comment-byline">
			<?php echo $comment->author->name; ?>
			<?php if ($comment->parent_id != 0): ?>
				<span class="icon-share-alt"></span> <?php echo $comment->parent->author->name; ?> •
			<?php else: ?>
			 •
			<?php endif; ?>
			<time class="timeago" title="<?php echo date('c', strtotime($comment->created)); ?>">
				<?php echo Cii::formatDate($comment->created); ?>
			</time>
		</div>
		<div class="comment-body">
			<?php echo $md->safeTransform($comment->comment); ?>
		</div>
		<div class="comment-body comment-byline comment-byline-footer">
			<span class="reply">reply</span>
		</div>
	</div>
	<div class="clearfix"></div>
</div>