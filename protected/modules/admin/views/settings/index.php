<div class="well">
	<?php $this->widget('bootstrap.widgets.BootGridView',array(
		'id'=>'configuration-grid',
		'dataProvider'=>$model->search(),
		'filter'=>$model,
		'columns'=>array(
			'key',
			'value',
			array(
				'class'=>'bootstrap.widgets.BootButtonColumn',
				'template'=>'{update}{delete}',
				'updateButtonUrl'=>'Yii::app()->createUrl("/admin/settings/save/id/" . $data->key)',
			),
		),
	)); ?>
</div>