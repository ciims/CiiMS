<h3><?php echo Yii::t('Install.main', '{all} Done!', array('{all}' => CHtml::tag('span', array('class' => 'highlight'), Yii::t('Install.main', 'All')))); ?></h3>
<hr />
<p><?php echo Yii::t('Install.main', 'CiiMS has finished the installation, you are now ready to login. Click the login button below to login to your site.'); ?></p>

<p><?php echo Yii::t('Install.main', 'As a reminder, now would be a good time to secure your installation. Details on how to secure your installation can be found {{github}}. I hope you enjoy using CiiMS!', array(
	'{{github}}' => CHtml::link(Yii::t('Install.main', 'on github'), 'https://github.com/charlesportwoodii/CiiMS/wiki/Securing-CiiMS', array('target' => '_blank'))
)); ?></p>

<hr />
<?php echo CHtml::link(Yii::t('Install.main', 'Login'), $this->createUrl('/dashboard'), array('class' => 'pure-button pure-button-success')); ?>