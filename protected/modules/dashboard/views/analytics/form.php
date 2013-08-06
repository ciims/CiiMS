<?php $htmlOptions = array('class' => 'pure-input-2-3'); ?>
<ul class="providers">
	<?php foreach ($model->groups() as $group=>$keys): ?>
		<li class="provider">
			<div class="tile" data-name="<?php echo str_replace(".", "_", str_replace(" ", "_", $group)); ?>">
				<span class="title">
					<?php echo CHtml::image($this->asset.'/images/providers/' . $group .'/logo.png'); ?>
				</span>
				<?php $first = reset($keys); ?>
				<?php echo $form->toggleButtonRow($model, $first, $htmlOptions); ?>
				<span class="help">Click to view options</span>
			</div>
		</li>
	<?php endforeach; ?>
</ul>

<div class="transparent"></div>

<?php foreach ($model->groups() as $group=>$keys): ?>
	<div class="options-panel <?php echo str_replace(".", "_", str_replace(" ", "_", $group)); ?>">
		<legend>Attributes for <?php echo $group; ?></legend>
		<?php foreach ($keys as $key): ?>
			<div class="pure-control-group">
				<?php if (strpos($key, 'enabled') === false): ?>
					<?php echo $form->textFieldRowLabelFix($model, $key, $htmlOptions); ?>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
<?php endforeach; ?>

<?php Yii::app()->getClientScript()->registerCss('analytics-form', 'main .settings-container .body-content #main .content { padding: 0px; }'); ?>
