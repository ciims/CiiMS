<div class="modal-container">
    <h2 class="pull-left"><?php echo Yii::t('DefaultTheme', 'Login to Your Account'); ?></h3>
    <hr class="clearfix"/>
    <?php $form=$this->beginWidget('cii.widgets.CiiActiveForm', array(
            'id'					=>	'login-form',
            'focus'					=> 'input[type="text"]:first',
            'registerPureCss'       => false,
            'enableAjaxValidation'	=>	true,
            'action'                => $this->createUrl('/login') . (isset($_GET['next']) ? '?next=' . $_GET['next'] : NULL),
            'htmlOptions' => array(
                'class' => 'pure-form pure-form-stacked'
            )
        )); ?>
    <?php if (!Yii::app()->user->isGuest): ?>
        <div class="alert alert-info">
            <?php echo Yii::t('DefaultTheme', "{{headsup}} Looks like you're already logged in as {{email}}. You should {{logout}} before logging in to another account.", array(
                    '{{headsup}}' => CHtml::tag('strong', array(), Yii::t('DefaultTheme', 'Heads Up!')),
                    '{{email}}'   => CHtml::tag('strong', array(), Yii::app()->user->email),
                    '{{logout}}'  => CHtml::tag('strong', array(), CHtml::link(Yii::t('DefaultTheme', 'logout'), $this->createUrl('/logout')))
                )); ?>
         </div>
    <?php else: ?>
        <?php if ($model->hasErrors()): ?>
            <div class="alert alert-danger">
                <?php echo Yii::t('DefaultTheme', "{{oops}} We weren't able to log you in using the provided credentials.", array(
                    '{{oops}}' => CHtml::tag('strong', array(), Yii::t('DefaultTheme', 'Oops!'))
                )); ?>
            </div>
        <?php endif; ?>
        <?php echo $form->TextField($model, 'username', array('class' => 'pure-u-1', 'id'=>'email', 'placeholder'=>Yii::t('DefaultTheme', 'Email Address'))); ?>
        <?php echo $form->PasswordField($model, 'password', array('class' => 'pure-u-1', 'id'=>'password', 'placeholder'=>Yii::t('DefaultTheme', 'Password'))); ?>
        <div class="pull-left">
            <?php echo CHtml::link(Yii::t('DefaultTheme', 'register'), $this->createUrl('/register')); ?>
            <span> | </span>
            <?php echo CHtml::link(Yii::t('DefaultTheme', 'forgot'), $this->createUrl('/forgot')); ?>
        </div>
        <button type="submit" class="pull-right pure-button pure-button-primary"><?php echo Yii::t('DefaultTheme', 'Submit'); ?></button>
        <div class="clearfix"></div>
    <?php endif; ?>
    
    <!-- Social Icons -->
    <?php if (Yii::app()->user->isGuest): ?>
        <?php if (count(Cii::getHybridAuthProviders()) >= 1): ?>
        <div class="clearfix" style="border-bottom: 1px solid #aaa; margin: 15px;"></div>
            <span class="login-form-links"><?php echo Yii::t('DefaultTheme', 'Or sign in with one of these social networks'); ?></span>
        <?php endif; ?>
        <div class="clearfix"></div>
        <div class="social-buttons">
            <?php foreach (Cii::getHybridAuthProviders() as $k=>$v): ?>
                <?php if (Cii::get($v, 'enabled', false) == 1): ?>
                    <?php echo CHtml::link(NULL, $this->createUrl('/hybridauth/'.$k), array('class' => 'social-icons ' . strtolower($k))); ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php $this->endWidget(); ?>
    <div class="clearfix"></div>
</div>
