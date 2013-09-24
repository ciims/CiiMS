<?php echo Yii::t('Dashboard.email', 'Hello {{user}},', array('{{user}}' => Yii::app()->user->displayName)); ?>

<?php echo Yii::t('Dashboard.email', 'This a security notification from CiiMS. A request has been made on your behalf to change the email address associated to your account.'); ?>
<?php echo Yii::t('Dashboard.email', 'If you requested this change, please visit {{link}} to verify your new email address. Your email address will not be changed until you complete the verification process.', array('{{link}}' =>  Yii::app()->createAbsoluteUrl('/emailchange/' . $key))); ?>
<?php echo Yii::t('Dashboard.email', 'If you did NOT request this change, you may safely ignore this email. This request will automatically expired in three (3) days.'); ?>