<?php Yii::import('ext.redactor.ImperaviRedactorWidget'); ?>
<?php
$this->widget('ImperaviRedactorWidget', array(
        'model' => $model,
        'attribute' => 'content',
        'options' => array(
            'focus' => true,
            'autoresize' => false,
            'imageUpload' => $this->createUrl('/admin/content/upload?id=' . $model->id),
            'fileUpload' => $this->createUrl('/admin/content/upload?id=' . $model->id),
            'autosave' => $this->createUrl('/admin/content/save/' . $model->id),
            'interval' => 120,
            'autosaveCallback' => 'saveCallback',
            'air' => $preferMarkdown,
            'airButtons' => array(
                'formatting',
                '|',
                'bold',
                'italic',
                'deleted',
                '|',
                'unorderedlist',
                'orderedlist',
                'indent',
                '|',
                'image',
                'video',
                'file',
                'link',
                'alignment',
                'horizontalrule'
            )
        )
    ));
?>