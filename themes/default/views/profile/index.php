<h4 class="pull-left"><?php echo CHtml::encode($model->name); ?></h4>
<?php if (!Yii::app()->user->isGuest && $model->id == Yii::app()->user->id): ?>
	<span class="pull-right">
		<?php echo CHtml::link('edit', '/profile/edit'); ?>
	</span>
<?php endif; ?>
<div class="clearfix"></div>
<?php
$this->widget('bootstrap.widgets.TbAlert', array(
    'block'=>true, // display a larger alert block?
    'fade'=>true, // use transitions?
    'closeText'=>'×', // close link text - if set to false, no close link is displayed
    'alerts'=>array( // configurations per alert type
	    'success'=>array('block'=>true, 'fade'=>true, 'closeText'=>'×'), // success, info, warning, error or danger
    ),
));
?>
<div class="posts">
	<div class="post">
		<div style="padding: 5px; ">
			<table>
                <tbody>
                	<tr>
	                    <td style="padding-left: 0px;" rowspan="2">
	                    	<?php echo CHtml::image($model->gravatarImage(50)); ?>
	                    </td>
	                    <td style="padding-left: 10px;"><strong><?php echo $contentCount; ?></strong> Post(s)</td>
	                    <th><em>bio</em></th>
	                    <td>Website</td>
	                    <td>
	                    	<?php $website = Cii::get($model->parseMeta($model->metadata), 'website', NULL); ?>
	                    	<?php if ($website !== NULL): ?>
								<?php echo CHtml::link(CHtml::encode($website['value']), $website['value'], array('rel' => 'nofollow me')); ?>
	                    	<?php endif; ?>
	                    </td>
	                    
	                </tr>
	                <tr>
	                    <td style="padding-left: 10px;"><strong><?php echo Comments::model()->countByAttributes(array('user_id' => $model->id)); ?></strong> Comments</td>
	                    <th></th>
	                    <td>Member since</td>
	                    <td>
	                    	<time datetime="<?php echo date(DATE_ISO8601, strtotime($model->created)); ?>">
								<?php echo date('M Y', strtotime($model->created)); ?>
							</time>
	                    </td>
	                </tr>
	            </tbody>
	        </table>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
<div class="clearfix"></div>
<div class="sidebar">
	<div class="well-span span6">
		<h4>Posts By This User</h4>
		<div class="posts-by-author">
			<?php $this->widget('cii.widgets.CiiMenu', array('items' => $this->getPostsByAuthor($model->id))); ?>
			<?php echo CHtml::link('more', $this->createUrl('/search') . '?q=user_id:' . $model->id, array('class' => 'login-form-links pull-right')); ?>
		</div>
	</div>
	<div class="well-span span6" style="margin-right: 0px;">
		<h4>About</h4>
		<?php $about = $model->about; ?>
		<?php if ($about == NULL): ?>
			<?php if (Yii::app()->user->id == $model->id): ?>
				<div class="alert alert-info">
				  <strong>Oops!</strong> It looks like you haven't provided any information about yourself yet.
				  Why don't you <?php echo CHtml::link('edit your profile', $this->createUrl('/profile/edit')); ?>
				  and add siome information?
				</div>
			<?php else: ?>
				<div class="alert alert-info">
				  <strong>Oops!</strong> This user hasn't added any information about themself yet.
				</div>
			<?php endif; ?>
		<?php else: ?>
			<?php $md = new CMarkdownParser(); ?>
			<?php echo $md->safeTransform($about); ?>
		<?php endif; ?>
	</div>
</div>
<style>
 	main .main-body { margin-left: 0px; }
 	table td, table th { padding: 0px; padding-left: 5px; font-size: 12px; color: #777; }
</style>
<?php $this->widget('ext.timeago.JTimeAgo', array(
    'selector' => ' .timeago',
));
?>