<div class="login-container">
	<div class="sidebar">
		<div class="well-span">
			<h4>Activate your Account</h4>
			<?php if(Yii::app()->user->hasFlash('activation-error')):?>
				<div class="alert alert-error" style="margin-top: 20px;">
				  	<strong>Oops!</strong> <?php echo Yii::app()->user->getFlash('activation-error'); ?>
				</div>
			<?php endif; ?>
			
			<?php if(Yii::app()->user->hasFlash('activation-success')):?>
				<div class="alert alert-success" style="margin-top: 20px;">
					  	<strong>All Right!</strong> <?php echo Yii::app()->user->getFlash('activation-success'); ?>
					</div>
			<?php endif; ?>
		</div>
	</div>
</div>