<?php $cs = Yii::app()->getClientScript(); ?>
<div class="dashboard">
	<div class="header">
		<div class="content">
			<div class="welcome">
				<?php echo Yii::t('Dashboard.views', '{{welcome}}, {{user}}', array(
					'{{welcome}}' => CHtml::tag('strong', array('class' => 'greeting'), Yii::t('Dashboard.views', 'Welcome back')),
					'{{user}}' => Yii::app()->user->displayName
				)); ?>
			</div>
			<div class="header-nav">
				<?php echo CHtml::link('<span class="icon-plus"></span> Add Card', '#', array('id' => 'add-card')); ?>
				<?php echo CHtml::link('<span class="icon-pencil"></span> New Post', $this->createUrl('/dashboard/content/save')); ?>
			</div>
		</div>
	</div>
	<div class="clearfix push-header"></div>

	<div class="widget-selector dh-settings-container hidden">
		<div class="sidebar">
			<div id="main" class="nano">
				<div class="content">
					<?php $this->widget('zii.widgets.CMenu', array(
						'htmlOptions' => array('class' => 'menu'),
						'items' => $cards['available_cards']
					)); ?>
				</div>
			</div>
		</div>
		<div class="body-content">
			<div id="main" class="nano">
				<div class="content">
					<!-- Display Nothing Here by Default -->
				</div>
			</div>
		</div>
	</div>

	<div class="widget-container"></div>
	<div class="shader"></div>
</div>

<span id="early-greeting" style="display:none">
	<?php echo Yii::t('Dashboard.views', '{{welcome}}', array(
		'{{welcome}}' => CHtml::tag('strong', array('class' => 'greeting'), Yii::t('Dashboard.views', "Mornin' Sunshine!"))
	)); ?>
</span>
<span id="morning-greeting" style="display:none">
	<?php echo Yii::t('Dashboard.views', '{{welcome}}, {{user}}', array(
		'{{welcome}}' => CHtml::tag('strong', array('class' => 'greeting'), Yii::t('Dashboard.views', "Good morning")),
		'{{user}}' => Yii::app()->user->displayName
	)); ?>
</span>
<span id="afternoon-greeting" style="display:none">
	<?php echo Yii::t('Dashboard.views', '{{welcome}}, {{user}}', array(
		'{{welcome}}' => CHtml::tag('strong', array('class' => 'greeting'), Yii::t('Dashboard.views', "Good afternoon")),
		'{{user}}' => Yii::app()->user->displayName
	)); ?>
</span>
<span id="evening-greeting" style="display:none">
	<?php echo Yii::t('Dashboard.views', '{{welcome}}, {{user}}', array(
		'{{welcome}}' => CHtml::tag('strong', array('class' => 'greeting'), Yii::t('Dashboard.views', "Good evening")),
		'{{user}}' => Yii::app()->user->displayName
	)); ?>
</span>
<span id="late-greeting" style="display:none">
	<?php echo Yii::t('Dashboard.views', '{{welcome}} {{user}}?', array(
		'{{welcome}}' => CHtml::tag('strong', array('class' => 'greeting'), Yii::t('Dashboard.views', "Working late tonght")),
		'{{user}}' => Yii::app()->user->displayName
	)); ?>
</span>
<span id="midnight-greeting" style="display:none">
	<?php echo Yii::t('Dashboard.views', '{{welcome}}', array(
		'{{welcome}}' => CHtml::tag('strong', array('class' => 'greeting'), Yii::t('Dashboard.views', "Burnin' the midnight oil huh?"))
	)); ?>
</span>

<?php $asset = Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('application.extensions.cii.assets'), true, -1, YII_DEBUG); ?>
<?php $cs->registerScriptFile($this->asset.'/shapeshift/core/vendor/jquery.touch-punch.min.js', CClientScript::POS_END)
		 ->registerCssFile($this->asset.'/css/image-picker.css')
		 ->registerCssFile($asset.'/css/pure.css')
		 ->registerScriptFile($this->asset.'/js/image-picker.min.js', CClientScript::POS_END)
		 ->registerScriptFile($this->asset.'/shapeshift/core/jquery.shapeshift.js', CClientScript::POS_END)
		 ->registerScriptFile($this->asset.'/js/jquery.flippy.min.js', CClientScript::POS_END)
		 ->registerScriptFile($this->asset.'/js/jquery.nanoscroller.min.js', CClientScript::POS_END); ?>