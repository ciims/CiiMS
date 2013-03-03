<?php $content = &$data; ?>
<?php $meta = Content::model()->parseMeta($content->metadata); ?>

<div class="content">
	<div class="post">
		<?php if (Cii::get(Cii::get($meta, 'blog-image', array()), 'value', '') != ""): ?>
			<p style="text-align:center;"><?php echo CHtml::image(Yii::app()->baseUrl . $meta['blog-image']['value'], NULL, array('class'=>'image')); ?></p>
		<?php endif; ?>
		<div class="post-inner">
			<div class="post-header">
				<h3><?php echo CHtml::link($content->title, Yii::app()->createUrl($content->slug)); ?></h3>
			</div>
			<div class="blog-meta inline">
				<span class="date"><?php echo $content->getCreatedFormatted() ?></span>
				<span class="separator">⋅</span>
				<span class="blog-author minor-meta"><strong>by </strong>
					<span>
						<?php echo $content->author->displayName; ?>
					</span>
					<span class="separator">⋅</span> 
				</span> 
				<span class="minor-meta-wrap">
					<span class="blog-categories minor-meta"><strong>in </strong>
					<span>
						<?php echo CHtml::link($content->category->name, Yii::app()->createUrl($content->category->slug)); ?>
					</span> 
					<span class="separator">⋅</span> 
				</span> 					
				<span class="comment-container">
					<?php echo $content->comment_count; ?> Comments</a>					
				</span>
			</div>
				<?php $md = new CMarkdownParser; echo $md->safeTransform($content->content); ?>
		</div>
	    <div style="clear:both;"><br /></div>
	</div>
	
	<div class="" style="float:right; margin-right: 6%;">
	<?php if ($data->commentable): ?>
	<h4><?php echo Yii::t('comments', 'n==0#No Comments|n==1#{n} Comment|n>1#{n} Comments', count($comments)); ?>: </h4>
	<?php echo CHtml::link(NULL, NULL, array('name'=>'comments')); ?>
		<?php $count = 0; ?>
		<?php foreach ($comments as $comment): ?>	
			<?php if (!$comment->approved): ?>
				<?php continue; ?>
			<?php endif; ?>
			<?php $count++; ?>
			<?php $this->renderPartial('/comment/comment', array('comment'=>$comment)); ?>
		<?php endforeach; ?>
		<div id="new-comment" style="display:none;"></div>
		<?php echo CHtml::link(NULL, NULL, array('name'=>'reply')); ?>
		<?php if (Yii::app()->user->isGuest): ?>
			<p>Only registered users to can leave comments. Please <?php echo CHtml::link('login', Yii::app()->createUrl('/login')); ?> to leave a comment.</p>
		<?php else: ?>
			<?php $form=$this->beginWidget('CActiveForm', array(
					'id'=>'reply',
					'focus'=>array($model,'comment'),
					'enableAjaxValidation'=>false,
					'errorMessageCssClass'=>'alertBox-alert',
				)); ?>
				<?php echo $form->error($model,'comment'); ?>
				<?php echo $form->hiddenField($model, 'content_id', array('value'=>$data->id)); ?>	
				<?php echo CHtml::hiddenField('count', NULL, array('value'=>$count)); ?>	
				
					<br /><br />
				<?php echo $form->textArea($model, 'comment', array('id'=>'comment', 'placeholder'=>'Type your comment here', 'rows'=>5, 'class'=>'span20')); ?>
		
				<?php echo CHtml::submitButton('Comment', array('class'=>'btn btn-primary', 'style'=>'float:right;')); ?>
			<?php $this->endWidget(); ?>
		<?php endif; ?>
		<?php Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/' . Yii::app()->theme->name .'/commentform.js'); ?>
	<?php endif; ?>
	
	<?php Yii::app()->clientScript->registerScript('moveComments', '$("div[id*=comment-]").css("margin", "5px");'); ?>
	</div>
</div>