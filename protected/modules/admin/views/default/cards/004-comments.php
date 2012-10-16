<?php
$criteria = new CDbCriteria();
$criteria->limit = 5;
$criteria->order = 'updated DESC';
$comments = Comments::model()->findAll($criteria);
?><div class="well span6 card">
	<h4 class="nav-header top">Recent Comments</h4>
	<br />
	<div class="left span11">
		<?php foreach($comments as $comment): ?>
			<h5>By: <?php echo $comment->author->displayName; ?> on <?php echo date('F jS, Y @ H:i', strtotime($comment->created)); ?></h5>
			<em>Commented on on <?php echo CHtml::link($comment->content->title, Yii::app()->createUrl($comment->content->slug)); ?></em>
			<p style="margin-left: 20px;"><?php echo $comment->comment; ?></p>
			<hr style="margin: 5px;"/>
		<?php endforeach; ?>
		<div class="right">
			<?php echo CHtml::link('More...', Yii::app()->createUrl('/admin/comments')); ?>
		</div>
	</div>
</div>
