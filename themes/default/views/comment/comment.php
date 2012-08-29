<div id="comment-<?php echo $comment->id; ?>">
	<?php echo CHtml::image('https://gravatar.com/avatar/' . md5(Users::model()->findByPk($comment->user_id)->email), NULL, array('class'=>'avatar')); ?>
	<div class="comment-block <?php echo Yii::app()->user->id == $comment->author->id ? 'alert-success author' : ''; ?>">
		<p class="comment-post red-block">
			By: <?php echo Users::model()->findByPk($comment->user_id)->displayName; ?>
			on <?php echo date('F jS, Y @ H:i'); ?>
		</p>
		<p class="<?php echo $count%2 == 0 ? 'green' : 'blue'; ?>-block comment-post" style="margin-top: 7px"><?php echo $comment->comment; ?></p>
		<div style="float:right; margin-top: 5px;">
			<?php if (isset(Yii::app()->user->role) && Yii::app()->user->role == 5): ?>
				<?php echo CHtml::link('Delete', NULL, array('id'=>'delete', 'value'=>$comment->id, 'class'=>'label label-inverse', 'style'=>'margin-left: 5px;')); ?>
			<?php endif; ?>
			
			
			<?php if (!Yii::app()->user->isGuest): ?>
				<?php echo CHtml::link('Flag', NULL, array('id'=>'flag', 'value'=>$comment->id, 'class'=>'label label-important', 'style'=>'margin-left: 5px;')); ?>		
				<?php echo CHtml::link('Reply', '#reply', array('class'=>'label label-info', 'style'=>'margin-left: 5px;')); ?>
			<?php endif; ?>
		</div>
	</div>
</div>
<br />
<div id="new-comment" style="display:none;"></div>
<script>
	$(document).ready(function() {
		$("div[id*=comment-]").css("margin", "5px");
	});
</script>