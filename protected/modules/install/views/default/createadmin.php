<h3><?php echo Yii::t('Install.main', '{create} Admin User', array('{create}' => CHtml::tag('span', array('class' => 'highlight'), Yii::t('Install.main', 'Create')))); ?></h3>
<p><?php echo Yii::t('Install.main', 'Lets setup your first admin user, and set a few site settings. All of these settings can be changed later in the admin panel.'); ?></p>

<?php $form = $this->beginWidget('cii.widgets.CiiBaseActiveForm', array(
    'id'=>'user-form',
    'type'=>'inline',
    'htmlOptions' => array(
        'class' => 'pure-form pure-form-aligned'
    )
)); ?>
    <div class="pure-u-1">
        <?php echo $form->textField($model, 'email',  array('class'=>'pure-u-1-3', 'placeholder' => Yii::t('Install.main', 'Email'))); ?>
        <?php echo $form->passwordField($model, 'password',  array('class'=>'pure-u-1-3', 'placeholder' => Yii::t('Install.main', 'Password'))); ?>
    </div>
    <div class="pure-u-1">
        <?php echo $form->textField($model, 'firstName',  array('class'=>'pure-u-1-3', 'placeholder' => Yii::t('Install.main', 'First Name'))); ?>
        <?php echo $form->textField($model, 'lastName',  array('class'=>'pure-u-1-3', 'placeholder' => Yii::t('Install.main', 'Last Name'))); ?>
    </div>
   <div class="pure-u-1">
        <?php echo $form->textField($model, 'displayName', array('class'=>'pure-u-1-3', 'placeholder' => Yii::t('Install.main', 'Display Name'))); ?>
        <?php echo $form->textField($model, 'siteName', array('class'=>'pure-u-1-3', 'placeholder' => Yii::t('Install.main', 'Site Name'))); ?>
    </div>
    <hr />

   <button class="pure-button pure-button-primary" type="submit"><?php echo Yii::t('Install.main', 'Create Admin User'); ?></button>
<?php $this->endWidget(); ?>