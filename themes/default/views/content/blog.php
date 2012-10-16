<h2><?php echo CHtml::link($data->title, Yii::app()->createUrl($data->slug)); ?></h2>
<div class="blog-data">
	<?php echo $data->created == $data->updated ? 'Posted' : 'Updated'; ?>
	<strong><?php echo date('F jS, Y @ H:i', strtotime($data->created)); ?></strong>
	by <strong><?php echo $data->author->displayName; ?></strong>
	in <?php echo CHtml::link($data->category->name, Yii::app()->createUrl($data->category->slug)); ?>
	<?php if ($data->commentable): ?>
		 - <?php echo CHtml::link("<span class=\"label label-info\">{$data->comment_count} Comments</span>", '#comments', array('escape'=>true)); ?>
	<?php endif; ?>
	<br /><br />
</div>
<?php if (isset($meta['blog-image'])): ?>
	<p><?php echo CHtml::image(Yii::app()->baseUrl . $meta['blog-image']['value'], NULL, array('class'=>'image')); ?></p>
<?php endif; ?>
<div class="clear"></div>
<div class="content">
	<?php $md = new CMarkdownParser; echo $md->safeTransform($data->content); ?>
</div>
</div>
<hr />
<div class="span3"></div>
<div class="span8 well" style="float:right; margin-right: 6%;">
<?php if ($data->commentable): ?>
<h4><?php echo Yii::t('comments', 'n==0#No Comments|n==1#{n} Comment|n>1#{n} Comments', count($comments)); ?>: </h4>
<?php echo CHtml::link(NULL, NULL, array('name'=>'comments')); ?>
	<?php $count = 0; ?>
	<?php foreach ($comments as $comment): ?>	
		<?php if (!$comment->approved): ?>
			<?php continue; ?>
		<?php endif; ?>
		<?php $count++; ?>
		
		<div id="comment-<?php echo $comment->id; ?>">
			<?php echo CHtml::image('https://gravatar.com/avatar/' . md5(Users::model()->findByPk($comment->user_id)->email), NULL, array('class'=>'avatar')); ?>
			<div class="comment-block <?php echo $data->author->id == $comment->author->id ? 'alert-success author' : ''; ?>">
				<p class="comment-post red-block">
					By: <?php echo Users::model()->findByPk($comment->user_id)->displayName; ?>
					on <?php echo date('F jS, Y @ H:i', strtotime($comment->created)); ?>
				</p>
				<p class="comment-post" style="margin-top: 7px"><?php echo $comment->comment; ?></p>
				<div style="float:right; margin-top: 5px;">
					<?php if (isset(Yii::app()->user->role) && Yii::app()->user->role == 5): ?>
						<?php echo CHtml::link('Delete', NULL, array('id'=>'delete', 'value'=>$comment->id, 'class'=>'label label-inverse', 'style'=>'margin-left: 5px;')); ?>
					<?php endif; ?>
					
					<?php if (!Yii::app()->user->isGuest): ?>
						<?php echo CHtml::link('Flag', NULL, array('id'=>'flag', 'value'=>$comment->id, 'class'=>'label label-important', 'style'=>'margin-left: 5px;')); ?>		
						<?php echo CHtml::link('Reply', '#reply', array('class'=>'label label-info', 'style'=>'margin-left: 5px;')); ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<br />
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
