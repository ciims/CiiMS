<div class="row-fluid">
    <div>
     <?php $this->widget('bootstrap.widgets.TbButtonGroup', array(
         'htmlOptions' => array(
            'class' => 'pull-right'
         ),
        'buttons'=>array(
            array('label'=>'', 'url'=> $this->createUrl('/admin/content/perspective?id=1'), 'icon' => 'th-large', 'htmlOptions' => array('class'=>'active')),
            array('label'=>'', 'url'=>$this->createUrl('/admin/content/perspective?id=2'), 'icon' => 'th-list'),
            array('label' => 'New Post', 'url' => $this->createUrl('/admin/content/save'), 'type'=>'primary'),
        ),
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