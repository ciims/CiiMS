<?php echo $user->displayName; ?>,<br /><br />
<?php echo Yii::t('Thanks for registering your account at CiiMS Blog {{blog}}. To verify your account, {{clickhere}}. This action will verify your email address and allow you to interact with features only available to registered users.', array(
		'{{blog}}' => CHtml::link(Cii::getConfig('name', Yii::app()->name), Yii::app()->createAbsoluteUrl('/')),
		'{{clickhere}}' => CHtml::link(Yii::t('main', 'click here'), Yii::app()->createAbsoluteUrl('/activation/'.$user->id.'/'.$hash));
)); ?>
<br /><br />
<?php echo Yii::t('main', 'Thank you.'); ?><br /><br />
<?php echo Yii::t('main', 'P.S. If you did not request this email, you may safely ignore it.'); ?>