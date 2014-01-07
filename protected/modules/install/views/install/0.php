<h3>
	<?php echo Yii::t('Install.main', '{welcome} Let\'s Install CiiMS', array(
		'{welcome}' => Yii::tag('span', array('class' => 'highlight'), 'Welcome!')
	)); ?>
</h3>
<hr />
<p>
	<?php echo Yii::t('Install.main', '{thanks} This installer will walk you through the installation process for CiiMS, and should only take a few minutes to complete.', array(
		'{thanks}' => Yii::tag('strong', array(), Yii::tag('span', array('class' => 'highlight'), 'Thanks') . Yii::t('Install.main', ' for choosing CiiMS!'))
	)); ?>
</p>
<p>
	<?php echo Yii::t('Install.main', 'Before continuing, please make sure that you server can support running Yii Framework, and that the following directories are writable by your webserver. After you have verified the folders below are writable, press the "Next" button below.'); ?>

<pre><?php echo str_replace('/modules/install', '', dirname(__FILE__) . '/runtime/'); ?><br />
<?php echo str_replace('/modules/install', '', dirname(__FILE__) . '/config/'); ?><br />
<?php echo str_replace('/protected/modules/install', '', dirname(__FILE__) . '/assets/'); ?>
</pre>
</p>

<a href="?stage=1" class="pure-button pure-button-primary pure-button-small">Next <span class="fa fa-arrow-right"></span></a> 

<div class="clearfix"></div>
