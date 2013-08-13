<?php echo $content->author->displayName; ?>,<br /><br />
<p>
	<?php echo Yii::t('main', 'This is a notification informing you a new comment has been add on your post: {{title}}.', array(
		'{{title}}' => $content->title
	)); ?>
</p>
<p>
	<?php echo Yii::t('main', '{{from}} {{user}}', array(
		'{{from}}' => CHtml::tag('strong', array(), Yii::t('main', 'From:')),
		'{{user}}' => $comment->author->displayName
	)); ?><br />
	<?php echo $comment->comment; ?>
</p>
