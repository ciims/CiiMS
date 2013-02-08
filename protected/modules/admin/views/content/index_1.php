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