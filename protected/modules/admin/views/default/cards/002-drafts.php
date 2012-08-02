<?php
	// Posts Criteria
	$postsCriteria = new CDbCriteria;
	$postsCriteria->addCondition("vid=(SELECT MAX(vid) FROM content WHERE id=t.id)");
	$postsCriteria->addCondition('status=0');
	?>

<div class="well span6 card">
	<h4 class="nav-header top">Drafts</h4>
	<br />
	<?php foreach(Content::model()->findAll($postsCriteria) as $draft): ?>
		<div class="span10">
			<h5><?php echo CHtml::link($draft->title, Yii::app()->createUrl('/admin/content/save/id/'. $draft->id)); ?> by <?php echo $draft->author->displayName; ?> on <?php echo CTimestamp::formatDate("M d, y @ H:i", strtotime($draft->updated)); ?></h5>
			<?php echo $draft->content; ?>
		</div>
		<br />
	<?php endforeach; ?>
</div>
