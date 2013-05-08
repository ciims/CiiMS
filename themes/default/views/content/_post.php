<?php $meta = Content::model()->parseMeta($content->metadata); ?>
<div class="post">
	<?php if (Cii::get(Cii::get($meta, 'blog-image', array()), 'value', '') != ""): ?>
		<p style="text-align:center;"><?php echo CHtml::image(Yii::app()->baseUrl . $meta['blog-image']['value'], NULL, array('class'=>'image')); ?></p>
	<?php endif; ?>
	<div class="post-inner">
		<div class="post-header">
			<h3><?php echo CHtml::link($content->title, Yii::app()->createUrl($content->slug)); ?></h3>
			<?php $md = new CMarkdownParser; echo strip_tags($md->safeTransform($content->extract), '<h1><h2><h3><h4><h5><6h><p><b><strong><i>'); ?>
		</div>
		<div class="blog-meta">
			<span class="date"><?php echo $content->getCreatedFormatted() ?></span>
			<span class="separator">⋅</span>
			<span class="blog-author minor-meta"><strong>by </strong>
				<span>
					<?php echo CHtml::link(CHtml::encode($content->author->displayName), $this->createUrl("/profile/{$content->author->id}/")); ?>
				</span>
				<span class="separator">⋅</span> 
			</span> 
			<span class="minor-meta-wrap">
				<span class="blog-categories minor-meta"><strong>in </strong>
				<span>
					<?php echo CHtml::link(CHtml::encode($content->category->name), Yii::app()->createUrl($content->category->slug)); ?>
				</span> 
				<span class="separator">⋅</span> 
			</span> 					
			<span class="comment-container">
				<?php echo $content->getCommentCount(); ?> Comments</a>					
			</span>
		</div>
		<a class="read-more-icon" href="<?php echo $this->createUrl('/' . $content->slug); ?>" rel="bookmark">
			<strong style="width: 93px;">Read more</strong>
			<span></span>
		</a>
	</div>
    <div style="clear:both;"><br /></div>
</div>