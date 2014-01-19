<h3>
	<?php echo Yii::t('Install.main', '{woah} An error occured!', array(
		'{woah}' => CHtml::tag('span', array('class' => 'highlight'), 'Woah!')
	)); ?>
</h3>
<hr />
<p>
	<?php echo Yii::t('Install.main', 'CiiMS has encountered an error during the installation that requires manual intervention.'); ?>
</p>

<?php if (isset($error)): ?>
	<h4><?php echo Yii::t('Install.main', 'The following error was produced during the installation.'); ?></h4>
	<pre><?php echo $error['message']; ?></pre>
<?php endif; ?>

<ul>
    <li><?php echo Yii::t('Install.main', 'Press the "back" button in your browser to go back to the previous page and try whatever action you last performed again.'); ?></li>
    <li><?php echo Yii::t('Install.main', 'Press the button below and let CiiMS figure out what you need to do next.'); ?></li>
    <li><?php echo Yii::t('Install.main', 'If you still get stuck, write down everything you did and submit an issue at {{github}}.', array(
    		'{{github}}' => CHtml::link(Yii::t('Install.main', 'github'), 'https://github.com/charlesportwoodii/CiiMS/issues')
    )); ?></li>
</ul>