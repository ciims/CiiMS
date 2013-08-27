<?php Yii::setPathOfAlias('bootstrap', Yii::getPathOfAlias('ext.bootstrap')); ?>
<div class="comment comment-<?php echo $comment->id; ?>" data-attr-id="<?php echo $comment->id; ?>" style="margin-left: <?php echo $depth*4 * 10; ?>px;">
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
		        <em class="flagged"><?php echo Yii::t('DefaultTheme', 'Comment has been redacted'); ?></em>
		    <?php else: ?>
			    <?php echo $md->safeTransform($comment->comment); ?>
			<?php endif; ?>
		</div>
		<div class="comment-body comment-byline comment-byline-footer">
			<span class="approve">approve</span>
			<span class="delete">delete</span>
		</div>
	</div>
		<?php $model = new Comments(); ?>
		<?php $comment->parent_id = $comment->parent_id; ?>
	<div class="clearfix"></div>
</div>


<script type="text/javascript">
	$(document).ready(function() {
		CiiDashboard.Content.futurePerspective.loadComment(<?php echo $comment->id; ?>);
	});
</script>