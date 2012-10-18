<?php if ($comment === NULL) { $comment = $data; } ?>
<div class="comment-block">
	<div class="left" stye="width:40px;">
		<?php echo CHtml::image($comment->author->gravatarImage(40)); ?>
	</div>
	<span class="small-text left">
		Submitted by <?php echo CHtml::link($comment->author->displayName, Yii::app()->createUrl('/admin/users/update/id/'.$comment->author->id)); ?> <?php echo date('F jS, Y @ H:i', strtotime($comment->created)); ?> 
		in <?php echo CHtml::link($comment->content->title, Yii::app()->createUrl('/'.$comment->content->slug)); ?> 
		<em><?php echo CHtml::link('(permalink)', Yii::app()->createUrl('/'.$comment->content->slug.'#comment-'.$comment->id)); ?></em>
	</span>
	
	<div class="right">
		<?php echo CHtml::ajaxLink('<i class="icon-white icon-ok"></i>', Yii::app()->createUrl('/admin/comments/approve/id/'.$comment->id), array('success'=>'$(this).parent().parent().fadeOut()'), array('class'=>'btn btn-primary', 'title'=>'Approve')); ?>
		<?php echo CHtml::ajaxLink('<i class="icon-white icon-trash"></i>', Yii::app()->createUrl('/admin/comments/delete/id/'.$comment->id), array('success'=>'$(this).parent().parent().fadeOut()'), array('class'=>'btn btn-danger', 'title'=>'Delete')); ?>
	</div>
	<br />
	<blockquote class="comment">
		<?php echo $comment->comment; ?>
	</blockquote>
	
	<div style="clear:both;"></div>
	<hr style="margin: 5px 0px;"/>
</div>