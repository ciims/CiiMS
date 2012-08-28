<div class="row-fluid">
	<?php $form=$this->beginWidget('bootstrap.widgets.BootActiveForm',array(
		'id'=>'content-form',
		'enableAjaxValidation'=>false,
		'htmlOptions' => array('enctype' => 'multipart/form-data'),
		'enableClientValidation'=>true,
        'clientOptions'=>array(
            'validateOnSubmit'=>true,
            ),
	)); ?>
	<div class="well span8 left">
		<h2 class="top">Main Content</h2>
		<?php echo $this->renderPartial('_form',array('model'=>$model, 'form'=>$form)); ?>
	</div>
	<?php if (!$model->isNewRecord): ?>
		<div class="well span4 right">
			<h2 class="top">Associated Data</h2>
			<?php echo $this->renderPartial('_files', array('model'=>$model)); ?>
		</div>
	<?php endif; ?>
	<div class="well span4 right" style="float:right;">
		<h2 class="top">Additional Details</h2>
		<?php echo $this->renderPartial('_details',array('model'=>$model, 'form'=>$form)); ?>
	</div>
	
	<div style="clear:both;"></div>
	<div class="form-actions">
		<?php $this->widget('bootstrap.widgets.BootButton', array(
			'type'=>'primary',
			'buttonType'=>'submit',
			'label'=>$model->isNewRecord ? 'Create' : 'Save',
		)); ?>
		<?php $this->widget('bootstrap.widgets.BootButton', array(
			'type'=>'danger',
			'label'=>'Change Extract',
			'htmlOptions'=>array(
				'id'=>'extractButton',
				'rel'=>'tooltip',
				'title'=>'Click to manually edit the content extract'
			)
		)); ?>
		<?php $this->widget('bootstrap.widgets.BootButton', array(
			'type'=>'success',
			'label'=>'Preview',
			'url'=>'#previewModal',
			'htmlOptions'=>array(
				'id'=>'previewButton',
				'rel'=>'tooltip',
				'title'=>'Preview how your post will look'
			)
		)); ?>
	</div>
	<?php $this->endWidget(); ?>
</div>

<div id="previewPost"></div>
<?php Yii::app()->clientScript->registerScript('extract', '
	$("#extractButton").click(function() {
		$("#extractForm").slideToggle();
	});
	
	$("#previewButton").click(function() {
		$.ajax({
			type: "POST",
			url: "'.Yii::app()->createAbsoluteUrl('/admin/content/preview').'", 
			data: $("form").serialize(),
			success: function(data) {
				$("#previewPost").html(data);
				$("#previewModal").modal();
			}
		});
	});
'); ?>
