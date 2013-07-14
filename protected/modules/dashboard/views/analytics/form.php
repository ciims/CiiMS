<?php $asset=Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('application.modules.dashboard.assets'), true, -1, YII_DEBUG); ?>
<ul class="providers">
	<?php foreach ($model->groups() as $group=>$keys): ?>
		<li class="provider">
			<div class="tile" data-name="<?php echo $group; ?>">
				<span class="title">
					<?php echo CHtml::image($asset.'/images/providers/' . $group .'/logo.png'); ?>
				</span>
				<?php $first = reset($keys); ?>
				<?php echo $form->toggleButtonRow($model, $first); ?>
				<span class="help">Click to view options</span>
			</div>
		</li>
	<?php endforeach; ?>
</ul>

<?php Yii::app()->getClientScript()->registerCss('analytics-form', 'main .settings-container .body-content #main .content { padding: 0px; }'); ?>