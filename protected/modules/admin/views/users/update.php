<div class="row-fluid">	
	<div class="well span7 left">
		<h3 class="top">Update User</h3><br />
		<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>
	</div>
	<div class="well span5 right">
		<h2 class="top">Associated Data</h2><br />
		<?php echo $this->renderPartial('_meta', array('model'=>$model)); ?>
	</div>
</div>