<?php $cs = Yii::app()->clientScript; ?>
<!DOCTYPE html>
<html lang="<?php echo Yii::app()->language; ?>">
	<head>
		<title><?php echo CHtml::encode($this->pageTitle); ?></title>
		<meta name="viewport" content="initial-scale=1.0">
	    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
	    <link href='http://fonts.googleapis.com/css?family=Source+Sans+Pro' rel='stylesheet' type='text/css'>
		<?php $cs->registerMetaTag('text/html; charset=UTF-8', 'Content-Type', 'Content-Type', array(), 'Content-Type')
                 ->registerMetaTag($this->keywords, 'keywords', 'keywords', array(), 'keywords')
                 ->registerMetaTag(strip_tags($this->params['meta']['description']), 'description', 'description', array(), 'description')
                 ->registerCssFile($this->asset . (YII_DEBUG ? '/css/theme.css' : '/css/theme.min.css'))
				 ->registerCssFile($this->asset . '/font-awesome/css/font-awesome.min.css')
				 ->registerScriptFile($this->asset . '/js/jquery-2.0.3.min.js')
				 ->registerScriptFile($this->asset .(YII_DEBUG ? '/js/theme.js' : '/js/theme.min.js'))
				 ->registerScript('load', '$(document).ready(function() { Theme.load(); });', CClientScript::POS_END); ?>
	</head>
	<body>
		<div id="main-container">
			<header id="top-header">
				<div class="logo pull-left">
					<?php echo CHtml::link(CHtml::encode(Cii::getConfig('name')), Yii::app()->getBaseUrl(true)); ?>
				</div>
				<nav class="top-navigation pull-right">
					<ul>
						<li><?php echo CHtml::link(Yii::t('DefaultTheme.main', 'Home'), Yii::app()->getBaseUrl(true)); ?></li>
						<li><?php echo CHtml::link(Yii::t('DefaultTheme.main', 'Dashboard'), $this->createUrl('/dashboard')); ?></li>
						<li><?php echo CHtml::link(NULL, $this->createUrl('/search'), array('class' => 'fa fa-search')); ?></li>
					</ul>
				</nav>
				<div class="clearfix"></div>
			</header>
			<main class="pure-g-r">
				<?php echo $content; ?>
			</main>
			<div class="main-footer-container">
				<footer id="main-footer">
					<div class="copyright pull-left">
						Copyright &copy <?php echo date("Y"); ?> <?php echo CHtml::encode(Cii::getConfig('name')); ?>
					</div>
					<nav class="footer-nav pull-right">
						<ul>
							<li><?php echo CHtml::link(Yii::t('DefaultTheme.main', 'Home'), Yii::app()->getBaseUrl(true)); ?></li>
							<li><?php echo CHtml::link(Yii::t('DefaultTheme.main', 'Dashboard'), $this->createUrl('/dashboard')); ?></li>
						</ul>
					</nav>
					<div class="clearfix"></div>
				</footer>
			</div>
		</div>
	</body>
	<span id="endpoint" data-attr-endpoint="<?php echo Yii::app()->getBaseUrl(true); ?>" style="display:none"></span>
</html>