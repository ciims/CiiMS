<?php Yii::setPathOfAlias('bootstrap', Yii::getPathOfAlias('ext.bootstrap')); ?>
<div class="posts-container">
    <div class="list-view" style="position: relative; top: -20px">
        <div class="summary"></div>
<?php
$plus = Yii::app()->user->role !== 7 ? CHtml::link(NULL, Yii::app()->createUrl('/dashboard/content/save'), array('class' => 'icon-plus pull-right')) : NULL;
$this->widget('ext.bootstrap.widgets.TbExtendedGridView', array(
        'type' => 'striped',
        'dataProvider' => $model->search(),
        'filter' => $model,
        'responsiveTable' => true,
        'summaryCssClass' => 'sorter',
        'summaryText' => '<div class="content"><strong>Manage Content</strong> <span class="icon-exchange pull-right" id="perspective"></span>' . $plus .'<div class="summary-text">Showing {start} through {end} of {count}</div></div>',
        'bulkActions' => array(
        'actionButtons' => array(
            array(
                'buttonType' => 'button',
                'type' => 'danger',
                'size' => 'small',
                'label' => 'Delete Selected',
                'click' => 'js:function(values) {
                    CiiDashboard.Content.oldPerspective.bulkActionClick(values);
                    }'
                )
            ),
            'checkBoxColumnConfig' => array(
                'name' => 'id'
            ),
        ),
        'columns' => array(
            'title',
            'status'=>array(
                'name'=>'status',
                'value'=>'array_search($data->status, array("Draft"=>0, "Published"=>1))'
            ),
            'category_id'=>array(
                'name'=>'category_id',
                'value'=>'$data->category->name'
            ),
            'comment_count',
            'like_count',
            array(
                'class'=>'bootstrap.widgets.TbButtonColumn',
                'viewButtonUrl'=>'Yii::app()->createUrl($data->slug)',
                'viewButtonOptions' => array('class' => 'icon-eye-open'),
                'updateButtonUrl'=>'Yii::app()->createUrl("/dashboard/content/save/id/" . $data->id)',
                'updateButtonOptions' => array('class' => 'icon-edit'),
                'deleteButtonUrl'=>'Yii::app()->createUrl("/dashboard/content/delete/id/" . $data->id)',
                'deleteButtonOptions' => array('class' => 'icon-trash'),
            ),
        ),
    ));
?>
    </div>
</div>
<?php echo CHtml::tag('span', array('style' => 'display: none', 'id' => 'currentPerspective', 'value' => Yii::app()->session['admin_perspective']), NULL); ?>