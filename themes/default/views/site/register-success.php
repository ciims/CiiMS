<div class="modal-container">
    <h2><?php echo Yii::t('DefaultTheme.main', 'Thanks for Registering!'); ?></h2>
    <hr />
    <p class="pull-text-left"><?php echo Yii::t('DefaultTheme.main', "Before you can login to your account we need you to verify your email address. Be on the lookout for an email from {{email}} containing activating instructions.", array(
        '{{email}}' => CHtml::tag('strong', array(), $notifyUser->email)
    )); ?></p>
</div>
