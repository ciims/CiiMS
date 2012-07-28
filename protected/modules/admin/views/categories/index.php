<div class="well">
	<?php $this->widget('bootstrap.widgets.BootButton', array('label'=>'New Category', 'type'=>'primary', 'size'=>'normal', 'htmlOptions'=>array('id'=>'categoriesButton', 'style'=>'float:right;'))); ?>
	<br />
	<div id="form" class="hidden">
		<?php echo $this->renderPartial('_form',array('model'=>$model)); ?>
	</div>
	<?php $this->widget('bootstrap.widgets.BootGridView',array(
	'id'=>'categories-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'parent_id'=>array(
			'name'=>'parent_id',
			'value'=>'Categories::model()->findByPk($data->parent_id)->name'
		),
		'name',
		'slug',
		array(
			'class'=>'bootstrap.widgets.BootButtonColumn',
			'template'=>'{view}{delete}',
			'viewButtonUrl'=>'Yii::app()->createUrl($data->slug)',
			'updateButtonUrl'=>'Yii::app()->createUrl("/admin/categories/save/id/".$data->id)'
		),
	),
)); ?>
</div>

<?php Yii::app()->clientScript->registerScript('categoriesButton', '
	$("#categoriesButton").click(function(){
		$("#form").slideToggle();
	});

');?>