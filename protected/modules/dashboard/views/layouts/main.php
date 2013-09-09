<?php Yii::setPathOfAlias('bootstrap', Yii::getPathOfAlias('ext.bootstrap')); ?>
<!DOCTYPE html>
<html lang="<?php echo Yii::app()->language; ?>">
	<head>
		<meta name="viewport" content="initial-scale=1.0">
	    <meta charset="UTF-8" />
	    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
		<link rel="icon" href="/favicon.ico" type="image/x-icon">
	    <title><?php echo Yii::t('Dashboard.views', 'CiiMS Dashboard | {{pagetitle}}', array('{{pagetitle}}' => CHtml::encode($this->pageTitle))); ?></title>
	    <?php $bootstrap=Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('application.extensions.bootstrap.assets'), true, -1, YII_DEBUG); ?>
	    <?php Yii::app()->getClientScript()->registerMetaTag('text/html; charset=UTF-8', 'Content-Type', 'Content-Type', array(), 'Content-Type')
				                      ->registerCssFile($bootstrap .'/css/bootstrap.min.css')
				                      ->registerCssFile($this->asset . (YII_DEBUG ? '/css/dashboard.css' : '/css/dashboard.min.css'))
				                      ->registerCssFile($this->asset . (YII_DEBUG ? '/font-awesome/css/font-awesome.css' : '/font-awesome/css/font-awesome.min.css'))
				                      ->registerCssFile($this->asset .'/font-mfizz/font-mfizz.css')
				                      ->registerCoreScript('jquery')
				                      ->registerScriptFile($this->asset.'/js/jquery-ui.min.js', CClientScript::POS_HEAD)
				                      ->registerScriptFile($this->asset.'/js/jquery.nanoscroller.min.js', CClientScript::POS_END)
				                      ->registerScript('alert-close', '$(".close").click(function() { $(this).parent().fadeOut(); });')
				                      ->registerScript('align', '$(".icon-align-justify").click(function() { $("aside.navigation").toggleClass("active"); });'); ?>
	</head>
	<body>
		<section class="hbox">
			<aside class="navigation tc-container">
				<header>
					<?php echo CHtml::link(CHtml::image($this->asset .'/images/ciims.png') . CHtml::tag('span', array(), 'ciims'), Yii::app()->getBaseUrl(true)); ?>
					<?php echo CHtml::image(Users::model()->findByPk(Yii::app()->user->id)->gravatarImage(60), NULL, array('class' => 'user-image')); ?>
				</header>
				<nav>
					<?php $this->widget('zii.widgets.CMenu', array(
						'encodeLabel' => false,
						'items' => array(
							array('label' => CHtml::tag('i', array('class' => 'icon-th-large'), NULL) . CHtml::tag('span', array(), Yii::t('Dashboard.views', 'Dashboard')), 'url' => $this->createUrl('/dashboard'), 'active' => $this->id == 'default'),
							array('label' => CHtml::tag('i', array('class' => 'icon-pencil'), NULL) . CHtml::tag('span', array(), Yii::t('Dashboard.views', 'Content')), 'url' => $this->createUrl('/dashboard/content'), 'active' => $this->id == 'content'),
							array('label' => CHtml::tag('i', array('class' => 'icon-cogs'), NULL) . CHtml::tag('span', array(), Yii::t('Dashboard.views', 'Settings')), 'url' => $this->createUrl('/dashboard/settings'), 'active' => !in_array($this->id, array('default', 'content'))),
						)
					)); ?>
				</nav>
				<footer>
					<span class="icon-align-justify"></span>
					<?php echo CHtml::link(CHtml::tag('span', array('class' => 'icon-power-off'), NULL), $this->createUrl('/logout')); ?>
				</footer>
			</aside>
				
			<main class="tc-container">
				<?php echo $content; ?>
			</main>

			<div class="clearfix"></div>
			<footer>
			</footer>
			<?php echo CHtml::tag('span', array('style' => 'display:none', 'value' => $this->createUrl('/dashboard'), 'id' => 'dashboard-endpoint'), NULL); ?>
		</div>
	<body>
</html>