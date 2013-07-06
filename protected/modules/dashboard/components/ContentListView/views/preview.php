<?php if ($model !== null): ?>
	<?php $meta = Content::model()->parseMeta($model->metadata); ?>
	<div class="preview-header">
		<span class="title pull-left"><?php echo $model->title; ?></span>
		<?php echo CHtml::link(NULL, Yii::app()->createUrl('/dashboard/content/save/id/' . $model->id), array('class' => 'icon-gear pull-right')); ?>
		<?php echo CHtml::link(NULL, '#', array('class' => 'icon-edit pull-right')); ?>
		<div class="clearfix"></div>
	</div>
	<?php if (Cii::get(Cii::get($meta, 'blog-image', array()), 'value', '') != ""): ?>
		<p style="text-align:center;"><?php echo CHtml::image(Yii::app()->baseUrl . $meta['blog-image']['value'], NULL, array('class'=>'preview-image')); ?></p>
	<?php endif; ?>
	<div class="preview-data">	
		<div class="preview-metadata">
			<span class="blog-author minor-meta">By
				<?php echo CHtml::link(CHtml::encode($model->author->displayName), Yii::app()->createUrl("/profile/{$model->author->id}/")); ?>
				<span class="separator">⋅</span> 
			</span>
			<span class="date"><?php echo $model->getCreatedFormatted() ?>
				<span class="separator">⋅</span> 
			</span>
			<span class="separator">⋅</span>
			<span class="minor-meta-wrap">
				<span class="blog-categories minor-meta">In
				<?php echo CHtml::link(CHtml::encode($model->category->name), Yii::app()->createUrl($model->category->slug)); ?>
				</span>
			</span>
		</div>
		<?php $md = new CMarkdownParser(); ?>
		<?php echo $md->safeTransform($model->content); ?>
	</div>
<?php endif; ?>