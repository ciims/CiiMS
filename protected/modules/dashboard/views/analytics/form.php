<?php $htmlOptions = array('class' => 'pure-input-2-3'); ?>
<ul class="providers">
	<?php foreach ($model->groups() as $group=>$keys): ?>
		<li class="provider">
			<div class="tile" data-name="<?php echo str_replace(".", "_", str_replace(" ", "_", $group)); ?>">
				<span class="title">
					<img src="<?php echo $this->asset.'/images/providers/' . $group .'/logo.png'; ?>" />
				</span>
				<?php $first = reset($keys); ?>
				<?php echo $form->toggleButtonRow($model, $first, $htmlOptions); ?>
				<span class="help"><?php echo Yii::t('Dashboard.views', 'Click to view options'); ?></span>
			</div>
		</li>
	<?php endforeach; ?>
</ul>

<div class="transparent"></div>

<?php foreach ($model->groups() as $group=>$keys): ?>
	<div class="options-panel <?php echo str_replace(".", "_", str_replace(" ", "_", $group)); ?>">
		<legend><?php echo Yii::t('Dashboard.views', 'Attributes for {{group}}', array('{{group}}' => $group)); ?></legend>
		<?php foreach ($keys as $key): ?>
			<div class="pure-control-group">
				<?php $validators = $model->getValidators($key); ?>
				<?php if( strpos($key, 'enabled') !== false): ?>
					<?php continue; ?>
				<?php endif; ?>
				<?php if (isset($validators[0]) && get_class($validators[0]) == "CBooleanValidator"): ?>
					<?php echo $form->toggleButtonRowFix($model, $key, $htmlOptions); ?>
				<?php else: ?>
					<?php echo $form->textFieldRowLabelFix($model, $key, $htmlOptions); ?>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>
<?php endforeach; ?>

<?php Yii::app()->getClientScript()->registerCss('analytics-form', 'main .settings-container .body-content #main .content { padding: 0px; }'); ?>
