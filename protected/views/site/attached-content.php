<?php if (Cii::get(Cii::get($meta, 'blog-image', array()), 'value', '') != ""): ?>
	<p style="text-align:center;"><?php echo CHtml::image(Yii::app()->baseUrl . $meta['blog-image']['value'], NULL, array('class'=>'image')); ?></p>
<?php endif; ?>