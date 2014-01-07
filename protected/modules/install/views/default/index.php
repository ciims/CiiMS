<h3>
    <?php echo Yii::t('Install.main', '{connect} to MySQL Database', array(
        '{connect}' => CHtml::tag('span', array('class' => 'highlight'), 'Connect')
    )); ?>
</h3>
<hr />
<p><?php echo Yii::t('Install.main', "If you don't have a MySQL database setup yet, please do so now, then return to this page."); ?></p>

<?php $form = $this->beginWidget('cii.widgets.CiiBaseActiveForm', array(
    'id'=>'db-source-form',
    'htmlOptions' => array(
        'class' => 'pure-form pure-form-aligned'
    )
)); ?>
    <fieldset>
            <div class="pure-u-1">
                <?php echo $form->textField($model, 'username',  array('class'=>'pure-u-1-3', 'placeholder' => Yii::t('Install.main', 'Username'))); ?>
                <?php echo $form->passwordField($model, 'password',  array('class'=>'pure-u-1-3', 'placeholder' => Yii::t('Install.main', 'Password'))); ?>
            </div>
            <div class="pure-u-1">
                <?php echo $form->textField($model, 'dbname',  array('class'=>'pure-u-1-3', 'placeholder' => Yii::t('Install.main', 'Database Name'))); ?>
                <?php echo $form->textField($model, 'host',  array('class'=>'pure-u-1-3', 'placeholder' => Yii::t('Install.main', 'Database Host'))); ?>
            </div>
    </fieldset>
    <div class="clearfix"></div>
    <hr />
    <button class="pure-button pure-button-primary" type="submit"><?php echo Yii::t('Install.main', 'Check MySQL Connection'); ?></button>
    
<?php $this->endWidget(); ?>