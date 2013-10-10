<?php if ($model !== null): ?>
	<?php $meta = Content::model()->parseMeta($model->metadata); ?>
	<div class="preview-header">
		<span class="title pull-left"><?php echo ($model->title !== '' ? $model->title : CHtml::tag('em', array(), Yii::t('Dashboard.main', 'Drafted Post'))); ?></span>
		<?php echo CHtml::link(NULL, Yii::app()->createUrl('/dashboard/content/delete/id/' . $model->id), array('class' => 'icon-trash pull-right')); ?>
		<?php echo CHtml::link(NULL, Yii::app()->createUrl('/dashboard/content/save/id/' . $model->id), array('class' => 'icon-edit pull-right')); ?>
		<span class="icon-comment pull-right"></span>
		<?php if ($model->status == 1 && strtotime($model->published) <= time()): ?>
			<?php echo CHtml::link(NULL, Yii::app()->createUrl($model->slug), array('class' => 'icon-eye-open pull-right')); ?>
		<?php endif; ?>
		<div class="clearfix"></div>
	</div>
	<?php if (Cii::get(Cii::get($meta, 'blog-image', array()), 'value', '') != ""): ?>
		<p style="text-align:center;"><?php echo CHtml::image(Yii::app()->baseUrl . $meta['blog-image']['value'], NULL, array('class'=>'preview-image')); ?></p>
	<?php endif; ?>
	<div class="preview-data">	
		<div class="preview-metadata">
			<span class="blog-author minor-meta">
				<?php echo Yii::t('Dashboard.main', 'By {{user}}', array(
					'{{user}}' => CHtml::link(CHtml::encode($model->author->displayName), Yii::app()->createUrl("/profile/{$model->author->id}/"))
				)); ?>
				<span class="separator">⋅</span> 
			</span>
			<span class="date"><?php echo Cii::formatDate($model->published) ?>
				<span class="separator">⋅</span> 
			</span>
			<span class="separator">⋅</span>
			<span class="minor-meta-wrap">
				<span class="blog-categories minor-meta">
					<?php echo Yii::t('Dashboard.main', 'In {{category}}', array(
					'{{category}}' => CHtml::link(CHtml::encode($model->category->name), Yii::app()->createUrl($model->category->slug))
				)); ?>
				</span>
			</span>
		</div>
		<?php $md = new CMarkdownParser(); ?>
		<div id="md-output"></div>
		<textarea id="markdown" style="display:none"><?php echo $model->content; ?></textarea>
		<span id="item-id" style="display:none;"><?php echo $model->id; ?></span>
		<span id="item-status" style="display:none;"><?php echo $model->status; ?></span>
		<noscript>
			<?php echo $md->safeTransform($model->content); ?>
		<noscript>

	</div>
<?php endif; ?>