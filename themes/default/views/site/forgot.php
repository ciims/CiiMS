<div class="modal-container">
    <h2 class="pull-left"><?php echo Yii::t('DefaultTheme', 'Forgot Your Password?'); ?></h3>
    <hr class="clearfix"/>
    <p class="pull-text-left"><?php echo Yii::t('DefaultTheme', 'Did you forget your password? Enter your email address below and we\'ll send you an email link that will allow you to reset your password.'); ?></p>
    <?php $form=$this->beginWidget('cii.widgets.CiiActiveForm', array(
            'id'					=>	'login-form',
            'focus'					=> 'input[type="text"]:first',
            'registerPureCss'       => false,
            'enableAjaxValidation'	=>	true,
            'htmlOptions' => array(
                'class' => 'pure-form pure-form-stacked'
            )
        )); ?>
        <?php if(Yii::app()->user->hasFlash('reset-sent')):?>
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php echo Yii::app()->user->getFlash('reset-sent'); ?>
            </div>
        <?php endif; ?>

        <?php if ($id == NULL): ?>
            <?php echo CHtml::textField('email', Cii::get($_POST, 'email', ''), array('class' => 'pure-u-1', 'placeholder'=>Yii::t('DefaultTheme', 'Your email address (you@example.com)'))); ?>

        <?php else: ?>
            <?php if ($badHash): ?>
                <br />
                <div class="alert alert-danger"><?php echo Yii::t('DefaultTheme', 'The password reset key you provided is either invalid or has expired.'); ?></div>
            <?php else: ?>

                <?php echo CHtml::passwordField('password',  Cii::get($_POST, 'password', ''), array('class' => 'pure-u-1', 'placeholder'=>Yii::t('DefaultTheme', 'Your new password'))); ?>
                <br />
                <br />
                <?php echo CHtml::passwordField('password2',  Cii::get($_POST, 'password2', ''), array('class' => 'pure-u-1', 'placeholder'=>Yii::t('DefaultTheme', 'Once more with feeling!'))); ?>
            <?php endif; ?>
                <?php endif; ?>

        <div class="pull-left">
            <?php echo CHtml::link(Yii::t('DefaultTheme', 'login'), $this->createUrl('/login')); ?>
            <span> | </span>
            <?php echo CHtml::link(Yii::t('DefaultTheme', 'register'), $this->createUrl('/register')); ?>
        </div>
        <button type="submit" class="pull-right pure-button pure-button-primary"><?php echo Yii::t('DefaultTheme', 'Submit'); ?></button>
        <div class="clearfix"></div>
    
    <?php $this->endWidget(); ?>
    <div class="clearfix"></div>
</div>
