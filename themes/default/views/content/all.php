<h2>Blogroll</h2>
<br />
	<?php foreach($data as $content): ?>
		<?php $meta = Content::model()->parseMeta($content->metadata); ?>
		<h3><?php echo CHtml::link($content->title, Yii::app()->createUrl($content->slug)); ?></h3>
		<div class="blog-data">
			Posted <strong><?php echo date('F jS, Y @ H:i', strtotime($content->created)); ?></strong>
			by <strong><?php echo $content->author->displayName; ?></strong>
			in <?php echo CHtml::link($content->category->name, Yii::app()->createUrl($content->category->slug)); ?>
			<span class="label label-info"><?php echo $content->comment_count; ?> Comments</span> </div>
		<?php if (isset($meta['blog-image']['value'])): ?>
			<br />
			<p style="text-align:center;"><?php echo CHtml::image(Yii::app()->baseUrl . $meta['blog-image']['value'], NULL, array('class'=>'image')); ?></p>
		<?php endif; ?>
		<?php $md = new CMarkdownParser; echo strip_tags($md->safeTransform($content->extract), '<h1><h2><h3><h4><h5><6h><p><b><strong><i>'); ?>
		<?php echo CHtml::link('Read More', Yii::app()->createUrl($content->slug), array('class'=>'btn btn-inverse', 'style'=>'float:right;')); ?>
		<div style="clear:both;"><br /></div>
	<?php endforeach; ?>
    <?php 
		// Auto pagination
		if ($pages != array())
		{
			$this->widget('CLinkPager', array(
	            'currentPage'=>$pages->getCurrentPage(),
	            'itemCount'=>$itemCount,
	            'pageSize'=>$pages->pageSize,
	            'maxButtonCount'=>10,
	            'header'=>'',
	       		'htmlOptions'=>array('class'=>'pagination'),
	        ));
		}
	?>
