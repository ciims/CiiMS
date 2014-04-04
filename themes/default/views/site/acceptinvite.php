<div class="modal-container">
    <h2><?php echo Yii::t('DefaultTheme.main', 'Create Your Account'); ?></h2>
    <hr />
    <p class="pull-text-left"><?php echo Yii::t('DefaultTheme.main', 'To accept your invitation, set your account details here.'); ?></p>
    <?php $form = $this->beginWidget('cii.widgets.CiiActiveForm', array(
        'id'					=>	'login-form',
        'registerPureCss'       => false,
        'focus'					=>'	input[type="text"]:first',
        'enableAjaxValidation'	=>	true,
        'htmlOptions' => array(
            'class' => 'pure-form pure-form-stacked'
        )
    )); ?>
    <?php if ($model->hasErrors()): ?>
            <div class="alert alert-danger pull-text-left">
                <?php echo $form->errorSummary($model); ?>
            </div>
        <?php endif; ?>
        <?php echo $form->textField($model, 'email', array('class' => 'pure-u-1', 'placeholder' => Yii::t('DefaultTheme.main', 'Email Address'))); ?>
        <?php echo $form->textField($model, 'firstName', array('class' => 'pure-u-1', 'placeholder' => Yii::t('DefaultTheme.main', 'First Name'))); ?>
        <?php echo $form->textField($model, 'lastName', array('class' => 'pure-u-1', 'placeholder' => Yii::t('DefaultTheme.main', 'Last Name'))); ?>
        <?php echo $form->textField($model, 'displayName', array('class' => 'pure-u-1', 'placeholder' => Yii::t('DefaultTheme.main', 'Display Name'))); ?>
        <?php echo $form->passwordField($model, 'password', array('class' => 'pure-u-1', 'placeholder' => Yii::t('DefaultTheme.main', 'Password'))); ?>
    <div>
        <button type="submit" class="pull-right pure-button pure-button-primary"><?php echo Yii::t('DefaultTheme.main', 'Submit'); ?></button>
        <div class="clearfix"></div>

    </div>
    <?php $this->endWidget(); ?>
</div>
