<div class="modal-container">
    <h2><?php echo Yii::t('DefaultTheme', 'Thanks for Activating Your Account!'); ?></h2>
    <hr />
    <p class="pull-text-left"><?php echo Yii::t('DefaultTheme', "You may now {{login}} and head to the {{dashboard}}", array(
        '{{login}}' => CHtml::link(Yii::t('DefaultTheme', 'login'), $this->createUrl('/login')),
        '{{dashboard}}' => CHtml::link(Yii::t('DefaultTheme', 'dashboard'), $this->createUrl('/dashboard'))
    )); ?></p>
</div>
