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
    <div class="span4">
        <div class="well">test</div>
    </div>
    <?php $this->endWidget(); ?>
</div>

