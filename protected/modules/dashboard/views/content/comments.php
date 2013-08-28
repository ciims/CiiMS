<?php Yii::setPathOfAlias('bootstrap', Yii::getPathOfAlias('ext.bootstrap')); ?>
<div class="settings-row comment comment-<?php echo $comment->id; ?>" data-attr-id="<?php echo $comment->id; ?>" style="margin-left: <?php echo $depth*4 * 10; ?>px;">
	<div class="container-bundle">
		<div class="user-avatar pull-left">
			<?php echo CHtml::image($comment->author->gravatarImage(35), NULL, array('class' => 'rounded-img pull-left')); ?>
		</div>
		<div class="pull-left main-content">
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

	<div class="comment-body comment-byline comment-byline-footer pull-left">
		<?php if ($comment->approved != 1): ?>
			<span class="approve-<?php echo $comment->id; ?>"><?php echo Yii::t('Dashboard.main', 'approve'); ?></span> •
		<?php endif; ?>
		<span class="delete-<?php echo $comment->id; ?>"><?php echo Yii::t('Dashboard.main', 'delete'); ?></span> •
		<span class="reply-<?php echo $comment->id; ?>" data-attr-id="<?php echo $comment->id; ?>"><?php echo Yii::t('Dashboard.main', 'reply'); ?></span>
	</div>
	<div class="clearfix"></div>
	<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
		    'id'=>'comment-form',
		    'htmlOptions' => array('class' => 'comment-form comment-form-' . $comment->id)
		)); ?>
			<div id="sharebox-<?php echo $comment->id; ?>" class="comment-box">
                <div id="a-<?php echo $comment->id; ?>">
                    <div id="textbox-<?php echo $comment->id; ?>" contenteditable="true"></div>
                    <div id="close-<?php echo $comment->id; ?>"></div>
                    <div style="clearfix"></div>
                </div>
                <div id="b-<?php echo $comment->id; ?>" style="color:#999"><?php echo Yii::t('DefaultTheme', 'Comment on this post'); ?></div> 
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
		CiiDashboard.Content.futurePerspective.loadComment(<?php echo $comment->id; ?>);
	});
</script>