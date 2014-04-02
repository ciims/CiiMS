<?php $isIE = preg_match('/(?i)msie [1-8]/',$_SERVER['HTTP_USER_AGENT']); ?>
<?php if(!$isIE) echo "<!DOCTYPE html>"; ?>
<html lang="<?php echo Yii::app()->language; ?>" class="<?php echo $isIE ? 'ie' : NULL ?>">
	<head>
		<meta name="viewport" content="initial-scale=1.0">
	    <meta charset="UTF-8" />
	    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
		<link rel="icon" href="/favicon.ico" type="image/x-icon">
	    <title><?php echo Yii::t('Dashboard.views', 'CiiMS Dashboard | {{pagetitle}}', array('{{pagetitle}}' => CHtml::encode($this->pageTitle))); ?></title>
	    <?php Yii::app()->getClientScript()->registerMetaTag('text/html; charset=UTF-8', 'Content-Type', 'Content-Type', array(), 'Content-Type')
	    								->registerCssFile($this->asset . '/css/bootstrap.min.css')
				                      ->registerCssFile($this->asset . '/css/pure.css')
				                      ->registerCssFile($this->asset . (YII_DEBUG ? '/css/dashboard.css' : '/css/dashboard.min.css'))
				                      ->registerCssFile($this->asset . (YII_DEBUG ? '/font-awesome/css/font-awesome.css' : '/font-awesome/css/font-awesome.min.css'))
				                      ->registerCssFile($this->asset .'/font-mfizz/font-mfizz.css')
				                      ->registerCoreScript('jquery')
				                      ->registerScriptFile($this->asset.'/js/jquery-ui.min.js', CClientScript::POS_HEAD)
				                      ->registerScriptFile($this->asset.'/date.format/date.format.js', CClientScript::POS_HEAD)
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
							array('label' => CHtml::tag('i', array('class' => 'fa fa-th-large'), NULL) . CHtml::tag('span', array(), Yii::t('Dashboard.views', 'Dashboard')), 'url' => $this->createUrl('/dashboard'), 'active' => $this->id == 'default'),
							array('label' => CHtml::tag('i', array('class' => 'fa fa-pencil'), NULL) . CHtml::tag('span', array(), Yii::t('Dashboard.views', 'Content')), 'url' => $this->createUrl('/dashboard/content'), 'active' => $this->id == 'content'),
							array('label' => CHtml::tag('i', array('class' => 'fa fa-cogs'), NULL) . CHtml::tag('span', array(), Yii::t('Dashboard.views', 'Settings')), 'url' => $this->createUrl('/dashboard/settings'), 'active' => !in_array($this->id, array('default', 'content'))),
						)
					)); ?>
				</nav>
				<footer>
					<section>
						<span class="fa fa-align-justify icon-align-justify"></span>
						<?php echo CHtml::link(CHtml::tag('span', array('class' => 'fa fa-power-off'), NULL), $this->createUrl('/logout')); ?>
					</section>
				</footer>
			</aside>
				
			<main class="tc-container">
				<?php echo $content; ?>
			</main>

			<div class="clearfix"></div>
			<footer>
			</footer>
			<?php echo CHtml::tag('span', array('style' => 'display:none', 'value' => $this->createUrl('/dashboard'), 'id' => 'dashboard-endpoint'), NULL); ?>
			<?php echo CHtml::tag('span', array('style' => 'display:none', 'data-attr-endpoint' => $this->createAbsoluteUrl('/'), 'id' => 'endpoint'), NULL); ?>
			<?php Cii::loadUserInfo(); ?>
		</section>
	</body>
</html>