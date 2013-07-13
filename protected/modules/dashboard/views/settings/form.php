<?php $this->widget('application.modules.dashboard.components.CiiSettingsForm', array('model' => $model, 'header' => $header)); ?>
<?php $asset=Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('application.modules.dashboard.assets'), true, -1, YII_DEBUG); ?>
<?php Yii::app()->getClientScript()->registerScriptFile($asset.'/js/jquery.nanoscroller.min.js', CClientScript::POS_END); ?>
<?php Yii::app()->getClientScript()->registerScript('nano-scroller', '
	$("#main.nano").nanoScroller();
'); ?>