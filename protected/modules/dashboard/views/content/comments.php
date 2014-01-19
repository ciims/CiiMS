<div class="settings-row comment comment-<?php echo $comment->id; ?>" data-attr-id="<?php echo $comment->id; ?>" style="margin-left: <?php echo $depth*4 * 10; ?>px;">
	<div class="container-bundle">
		<div class="user-avatar">
			<?php echo CHtml::image($comment->author->gravatarImage(35), NULL, array('class' => 'rounded-img pull-left')); ?>
		</div>
		<div class="main-comment">
			<div class="user-info pull-left">
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
			<div class="clearfix"></div>
			<div class="pull-left body">
				<?php echo $md->safeTransform($comment->comment); ?>
			</div>
		</div>
	</div>

	<div class="clearfix"></div>

	<div class="comment-body comment-byline comment-byline-footer pull-right">
		<span style="<?php echo $comment->approved != 1 ? NULL : 'display: none'; ?>" class="approve-<?php echo $comment->id; ?>"><?php echo Yii::t('Dashboard.main', 'approve'); ?></span>
		<span style="<?php echo $comment->approved != 1 ? 'display: none' : NULL; ?>" class="block-<?php echo $comment->id; ?>"><?php echo Yii::t('Dashboard.main', 'block'); ?></span>
		 •
		<span class="delete-<?php echo $comment->id; ?>"><?php echo Yii::t('Dashboard.main', 'delete'); ?></span>
	</div>
	<div class="clearfix"></div>
</div>


<script type="text/javascript">
	$(document).ready(function() {
		CiiDashboard.Content.Preview.Comments.loadComment(<?php echo $comment->id; ?>);
	});
</script>
