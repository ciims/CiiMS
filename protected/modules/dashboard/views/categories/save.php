<?php $htmlOptions = array('class' => 'pure-input-2-3'); ?>
<?php $form = $this->beginWidget('cii.widgets.CiiActiveForm', array(
	'enableAjaxValidation'=>true,
    'htmlOptions' => array(
    	'class' => 'pure-form pure-form-aligned'
    )
)); ?>
<div class="header">
	<div class="pull-left">
		<h3><?php echo $model->isNewRecord ? 'Create a New Category' : 'Makes Changes to ' . $model->name; ?></h3>
		<p>Create or modify information for this category</p>
	</div>
	<div class="pull-right">
		<?php echo CHtml::submitButton('Save Changes', array('id' => 'header-button', 'class' => 'pure-button pure-button-primary pure-button-small pull-right')); ?>
	</div>
	<div class="clearfix"></div>
</div>
<div id="main" class="nano">
	<div class="content">
		<fieldset>
				<legend>Category Information</legend>
				<div class="pure-control-group">
					<?php echo $form->hiddenField($model, 'id'); ?>
				</div>
				<div class="pure-control-group">
					<?php echo $form->textFieldRow($model, 'name', $htmlOptions); ?>
				</div>
				<div class="pure-control-group">
					<?php echo $form->textFieldRow($model, 'slug', $htmlOptions); ?>
				</div>
				<div class="pure-control-group">
					<?php echo $form->dropDownListRow($model, 'parent_id', CHtml::listData(Categories::model()->findAll(), 'id', 'name'), $htmlOptions); ?>
				</div>

			<?php echo CHtml::submitButton('Save Changes', array('class' => 'pure-button pure-button-primary pure-button-small pull-right')); ?>
		<fieldset>
	</div>
</div>
<?php $this->endWidget(); ?>
<?php $asset=Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('application.modules.dashboard.assets'), true, -1, YII_DEBUG); ?>
<?php $cs = Yii::app()->getClientScript(); ?>
<?php $cs->registerScriptFile($asset.'/js/jquery.nanoscroller.min.js', CClientScript::POS_END); ?>
<?php $cs->registerScript('nano-scroller', '
		$("#main.nano").nanoScroller();
'); ?>
