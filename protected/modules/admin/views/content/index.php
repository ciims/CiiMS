<div class="well">
	<?php $this->widget('bootstrap.widgets.BootButton', array('label'=>'New Post', 'url'=>Yii::app()->createUrl('/admin/content/save'), 'type'=>'primary', 'size'=>'normal', 'htmlOptions'=>array('style'=>'float:right;'))); ?>
	<br />
	<?php $this->widget('bootstrap.widgets.BootGridView',array(
		'id'=>'content-grid',
		'dataProvider'=>$model->search(),
		'filter'=>$model,
		'columns'=>array(
			'author_id'=>array(
				'name'=>'author_id',
				'value'=>'Users::model()->findByPk($data->author_id)->displayName'
			),
			'title',
			'status'=>array(
				'name'=>'status',
				'value'=>'array_search($data->status, array("Draft"=>0, "Published"=>1))'
			),
			'category_id'=>array(
				'name'=>'category_id',
				'value'=>'Categories::model()->findByPk($data->category_id)->name'
			),
			'comment_count',
			array(
				'class'=>'bootstrap.widgets.BootButtonColumn',
				'viewButtonUrl'=>'Yii::app()->createUrl($data->slug)',
				'updateButtonUrl'=>'Yii::app()->createUrl("/admin/content/save/id/" . $data->id)',
				'deleteButtonUrl'=>'Yii::app()->createUrl("/admin/content/delete/id/" . $data->id)',
			),
		),
	)); ?>
</div>