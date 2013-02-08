<?php $this->beginContent('/layouts/dashboard'); ?>
    <div class="row-fluid">
        <div>
         <?php $this->widget('bootstrap.widgets.TbButtonGroup', array(
             'htmlOptions' => array(
                'class' => 'pull-right'
             ),
            'buttons'=>array(
                array('label'=>'', 'url'=> $this->createUrl('/admin/content/perspective?id=1'), 'icon' => 'th-large', 'htmlOptions' => array('class'=>Yii::app()->session['admin_perspective'] == 1 ? 'active' : NULL)),
                array('label'=>'', 'url'=>$this->createUrl('/admin/content/perspective?id=2'), 'icon' => 'th-list', 'htmlOptions' => array('class'=>Yii::app()->session['admin_perspective'] == 2 ? 'active' : 'hidden-phone')),
                array('label' => 'New Post', 'url' => $this->createUrl('/admin/content/save'), 'type'=>'primary'),
            ),
        )); ?>
        </div>
        <div class="clearfix"></div>
        <?php echo $content; ?>
    </div>
<?php $this->endContent(); ?>