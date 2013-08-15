<h4><?php echo Yii::t('Install.main', 'Now Lets Connect To Your Database'); ?></h4>
<p><?php echo Yii::t('Install.main', "If you don't have a MySQL database setup yet, please do so now, then return to this page."); ?></p>

<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'db-source-form',
    'type'=>'inline',
    'enableAjaxValidation'=>true,
    'enableClientValidation'=>true,
)); ?>
    <div class="path-field">
        <?php echo $form->textFieldRow($model, 'username',  array('class'=>'input-small', 'placeholder' => Yii::t('Install.main', 'Username'))); ?>
        <?php echo $form->passwordFieldRow($model, 'password',  array('class'=>'input-small', 'placeholder' => Yii::t('Install.main', 'Password'))); ?>
    </div>
    <div class="clearfix" style="margin: 5px;"></div>
    <div class="path-field">
        <?php echo $form->textFieldRow($model, 'dbname',  array('class'=>'input-small', 'placeholder' => Yii::t('Install.main', 'Database Name'))); ?>
        <?php echo $form->textFieldRow($model, 'host',  array('class'=>'input-small', 'placeholder' => Yii::t('Install.main', 'Database Host'))); ?>
    </div>
    <div class="clearfix" style="margin: 5px;"></div>
    <hr />
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>Yii::t('Install.main', 'Validate'), 'htmlOptions' => array('class'=>'pull-right btn-inverse')) ); ?>
<?php $this->endWidget(); ?>