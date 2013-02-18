<?php Yii::import('ext.redactor.*'); ?>

<div class="row-fluid">
    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id'=>'horizontalForm',
        'type'=>'horizontal',
    )); ?>
	    <div class="span8">
	    	<?php echo $form->hiddenField($model, 'id'); ?>
	    	<?php echo $form->hiddenField($model, 'vid'); ?>
	    	<?php echo $form->hiddenField($model,'parent_id',array('value'=>1)); ?>
			<?php echo $form->hiddenField($model,'author_id',array('value'=>Yii::app()->user->id,)); ?>
	    	<?php echo $form->textFieldRow($model, 'title', array('placeholder' => 'Title', 'style' => 'width: 98%')); ?>
	        <?php if ($preferMarkdown): ?>
	            <?php echo $form->markdownEditorRow($model, 'content', array('height'=>'200px'));?>
	        <?php else: ?>
	            <?php $this->widget('ImperaviRedactorWidget', array(
	                    'model' => $model,
	                    'attribute' => 'content',
	                    'options' => array(
	                        'focus' => true,
	                        'autoresize' => false,
	                        'autosave' => $this->createUrl('/admin/content/save/' . $model->id),
	                        'interval' => 120,
	                        'autosaveCallback' => 'saveCallback',
	                    )
	                ));
	            ?>
	        <?php endif; ?>
	        
	        <?php echo $form->textAreaRow($model, 'extract', array('style' => 'width: 98%; height: 100px')); ?>
	    </div>
	    <div class="span4 sidebarNav">
	    	<?php if ($model->vid >= 1): ?>
		        <div class="well">
		            <h5><i class="icon-upload"></i> Uploads <?php $this->widget('ext.EAjaxUpload.EAjaxUpload',
								array(
								        'id'=>'uploadFile',
								        'config'=>array(
								           'debug'=>false,
							               'action'=>Yii::app()->createUrl('/admin/content/upload/id/'. $model->id),
							               'allowedExtensions'=>array('jpg', 'jpeg', 'png', 'gif', 'bmp'),
							               'sizeLimit'=>10*1024*1024,// maximum file size in bytes
							               'minSizeLimit'=>1,
							               'template'=>'<div id="uploadFile"><ul class="qq-upload-list" style="display:none;">
							               </ul><div class="qq-upload-drop-area" style="display:none;"></div><a class="btn btn-small btn-primary qq-upload-button right">Upload</a>
							               </div>',	
							               'onComplete' => "js:function(id, fileName, response) {
							               		if (response.success)
							               		{
							               			$('#new-attachment').before('<span class=\"thumb-container thumb-center\"><span class=\"thumb-inner\"><span class=\"thumb-img\"><img class=\"thumb\" href=\"'+ response.filepath +'\" src=\"'+ response.filepath +'\" style=\"left: 0px; top: 0px;\"></span><span class=\"thumb-strip\"></span><span class=\"thumb-icon\"></span></span></span>').after('<li id=\"new-attachment\" style=\"display:none;\">');
							               			$('.thumb').thumbs();
													$('.thumb').colorbox({rel:'thumb'});
													$('#new-attachment-img').show().attr('id', 'thumb');
							               		}
										   }"
								        )
								)); ?></h5>
					   
						<div class="image-holder ">
							<?php foreach ($attachments as $attachment): ?>
							    <div class="image-ctrl" id="<?php echo $attachment->key; ?>">
    								<?php echo CHtml::image($attachment->value, NULL, array('class'=> 'thumb', 'href' => $attachment->value, 'title' => $attachment->value)); ?>
                                    <span class="delete-button icon icon-remove" id="<?php echo $attachment->key; ?>"></span>
                                </div>
							<?php endforeach; ?>
							<li id="new-attachment" style="display:none;"></li>
						</div>
					<div class="clearfix"></div>
		        </div>
		        
		        <div class="well">
		            <h5><i class="icon-tags"></i> Tags</h5>
		            <?php echo $form->textField($model, 'tagsFlat', array('id' => 'tags')); ?>
		        </div>
	        <?php endif; ?>
	        <div class="well">
	            <h5><i class="icon-align-justify"></i> Details</h5>
	            <?php echo $form->dropDownListRow($model,'status', array(1=>'Published', 0=>'Draft'), array('class'=>'span12')); ?>
				<?php echo $form->dropDownListRow($model,'commentable', array(1=>'Yes', 0=>'No'), array('class'=>'span12')); ?>
				<?php echo $form->dropDownListRow($model,'category_id', CHtml::listData(Categories::model()->findAll(), 'id', 'name'), array('class'=>'span12')); ?>
				<?php echo $form->dropDownListRow($model,'type_id', array(2=>'Blog Post', 1=>'Page'),array('class'=>'span12')); ?>
				<hr />
				<?php echo $form->textField($model,'password',array('class'=>'span12','maxlength'=>150, 'placeholder' => 'Password (Optional)')); ?>
				<?php echo $form->textField($model,'slug',array('class'=>'span12','maxlength'=>150, 'placeholder' => 'Slug')); ?>
	        </div>
	        <?php $this->widget('bootstrap.widgets.TbButtonGroup', array(
		        'htmlOptions' => array(
					'class' => 'pull-right'
				),
			    'buttons'=>array(
				    array('label'=>'Save', 'buttonType' => 'submit', 'type' => 'primary')
			    ),
			)); ?>
	    </div>
    <?php $this->endWidget(); ?>
