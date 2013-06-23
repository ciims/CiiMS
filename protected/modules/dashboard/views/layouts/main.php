<!DOCTYPE html>
<html lang="<?php echo Yii::app()->language; ?>">
	<head>
		<meta name="viewport" content="initial-scale=1.0">
	    <meta charset="UTF-8" />
	    <title>CiiMS Dashboard | <?php echo CHtml::encode($this->pageTitle); ?></title>
	    <?php $asset=Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('application.modules.dashboard.assets'), true, -1, YII_DEBUG); ?>
	    <?php $bootstrap=Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('application.extensions.bootstrap.assets'), true, -1, YII_DEBUG); ?>
	    <?php Yii::app()->clientScript->registerMetaTag('text/html; charset=UTF-8', 'Content-Type', 'Content-Type', array(), 'Content-Type')
					    			  ->registerCssFile('https://fonts.googleapis.com/css?family=PT+Sans:400,700')
				                      ->registerCssFile('https://fonts.googleapis.com/css?family=Open+Sans:400,600,800')
				                      ->registerCssFile($bootstrap .'/css/bootstrap.min.css')
				                      ->registerCssFile($bootstrap .'/css/bootstrap-responsive.min.css')
				                      ->registerCssFile($asset .'/font-awesome/css/font-awesome.css')
				                      ->registerCssFile($asset .'/css/main.css')
				                      ->registerScriptFile($asset.'/js/jquery-2.0.0.min.js', CClientScript::POS_HEAD)
				                      ->registerScriptFile($asset.'/js/jquery-ui.min.js', CClientScript::POS_HEAD); ?>
	</head>
	<body>
		<header>
			<div class="pull-left navigation">
				<?php $this->widget('zii.widgets.CMenu', array(
					'items' => array(
						array('label' => 'Dashboard', 'url' => $this->createUrl('/dashboard')),
						array('label' => 'Content', 'url' => $this->createUrl('/dashboard/content')),
						array('label' => 'Categories', 'url' => $this->createUrl('/dashboard/categories')),
						array('label' => 'People', 'url' => $this->createUrl('/dashboard/people')),
						array('label' => 'Settings', 'url' => $this->createUrl('/dashboard/settings')),
					)
				)); ?>
			</div>
			<div class="pull-right user">
				<!-- TODO: Proper Link to... ??? -->
				<?php echo CHtml::link(Yii::app()->user->displayName); ?>
				<?php echo CHtml::image(Users::model()->findByPk(Yii::app()->user->id)->gravatarImage(), NULL, array('class' => 'user-image')); ?>
				<?php echo CHtml::tag('span', array('class' => 'options')); ?>
			</div>
			<div class="clearfix"></div>
		</header>
		<div class="clearfix"></div>
		<main>
			<?php echo $content; ?>
		</main>
		<footer>
		</footer>
	<body>
</html>