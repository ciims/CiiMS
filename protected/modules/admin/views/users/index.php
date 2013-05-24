<div class="row-fluid">
    <div class="span12" style="margin-top: -20px;">
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
                            $.post("users/deleteMany", values, function(data) {
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
                    array(
                        'class' => 'bootstrap.widgets.TbImageColumn',
                        'imagePathExpression'=> '$data->gravatarImage(40)',
                        'htmlOptions' => array(
                            'style' => 'width: 40px'
                        )
                    ),
                    'name',
                    'displayName',
                    'email',
                    array(
                        'class'=>'bootstrap.widgets.TbToggleColumn',
                        'toggleAction'=>'/admin/users/toggle',
                        'name' => 'status',
                        'header' => 'Status'
                    ),
                    array(
                        'class'=>'bootstrap.widgets.TbButtonColumn',
                        'template' => '{update}{delete}',
                        'updateButtonUrl' => 'Yii::app()->createUrl("/admin/users/update/id/" . $data->id)',
                        'deleteButtonUrl'=>'Yii::app()->createUrl("/admin/users/delete/id/" . $data->id)',
                    ),
                ),
            ));
        ?>
    </div>
</div>