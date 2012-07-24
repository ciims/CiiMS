<div class="well">
	<h1>Activate your Account</h1>
	<br />
	<? if(Yii::app()->user->hasFlash('activation-error')):?>
	    <div><? echo Yii::app()->user->getFlash('activation-error'); ?></div>
	<? endif; ?>
	
	<? if(Yii::app()->user->hasFlash('activation-success')):?>
	    <div><? echo Yii::app()->user->getFlash('activation-success'); ?></div>
	<? endif; ?>
</div>