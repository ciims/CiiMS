<?php $this->beginWidget('bootstrap.widgets.BootModal', array('id'=>'previewModal')); ?>
	<div class="modal-header">
	    <a class="close" data-dismiss="modal">&times;</a>
	    <h3><?php echo $data['Content']['title']; ?></h3>
	</div>
	 
	<div class="modal-body">
	    <h4 class="top">Extract</h4>
	    <?php echo $md->safeTransform($data['Content']['extract']); ?>
	    <hr />
	    
	     <h4 class="top">Body</h4>
	    <?php echo $md->safeTransform($data['Content']['content']); ?>
	</div>
	 
	<div class="modal-footer">
	    <?php $this->widget('bootstrap.widgets.BootButton', array(
	        'label'=>'Close',
	        'url'=>'#',
	        'htmlOptions'=>array('data-dismiss'=>'modal'),
	    )); ?>
	</div>
<?php $this->endWidget(); ?>