</div>



<?php Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . '/css/admin/jquery.tags.css'); ?>
<?php Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . '/css/admin/colorbox.css'); ?>
<?php Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . '/css/jquery.thumbs.css'); ?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/admin/jquery.tags.min.js'); ?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.thumbs.min.js'); ?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.colorbox.min.js'); ?>
<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/jquery.gridster.js'); ?>
<?php Yii::app()->clientScript->registerScript('admin_promoted_image', 'setTimeout(function() { $("img.thumb").css("left", 0).css("right", 0).css("top", 0); $("#blog-image").find(".thumb-container").addClass("transition"); }, 500);'); ?>
<?php Yii::app()->clientScript->registerScript('admin_tags', '
$("#tags").tagsInput({
		defaultText : "Add a Tag",
	    width: "99%",
	    height : "40px",
	    onRemoveTag : function(e) {
	    	$.post("../../removeTag", { id : ' . $model->id . ', keyword : e });
	    },
	    onAddTag : function(e) {
	    	$.post("../../addTag", { id : ' . $model->id . ', keyword : e });
	    }
	});
'); ?>
<?php Yii::app()->clientScript->registerScript('admin_thumbs', '$(".thumb").thumbs();'); ?>
<?php Yii::app()->clientScript->registerScript('admin_colorbox', '$(".thumb").colorbox({rel:"thumb"});'); ?>
<?php Yii::app()->clientScript->registerScript('admin_promote', 'var timeoutId = 0; $(".image-ctrl").mousedown(function() {
        timeoutId = setTimeout(promote, 1000, ($(this).attr("id")));
    }).bind("mouseup mouseleave", function() {
        clearTimeout(timeoutId);
    });'); ?>
<?php Yii::app()->clientScript->registerScript('admin_delete', '$(".delete-button").click(function() {
    var element = $(this);
    $.post("../../removeImage", { id : ' . $model->id . ', key : $(this).attr("id") }, function () {
        element.parent().fadeOut();
    });
})'); ?>
<?php Yii::app()->clientScript->registerScript('admin_promote_action', 'function promote(id) {
    $.post("../../promoteImage", { id : ' . $model->id . ', key : id }, function() {
        $(".image-ctrl").find(".thumb-container").css("border-color", "").removeClass("transition");
        $("div[id*=\'" + id + "\']").find(".thumb-container").addClass("transition");
    });
}'); ?>