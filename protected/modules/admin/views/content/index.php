<div class="row-fluid">
    <div>
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'type' => 'primary',
        'label' => 'New Post',
        'htmlOptions' => array(
            'class' => 'pull-right'
        ),
        'size' => 'small',
        'url' => $this->createUrl('/admin/content/save')
    )); ?>
    </div>
    <div class="clearfix"></div>
    <?php $this->widget('bootstrap.widgets.TbListView', array(
        'dataProvider' => $model->search(),
        'itemView' => '_itemView',
        'summaryText' => false,
        'sorterHeader' => '<h4 class="pull-left">Manage Content</h4>Sort By:',
        'sortableAttributes' => array(
            'title',
            'created',
            'updated',
            'status',
            'comment_count'
        )    
    ));
    ?>
</div>