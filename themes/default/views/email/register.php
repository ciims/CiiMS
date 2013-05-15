<?php echo $user->displayName; ?>,<br /><br />
Thanks for registering your account at CiiMS Blog <?php echo Yii::app()->name; ?>. To verify your account, <?php echo CHtml::link('click here', Yii::app()->createAbsoluteUrl('/activation/'.$user->id.'/'.$hash)); ?>. This action will verify your email address and allow you to interact with features only available to registered users.
<br /><br />
Thank you,<br />
<?php echo Yii::app()->name . " Team"; ?>
<br /><br />
P.S. If you did not request this email, you may safely ignore it.