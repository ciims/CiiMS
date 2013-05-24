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
                            $.post("settings/deleteMany", values, function(data) {
                                values.each(function() {
                                    $(this).parent().parent().remove();
                                });
                            });
                            }'
                        )
                    ),
                    'checkBoxColumnConfig' => array(
                        'name' => 'key'
                    ),
                ),
                'columns' => array(
                    'key',
                    'value',
                    array(
                        'class'=>'bootstrap.widgets.TbButtonColumn',
                        'template' => '{update}{delete}',
                        'updateButtonUrl' => 'Yii::app()->createUrl("/admin/settings/save/id/" . $data->key)',
                        'deleteButtonUrl'=>'Yii::app()->createUrl("/admin/settings/delete/id/" . $data->key)',
                    ),
                ),
            ));
        ?>
    </div>
    <div class="span4 ">
        <?php $this->renderPartial('_form', array('model' => $model)); ?>
    </div>
</div>