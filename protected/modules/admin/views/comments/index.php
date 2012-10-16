<div class="well">
	<?php $this->widget('bootstrap.widgets.BootGridView',array(
		'id'=>'comments-grid',
		'dataProvider'=>$model->search(),
		'filter'=>$model,
		'columns'=>array(
			'updated',
			'author'=>array(
				'name'=>'author',
				'value'=>'$data->author->displayName'
			),
			'content'=>array(
				'name'=>'content',
				'value'=>'$data->content->title'
			),
			array(
				'class'=>'bootstrap.widgets.BootButtonColumn',
				'template'=>'{view}{delete}',
				'viewButtonUrl'=>'Yii::app()->createUrl($data->content->slug."#comment-".$data->id)'
			),
		),
	)); ?>
</div>
