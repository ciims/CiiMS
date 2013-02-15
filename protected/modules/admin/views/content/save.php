<?php Yii::import('ext.redactor.*'); ?>

<div class="row-fluid">
    <?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
        'id'=>'horizontalForm',
        'type'=>'horizontal',
    )); ?>
    <div class="span8">
        <?php if ($preferMarkdown): ?>
            <?php echo $form->markdownEditorRow($model, 'content', array('height'=>'200px'));?>
        <?php else: ?>
            <?php $this->widget('ImperaviRedactorWidget', array(
                    'model' => $model,
                    'attribute' => 'content',
                    'options' => array(
                        'focus' => true,
                        'autoresize' => false,
                        'autosave' => $this->createUrl('/admin/content/save/' . $model->id),
                        'interval' => 120,
                        'autosaveCallback' => 'saveCallback',
                    )
                ));
            ?>
        <?php endif; ?>
    </div>
    <div class="span4 sidebarNav">
        <div class="well">
            <h5><i class="icon-upload"></i> Uploads</h5>
        </div>
        
        <div class="well">
            <h5><i class="icon-tags"></i> Tags</h5>
        </div>
        
        <div class="well">
            <h5><i class="icon-align-justify"></i> Details</h5>
        </div>
        
    </div>
    <?php $this->endWidget(); ?>
</div>

