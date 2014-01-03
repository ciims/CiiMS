<h3>
	<?php echo Yii::t('Install.main', '{woah} An error occured!', array(
		'{woah}' => Yii::tag('span', array('class' => 'highlight'), 'Woah!')
	)); ?>
</h3>
<hr />
<p>
	<?php echo Yii::t('Install.main', 'CiiMS has encountered an error during the installation that requires manual intervention.'); ?>
</p>

<?php if (isset($e)): ?>
	<h4><?php echo Yii::t('Install.main', 'The following error was produced during the installation.'); ?></h4>
	<pre><?php echo $e->getMessage(); ?></pre>
<?php endif; ?>

<p>
    <?php echo Yii::t('Install.main', "Most likely the error above is a permission error. You can correct this by making the following {{assets}}, {{runtime}} and {{config}} directories writable.", array(
        '{{assets}}' => Yii::tag('strong', array(), 'assets'),
        '{{runtime}}' => Yii::tag('strong', array(), 'runtime'),
        '{{config}}' => Yii::tag('strong', array(), 'config')
    )); ?>
</p>
<pre>
chmod -R 777 <?php echo str_replace('/modules/install/views/install', '', dirname(__FILE__) . '/runtime/'); ?>

chmod -R 777 <?php echo str_replace('/modules/install/views/install', '', dirname(__FILE__) . '/config/'); ?>

chmod -R 777 <?php echo str_replace('/protected/modules/install/views/install', '', dirname(__FILE__) . '/assets/'); ?>
</pre>

<p><?php echo Yii::t('Install.main', 'When you have addressed the issue above, refresh the page to continue with the installation.'); ?></p>
                