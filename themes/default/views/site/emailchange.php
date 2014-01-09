<div class="modal-container">
    <h2><?php echo Yii::t('DefaultTheme.main', 'Change Your Email Address'); ?></h2>
    <hr />
    <?php $form=$this->beginWidget('cii.widgets.CiiActiveForm', array(
        'id'					=> 'login-form',
        'focus'					=> 'input[type="text"]:first',
        'regiterPureCss'        => false,
        'enableAjaxValidation'	=>	true,
        'htmlOptions' => array(
            'class' => 'pure-form pure-form-stacked'
        )
    )); ?>
        <?php if(Yii::app()->user->hasFlash('authenticate-error')):?>
            <div class="alert alert-danger">
                <?php echo Yii::app()->user->getFlash('authenticate-error'); ?>
            </div>
        <?php endif; ?>

        <?php if($success): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if (!$success): ?>
            <p class="pull-text-left"><?php echo Yii::t('DefaultTheme.main', 'To change the email address associated to your account, please enter your current password.'); ?></p>
            <?php echo CHtml::passwordField('password',  Cii::get($_POST, 'password', ''), array('class' => 'pure-u-1', 'placeholder'=>Yii::t('DefaultTheme.main', 'Your current password'))); ?>
            <button type="submit" class="pull-right pure-button pure-button-primary"><?php echo Yii::t('DefaultTheme.main', 'Submit'); ?></button>
            <div class="clearfix"></div>
        <?php endif; ?>
    <?php $this->endWidget(); ?>
    <div class="clearfix"></div>
</div>
