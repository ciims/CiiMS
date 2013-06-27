<div class="posts-container">
	<?php Yii::import('application.modules.dashboard.components.ContentListView.ContentListView'); ?>
	<?php $this->widget('ContentListView', array(
	    'dataProvider' => $model->search(),
	    'summaryText' => false,
	    'itemView' => 'post',
	    'sorterHeader' => '<div class="content"><strong>Manage Content</strong>',
	    'itemsCssClass' => 'posts',
	    'sorterCssClass' => 'sorter',
	    'sortableAttributes' => array(
	        'title',
	        'created',
	        'updated',
	        'status',
	        'comment_count'
	    )    
	));
	?>
	<div class="clearfix"></div>
</div>