<?php $form = $this->beginWidget('ext.cii.widgets.CiiActiveForm', array(
	'action' => Yii::app()->createUrl('/dashboard/card/update/', array('id' => $model->id))
)); ?>
	<?php echo $form->textField($model, 'global_apikey', array('placeholder' => $model->getAttributeLabel('global_apikey'))); ?>
<?php $this->endWidget(); ?>

<?php $cs = Yii::app()->getClientScript(); ?>
<?php $assets = Yii::app()->assetManager->publish(YiiBase::getPathOfAlias($model->assetPath, true, -1, YII_DEBUG)); ?>
<?php $cs->registerCssFile($assets.'/css/card.css'); ?>
<?php $cs->registerScriptFile($assets.'/js/card.js'); ?>