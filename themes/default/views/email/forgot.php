<?php echo $user->displayName; ?>,<br /><br />
You recently notified us that you forgot your password for CiiMS Blog <?php echo Yii::app()->name; ?>. To reset your password, <?php echo CHtml::link('click here', Yii::app()->createAbsoluteUrl('/forgot/' . $hash)); ?> and follow the instructions on the reset page. This link is only valid for 15 minutes.
<br /><br />
Thank you,<br />
<?php echo Yii::app()->name . " Team"; ?>
<br /><br />
P.S. If you did not request this email, you may safely ignore it.