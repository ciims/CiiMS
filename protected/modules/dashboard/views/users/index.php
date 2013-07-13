<div class="header">
	<div class="pull-left">
		<h3>Manage Users</h3>
		<p>Manage users, user metadata, and permissions</p>
	</div>
	<div class="pull-right">
		<?php echo CHtml::link(NULL, $this->createUrl('/dashboard/users/update'), array('id' => 'header-button', 'class' => 'icon-plus pure-plus pure-button pure-button-link pure-button-primary pure-button-small'), NULL); ?>
		<?php echo CHtml::link('Invite Users', $this->createUrl('/dashboard/users/update'), array('id' => 'header-button', 'class' => 'pure-button pure-button-link pure-button-primary pure-button-small')); ?>
	</div>
	<div class="clearfix"></div>
</div>
<div id="main">
	<div class="content">
		<fieldset>
			<legend>Pending Invitations</legend>

			<legend>Users</legend>
		<fieldset>
	</div>
</div>


<?php
	$asset = Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('application.extensions.cii.assets'), true, -1, YII_DEBUG);
	$cs = Yii::app()->getClientScript();

	$cs->registerCssFile($asset.'/css/pure.css'); 
	$cs->registerCssFile($asset.'/prism/prism-light.css'); 
	$cs->registerScriptFile($asset.'/prism/prism.js', CClientScript::POS_END);
?>
<?php $asset=Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('application.modules.dashboard.assets'), true, -1, YII_DEBUG); ?>
<?php Yii::app()->getClientScript()->registerScriptFile($asset.'/js/jquery.nanoscroller.min.js', CClientScript::POS_END); ?>
<?php Yii::app()->getClientScript()->registerScript('nano-scroller', '
	$(document).ready(function() {
		$("#main.nano").nanoScroller();
	});
'); ?>