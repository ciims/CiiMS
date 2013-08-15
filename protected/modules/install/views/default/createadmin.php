<h4><?php echo Yii::t('Install.main', 'Create Admin User'); ?></h4>
<p><?php echo Yii::t('Install.main', 'Lets setup your first admin user, and set a few site settings. All of these settings can be changed later in the admin panel.'); ?></p>

<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'user-form',
    'type'=>'inline',
    'enableAjaxValidation'=>true,
    'enableClientValidation'=>true,
)); ?>
    <div class="path-field">
        <?php echo $form->textFieldRow($model, 'email',  array('class'=>'input-small', 'placeholder' => Yii::t('Install.main', 'Email'))); ?>
        <?php echo $form->passwordFieldRow($model, 'password',  array('class'=>'input-small', 'placeholder' => Yii::t('Install.main', 'Password'))); ?>
    </div>
    <div class="clearfix" style="margin: 5px;"></div>
    <div class="path-field">
        <?php echo $form->textFieldRow($model, 'firstName',  array('class'=>'input-small', 'placeholder' => Yii::t('Install.main', 'First Name'))); ?>
        <?php echo $form->textFieldRow($model, 'lastName',  array('class'=>'input-small', 'placeholder' => Yii::t('Install.main', 'Last Name'))); ?>
    </div>
    <div class="clearfix" style="margin: 5px;"></div>
    <div class="path-field">
        <?php echo $form->textFieldRow($model, 'displayName', array('class'=>'input-small', 'placeholder' => Yii::t('Install.main', 'Display Name'))); ?>
        <?php echo $form->textFieldRow($model, 'siteName', array('class'=>'input-small', 'placeholder' => Yii::t('Install.main', 'Site Name'))); ?>
    </div>
    <div class="clearfix" style="margin: 5px;"></div>
    <hr />
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>Yii::t('Install.main', 'Submit'), 'htmlOptions' => array('class'=>'pull-right btn-inverse')) ); ?>
<?php $this->endWidget(); ?>