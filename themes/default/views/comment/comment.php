<div class="comment comment-<?php echo $comment->id; ?>" style="margin-left: <?php echo $depth*4 * 10; ?>px; display:none;">
	<?php echo CHtml::image($comment->author->gravatarImage(30), NULL, array('class' => 'rounded-image avatar')); ?>
	<div class="<?php echo $comment->author->id == $comment->content->author->id ? 'green-indicator author-indicator' : NULL; ?>">
		<div class="comment-body comment-byline">
			<?php echo $comment->author->name; ?>
			<?php if ($comment->parent_id != 0): ?>
				<span class="icon-share-alt"></span> <?php echo $comment->parent->author->name; ?> •
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
		    'htmlOptions' => array('style' => 'display:none; padding-left: 50px; margin-top: 10px; margin-bottom: 0px; padding-bottom: -10px;')
		)); ?>
			<?php echo $form->textField($model, 'comment', array('class'=>'span10')); ?>
			<?php $this->widget('bootstrap.widgets.TbButton', array(
                'type' => 'success',
                'label' => 'Submit',
                'url' => '#',
                'htmlOptions' => array(
                    'id' => 'submit',
                    'class' => 'sharebox-submit',
            ))); ?>
		<?php $this->endWidget(); ?>
	<div class="clearfix"></div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$(".timeago").timeago();
	});
</script>