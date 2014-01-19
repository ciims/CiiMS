<div class="post">
	<?php $this->renderPartial('//site/attached-content', array('meta' => Content::model()->parseMeta($content->metadata))); ?>
	<div class="post-inner">
		<div class="post-header">
			<h2><?php echo CHtml::link($content->title, Yii::app()->createUrl($content->slug)); ?></h2>
			<span class="author">
				<?php echo Yii::t('DefaultTheme.main', 'By:') . ' ' . CHtml::link(CHtml::encode($content->author->displayName), $this->createUrl("/profile/{$content->author->id}/")); ?> 
				<span class="pull-right">
					<?php echo CHtml::link(CHtml::encode($content->category->name), Yii::app()->createUrl($content->category->slug)); ?>
				</span>
			</span>
			<div class="extract">
				<?php echo strip_tags($md->safeTransform($content->extract), '<h1><h2><h3><h4><h5><6h><p><b><strong><i>'); ?>
			</div>
		</div>

		<div class="post-details">
			<?php echo CHtml::link(Yii::t('DefaultTheme.main', 'Read More'), $this->createUrl('/' . $content->slug), array('class' => 'read-more', 'rel' => 'bookmark')); ?>

			<div class="icons">
				<span class="comment-container">
					<?php if (Cii::getConfig('useDisqusComments')): ?>
						<?php echo CHtml::link(0, Yii::app()->createUrl($content->slug) . '#disqus_thread'); ?>
					<?php else: ?>
						<?php echo Chtml::link($content->getCommentCount(),Yii::app()->createUrl($content->slug) . '#comments'); ?>
					<?php endif; ?>				
				</span>
				<span class="likes-container">
					<?php echo $content->getLikeCount(); ?>
				</span>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
    <div style="clear:both;"><br /></div>
</div>
