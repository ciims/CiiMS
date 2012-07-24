<div class="well">
	<h1>Activate your Account</h1>
	<br />
	<?php if(Yii::app()->user->hasFlash('activation-error')):?>
	    <div><?php echo Yii::app()->user->getFlash('activation-error'); ?></div>
	<?php endif; ?>
	
	<?php if(Yii::app()->user->hasFlash('activation-success')):?>
	    <div><?php echo Yii::app()->user->getFlash('activation-success'); ?></div>
	<?php endif; ?>
</div>