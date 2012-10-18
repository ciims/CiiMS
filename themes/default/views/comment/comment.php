<?php $color = Yii::app()->user->id == $comment->author->id ? 'green' : $comment->author->role->id == 5 ? 'red' : 'blue'; ?>
<div id="comment-<?php echo $comment->id; ?>">
	<?php echo CHtml::image('https://gravatar.com/avatar/' . md5(Users::model()->findByPk($comment->user_id)->email), NULL, array('class'=>'avatar')); ?>
	<div class="comment-block">
		<p class="comment-post">
			By: <?php echo Users::model()->findByPk($comment->user_id)->displayName; ?>
			on <?php echo date('F jS, Y @ H:i', strtotime($comment->created)); ?>
		</p>
		<blockquote class="<?php echo Yii::app()->user->id == $comment->content->author->id ? 'author-comment' : ''; ?>">
			 <?php echo $comment->comment; ?>
		</blockquote>
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