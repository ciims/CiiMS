<?php echo Yii::t('Dashboard.email', 'Hello,'); ?><br />
<?php echo Yii::t('Dashboard.email', 'An administrator at {{blog}} has invited you to collaborate at their site at {{site}}. To accept this invitation and to setup your account, click the following link {{link}}.', array(
	'{{blog}}' => Cii::getConfig('name'),
	'{{site}}' => Yii::app()->getBaseUrl(true),
	'{{link}}' => CHtml::link(Yii::app()->getBaseUrl(true) . '/acceptinvite/' . $hash, Yii::app()->getBaseUrl(true) . '/acceptinvite/' . $hash)
)); ?>
<br /><br />
<?php echo Yii::t('Dashboard.email', 'If you do not wish to accept this invite, you may safely disregard this email.'); ?>
<br /><br />
<?php echo Yii::t('Dashboard.email', 'Thank You,'); ?>
<br />
<?php echo Yii::t('Dashboard.email', '{{sitename}} Management', array('{{sitename}}' => Cii::getConfig('name'))); ?>
