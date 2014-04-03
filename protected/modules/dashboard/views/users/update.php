<?php $htmlOptions = array('class' => 'pure-input-2-3'); ?>
<?php $form = $this->beginWidget('cii.widgets.CiiActiveForm', array(
	'enableAjaxValidation'=>true,
    'htmlOptions' => array(
    	'class' => 'pure-form pure-form-aligned'
    )
)); ?>
<div class="header">
	<div class="pull-left">
		<p><?php echo Yii::t('Dashboard.views', 'Change User Information'); ?></p>
	</div>
	<div class="pull-right">
		<?php echo CHtml::submitButton(Yii::t('Dashboard.views', 'Save Changes'), array('id' => 'header-button', 'class' => 'pure-button pure-button-primary pure-button-small pull-right')); ?>
	</div>
	<div class="clearfix"></div>
</div>

	<?php if (Yii::app()->user->hasFlash('error')): ?>
		<div class="alert-secondary alert in alert-block fade alert-error">
			<?php echo Yii::app()->user->getFlash('error'); ?>
			<a class="close" data-dismiss="alert">×</a>
		</div>
	<?php elseif (Yii::app()->user->hasFlash('success')): ?>
		<div class="alert-secondary alert in alert-block fade alert-success">
			<?php echo Yii::app()->user->getFlash('success'); ?>
			<a class="close" data-dismiss="alert">×</a>
		</div>
	<?php endif; ?>
<div id="main" class="nano">
	<div class="content">
		<fieldset>

				<legend><?php echo Yii::t('Dashboard.views', 'User Information'); ?></legend>
				<div class="pure-control-group">
					<?php echo $form->toggleButtonRow($model, 'status', $htmlOptions); ?>
				</div>
				<div class="pure-control-group">
					<?php echo $form->emailFieldRow($model, 'email', $htmlOptions); ?>
				</div>
				<div class="pure-control-group">
					<?php echo $form->textFieldRow($model, 'displayName', $htmlOptions); ?>
				</div>
				<div class="pure-control-group">
					<?php echo $form->dropDownListrow($model, 'user_role', CHtml::listData(UserRoles::model()->findAll(), 'id', 'name'), $htmlOptions); ?>
				</div>

				<legend>Optional Information</legend>
				<div class="pure-control-group">
					<?php echo $form->textFieldRow($model, 'firstName', $htmlOptions); ?>
				</div>
				<div class="pure-control-group">
					<?php echo $form->textFieldRow($model, 'lastName', $htmlOptions); ?>
				</div>
				<div class="pure-control-group">					
					<?php echo $form->textAreaRow($model, 'about', array('class' => 'pure-input-2-3', 'style' => 'height: 300px')); ?>
				</div>

				<legend>Password</legend>
				<div class="pure-control-group">
					<?php echo $form->passwordFieldRow($model, 'password', array('class' => 'pure-input-2-3', 'placeholder' => Yii::t('Dashboard.views', "Set or change a user's password. Otherwise leave blank"))); ?>
				</div>
				<legend>Metadata
					<span class="fa fa-plus meta-icon-plus pull-right icon-plus pure-button pure-button-link"></span>
				</legend>
				<div class="meta-container">
					<p class="small-text"><?php echo Yii::t('Dashboard.views', 'Do not alter this data unless you know what you are doing.'); ?></p>
					<div class="clearfix"></div>
					<?php foreach ($model->metadata as $meta): ?>
						<?php if (strpos($meta->key, 'api_key') !== false) continue; // Prevent API keys from being displayed or manipulated?>
						<?php $options = array('class' => 'pure-input-2-3', 'type' => 'text', 'value' => $meta->value, 'name' => 'UserMetadata[' . $meta->key . ']'); ?>

						<div class="pure-control-group">
							<!-- TODO: Find a way to hide dashboard items as they cause the dashboard to explode... -->
							<?php echo CHtml::tag('label', array(), CiiInflector::titleize($meta->key)); ?>

							<?php if (strpos($meta->key, 'Provider') !== false): ?>
								<?php $options['disabled'] = true; ?>
							<?php elseif ($meta->key == 'likes'): ?>
								<?php $options['disabled'] = true; ?>
								<?php $options['value'] = count(json_decode($meta->value, true)); ?>
							<?php elseif ($meta->key == 'dashboard'): ?>
								<?php $options['disabled'] = true; ?>
							<?php endif; ?>
							<?php echo CHtml::tag('input', $options, NULL); ?>
						</div>
					<?php endforeach; ?>
				</div>

			<?php echo CHtml::submitButton(Yii::t('Dashboard.views', 'Save Changes'), array('class' => 'pure-button pure-button-primary pure-button-small pull-right')); ?>
		<fieldset>
	</div>
</div>
<?php $this->endWidget(); ?>
<?php $cs = Yii::app()->getClientScript(); ?>
