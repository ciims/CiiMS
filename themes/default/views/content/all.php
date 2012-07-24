<h2>Blogroll</h2>
<br />
	<? foreach($data as $content): ?>
		<? $meta = Content::model()->parseMeta($content->metadata); ?>
		<h3><? echo CHtml::link($content->title, Yii::app()->createUrl($content->slug)); ?></h3>
		<div class="blog-data">
			Posted <strong><? echo date('F jS, Y @ H:i', strtotime($content->created)); ?></strong>
			by <strong><? echo $content->author->displayName; ?></strong>
			in <? echo CHtml::link($content->category->name, Yii::app()->createUrl($content->category->slug)); ?>
			<span class="label label-info"><? echo $content->comment_count; ?> Comments</span> </div>
		<? if ($this->displayVar($meta['blog-image']['value'])): ?>
			<br />
			<p style="text-align:center;"><? echo CHtml::image(Yii::app()->baseUrl . $meta['blog-image']['value'], NULL, array('class'=>'image')); ?></p>
		<? endif; ?>
		<? $md = new CMarkdownParser; echo strip_tags($md->safeTransform($content->extract), '<h1><h2><h3><h4><h5><6h><p><b><strong><i>'); ?>
		<? echo CHtml::link('Read More', Yii::app()->createUrl($content->slug), array('class'=>'btn btn-inverse', 'style'=>'float:right;')); ?>
		<div style="clear:both;"><br /></div>
	<? endforeach; ?>
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