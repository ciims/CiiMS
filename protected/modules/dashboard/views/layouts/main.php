<!DOCTYPE html>
<html lang="<?php echo Yii::app()->language; ?>">
	<head>
		<meta name="viewport" content="initial-scale=1.0">
	    <meta charset="UTF-8" />
	    <title>CiiMS Dashboard | <?php echo CHtml::encode($this->pageTitle); ?></title>
	    <?php $bootstrap=Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('application.extensions.bootstrap.assets'), true, -1, YII_DEBUG); ?>
	    <?php Yii::app()->getClientScript()->registerMetaTag('text/html; charset=UTF-8', 'Content-Type', 'Content-Type', array(), 'Content-Type')
					    			  ->registerCssFile('https://fonts.googleapis.com/css?family=PT+Sans:400,700')
				                      ->registerCssFile('https://fonts.googleapis.com/css?family=Open+Sans:400,600,800')
				                      ->registerCssFile($bootstrap .'/css/bootstrap.min.css')
				                      ->registerCssFile($this->asset .'/font-awesome/css/font-awesome.css')
				                      ->registerCssFile($this->asset .'/css/main.css')
				                      ->registerCoreScript('jquery')
				                      ->registerScriptFile($this->asset.'/js/jquery-ui.min.js', CClientScript::POS_HEAD); ?>
	</head>
	<body>
		<header>
			<div class="pull-left navigation">
				<?php $this->widget('zii.widgets.CMenu', array(
					'items' => array(
						array('label' => 'Dashboard', 'url' => $this->createUrl('/dashboard'), 'active' => $this->id == 'default'),
						array('label' => 'Content', 'url' => $this->createUrl('/dashboard/content'), 'active' => $this->id == 'content'),
						array('label' => 'Settings', 'url' => $this->createUrl('/dashboard/settings'), 'active' => !in_array($this->id, array('default', 'content'))),
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