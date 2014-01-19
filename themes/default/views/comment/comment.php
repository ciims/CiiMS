<div class="comment comment-<?php echo $comment->id; ?>" data-attr-id="<?php echo $comment->id; ?>" style="margin-left: <?php echo $depth*4 * 10; ?>px; display:none;">
	<?php echo CHtml::image($comment->author->gravatarImage(30), NULL, array('class' => 'rounded-image avatar')); ?>
	<div class="<?php echo $comment->author->id == $comment->content->author->id ? 'green-indicator author-indicator' : NULL; ?>">
		<div class="comment-body comment-byline">
			<?php echo CHtml::encode($comment->author->name); ?>
			<time class="timeago" datetime="<?php echo date(DATE_ISO8601, strtotime($comment->created)); ?>">
				<?php echo Cii::formatDate($comment->created); ?>
			</time>
		</div>
		<div class="comment-body">
		    <?php if ($comment->approved == -2): ?>
		        <em class="flagged"><?php echo Yii::t('DefaultTheme.main', 'Comment has been redacted'); ?></em>
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
		<?php $form = $this->beginWidget('cii.widgets.CiiActiveForm', array(
		    'id'=>'comment-form',
		    'registerPureCss'	=> false,
		    'htmlOptions' => array('class' => 'comment-form')
		)); ?>
			<div id="sharebox-<?php echo $comment->id; ?>" class="comment-box">
                <div id="a-<?php echo $comment->id; ?>">
                    <div id="textbox-<?php echo $comment->id; ?>" contenteditable="true"></div>
                    <div id="close-<?php echo $comment->id; ?>"></div>
                    <div style="clearfix"></div>
                </div>
                <div id="b-<?php echo $comment->id; ?>" ><?php echo Yii::t('DefaultTheme.main', 'Comment on this post'); ?></div> 
            </div>
            <div class="clearfix"></div>
            <a id="submit-comment-<?php echo $comment->id; ?>" class="sharebox-submit pure-button pure-button-primary pure-button-small pull-right" style="margin-bottom: 5px;" href="#">
            	<i class="icon-spin icon-spinner" style="display:none;"></i>
            	<?php echo Yii::t('DefaultTheme.main', 'Submit'); ?>
            </a>
		<?php $this->endWidget(); ?>
	<div class="clearfix"></div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		Theme.loadComment(<?php echo $comment->id; ?>);
	});
</script>