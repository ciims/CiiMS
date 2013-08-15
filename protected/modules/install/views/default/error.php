<h4><?php echo $this->breadcrumbs[$this->stage]; ?></h4>
<p><?php echo Yii::t('Install.main', "Whoops! Whatever you did just broke the installer! I'm sure that this is just a temporary thing though. Why don't you go back to the previous page and try whatever you did again? Here's a couple ideas to help get you out of this:"); ?></p>
<ul>
    <li><?php echo Yii::t('Install.main', 'Press the "back" button in your browser to go back to the previous page and try whatever action you last performed again.'); ?></li>
    <li><?php echo Yii::t('Install.main', 'Press the button below and let CiiMS figure out what you need to do next.'); ?></li>
    <li><?php echo Yii::t('Install.main', 'If you still get stuck, write down everything you did and submit an issue at {{github}}.', array(
    		'{{github}}' => CHtml::link(Yii::t('Install.main', 'github'), 'https://github.com/charlesportwoodii/CiiMS/issues')
    )); ?></li>
</ul>
<div class="clearfix"></div>
<hr />

<div class="clearfix"></div>