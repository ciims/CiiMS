<?php if (Cii::get($meta, 'blog-image', "") != ""): ?>
	<p style="text-align:center;"><?php echo CHtml::image(Yii::app()->baseUrl . $meta['blog-image'], NULL, array('class'=>'image')); ?></p>
<?php endif; ?>