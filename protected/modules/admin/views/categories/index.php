<div class="row-fluid">
    <div class="span8" style="margin-top: -20px;">
        <?php
            $this->widget('bootstrap.widgets.TbExtendedGridView', array(
                'type' => 'striped bordered',
                'dataProvider' => $model->search(),
                'filter' => $model,
                'responsiveTable' => true,
                'bulkActions' => array(
                'actionButtons' => array(
                    array(
                        'buttonType' => 'button',
                        'type' => 'danger',
                        'size' => 'small',
                        'label' => 'Delete Selected',
                        'click' => 'js:function(values) {
                            $.post("categories/deleteMany", values, function(data) {
                                values.each(function() {
                                    $(this).parent().parent().remove();
                                });
                            });
                            }'
                        )
                    ),
                    'checkBoxColumnConfig' => array(
                        'name' => 'id'
                    ),
                ),
                'columns' => array(
                    'id',
                    'name',
                    'slug',
                    array(
                        'class'=>'bootstrap.widgets.TbButtonColumn',
                        'viewButtonUrl'=>'Yii::app()->createUrl("/" . $data->slug)',
                        'updateButtonUrl' => 'Yii::app()->createUrl("/admin/categories/save/id/" . $data->id)',
                        'deleteButtonUrl'=>'Yii::app()->createUrl("/admin/categories/delete/id/" . $data->id)',
                    ),
                ),
            ));
        ?>
    </div>
    <div class="span4 ">
        <?php $this->renderPartial('_form', array('model' => $model)); ?>
    </div>
</div>