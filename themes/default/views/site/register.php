<div class="modal-container">
    <h2 class="pull-left"><?php echo Yii::t('DefaultTheme', 'Register an Account'); ?></h3>
    <hr class="clearfix"/>
    <?php $form=$this->beginWidget('cii.widgets.CiiActiveForm', array(
            'id'					=>	'login-form',
            'focus'					=> 'input[type="text"]:first',
            'registerPureCss'       => false,
            'enableAjaxValidation'	=>	true,
            'action'                => $this->createUrl('/register'),
            'htmlOptions' => array(
                'class' => 'pure-form pure-form-stacked'
            )
        )); ?>
    <?php if (!Yii::app()->user->isGuest): ?>
        <div class="alert alert-info">
            <?php echo Yii::t('DefaultTheme', "{{headsup}} Looks like you're already logged in as {{email}}", array(
                    '{{headsup}}' => CHtml::tag('strong', array(), Yii::t('DefaultTheme', 'Heads Up!')),
                    '{{email}}'   => CHtml::tag('strong', array(), Yii::app()->user->email),
                )); ?>
         </div>
    <?php else: ?>
        <?php if ($model->hasErrors()): ?>
            <div class="alert alert-danger">
                <?php echo Yii::t('DefaultTheme', "{{oops}} There were errors in your form submission. Please correct the items in red below.", array(
                    '{{oops}}' => CHtml::tag('strong', array(), Yii::t('DefaultTheme', 'Oops!'))
                )); ?>
            </div>
        <?php endif; ?>
        
        <?php echo $form->TextField($model, 'email', array('class' => 'pure-u-1', 'id'=>'email', 'placeholder'=>Yii::t('DefaultTheme', 'Email Address'))); ?>
        <?php echo $form->TextField($model, 'displayName', array('class' => 'pure-u-1', 'id'=>'username', 'placeholder'=>Yii::t('DefaultTheme', 'Username'))); ?>
        <?php echo $form->PasswordField($model, 'password', array('class' => 'pure-u-1', 'id'=>'password', 'placeholder'=>Yii::t('DefaultTheme', 'Password'))); ?>
        <div id ="password_strength_1" class="password_strength_container">
            <div class="password_strength_bg"></div>
            <div class="password_strength" style="width: 0%;"></div>
            <div class="password_strength_separator" style="left: 25%;"></div>
            <div class="password_strength_separator" style="left: 50%;"></div>
            <div class="password_strength_separator" style="left: 75%;"></div>
            <div class="password_strength_desc"></div>
            <div class="clearfix"></div>
        </div>
        <?php echo $form->PasswordField($model, 'password2', array('class' => 'pure-u-1', 'id'=>'password', 'placeholder'=>Yii::t('DefaultTheme', 'Password (again)'), 'id' => 'password2')); ?>
        <div id ="password_strength_2" class="password_strength_container">
            <div class="password_strength_bg"></div>
            <div class="password_strength" style="width: 0%;"></div>
            <div class="password_strength_separator" style="left: 25%;"></div>
            <div class="password_strength_separator" style="left: 50%;"></div>
            <div class="password_strength_separator" style="left: 75%;"></div>
            <div class="password_strength_desc"></div>
            <div class="clearfix"></div>
        </div>
                            
    
        <div class="pull-left">
            <?php echo CHtml::link(Yii::t('DefaultTheme', 'login'), $this->createUrl('/login')); ?>
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
            <span class="login-form-links"><?php echo Yii::t('DefaultTheme', 'Or register with one of these social networks'); ?></span>
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

<?php Yii::app()->clientScript->registerScriptFile($this->asset .'/js/zxcvbn.js'); ?>
<?php Yii::app()->clientScript->registerScript('password_strength_meter', '$(document).ready(function() { Theme.loadRegister(); });', CClientScript::POS_END);
