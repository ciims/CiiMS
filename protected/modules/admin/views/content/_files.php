<?php $meta = Content::model()->parseMeta($model->metadata); ?>
<ul>
	<?php foreach ($meta as $k=>$v): ?>
		<li><strong><?php echo $k; ?></strong>: <?php echo $v['value']; ?></li>
	<?php endforeach; ?>
</ul>

<?php echo CHtml::textField('titlehdr', 'blog-image', array('placeholder'=>'Title', 'style'=>'float:left;', 'rel'=>'tooltip', 'class'=>'left span4', 'title'=>'How you want Cii to reference this file as')); ?>
<?php $this->widget('ext.EAjaxUpload.EAjaxUpload',
	array(
	        'id'=>'uploadFile',
	        'config'=>array(
	               'action'=>Yii::app()->createUrl('/admin/content/upload/'),
	               'allowedExtensions'=>array('jpg', 'jpeg', 'gif', 'png', 'bmp'),//array("jpg","jpeg","gif","exe","mov" and etc...
	               'sizeLimit'=>10*1024*1024,// maximum file size in bytes
	               'minSizeLimit'=>1,// minimum file size in bytes
	               //'onComplete'=>"js:function(id, fileName, responseJSON){ alert(fileName); }",
	               //'messages'=>array(
	               //                  'typeError'=>"{file} has invalid extension. Only {extensions} are allowed.",
	               //                  'sizeError'=>"{file} is too large, maximum file size is {sizeLimit}.",
	               //                  'minSizeError'=>"{file} is too small, minimum file size is {minSizeLimit}.",
	               //                  'emptyError'=>"{file} is empty, please select files again without it.",
	               //                  'onLeave'=>"The files are being uploaded, if you leave now the upload will be cancelled."
	               //                 ),
	               //'showMessage'=>"js:function(message){ alert(message); }"
	              )
	)); ?>
