<div class="post" data-attr-id="<?php echo $data->id; ?>">
	<div class="post-metadata">
		<div class="post-metadata-inner">
			<span class="row comments">
				<?php if (Cii::getConfig('useDisqusComments')): ?>
					<strong><?php echo CHtml::link(0, Yii::app()->createUrl($data->slug) . '#disqus_thread', array('class' => 'comment-count-sp')); ?></strong> <span class="icon-comment"></span>
				<?php else: ?>
					<strong><?php echo $data->comment_count; ?></strong> <span class="icon-comment"></span>
				<?php endif; ?>
				</span>
			<span class="row likes">
				<strong><?php echo $data->like_count; ?></strong> <span class="icon-heart"></span>
			</span>
			<!--<span class="row views">
				<strong>0</strong> <span class="icon-bar-chart"></span>
			</span> -->
		</div>
	</div>
	<div class="post-data">
		<h6><?php echo ($data->title !== '' ? $data->title : CHtml::tag('em', array(), 'Drafted Post')); ?></h6>
		<span class="author"><?php echo Yii::t('Dashboard.views', 'By: {{user}}', array('{{user}}' => $data->author->displayName)); ?></span>

		<?php if ($data->status == 0): ?>
			<span class="status draft"><?php echo Yii::t('Dashboard.views', 'Draft'); ?></span>
		<?php elseif ($data->status == 1 && strtotime($data->published) > time()): ?>
			<span class="status scheduled">
				<?php echo Yii::t('Dashboard.views', 'Scheduled for {{date}}', array(
					'{{date}}' => CHtml::tag('abbr',array(
									'data-original-title'=> Cii::formatDate($data->published),
									'title'=> Cii::formatDate($data->published, 'c')
								),
								Cii::formatDate($data->published)
								)
					)); ?>
			</span>
		<?php else: ?>
			<span class="status published">
				<?php echo CHtml::tag(
					'abbr',
					array(
						'data-original-title'=> Cii::formatDate($data->published),
						'title'=> Cii::formatDate($data->published, 'c'),
						'class' => 'timeago'
					),
					Cii::formatDate($data->published)
					); ?>
			</span>
		<?php endif; ?>

	</div>
	<div class="clearfix"></div>
</div>