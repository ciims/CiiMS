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
			<?php $this->renderPartial('/comments/comment', array('comment'=>$comment)); ?>
		<?php endforeach; ?>
		<div class="right">
			<?php echo CHtml::link('More...', Yii::app()->createUrl('/admin/comments')); ?>
		</div>
	</div>
</div>
