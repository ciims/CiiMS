<!DOCTYPE html>
<html lang="en">
	<head>
	    <meta charset="UTF-8" />
	    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="keywords" content="<?php echo isset($this->params['meta']['keywords']) && !is_array(isset($this->params['meta']['keywords'])) ? $this->params['meta']['keywords'] : ''; ?>" />
		<meta name="description" content="<?php echo strip_tags($this->params['data']['extract']); ?>" />
		<?php Yii::app()->clientScript->registerCoreScript('jquery')
									  ->registerScriptFile(Yii::app()->baseUrl.'/js/jquery.gritter.js')
								      ->registerScriptFile(Yii::app()->baseUrl.'/js/default/script.js')
									  ->registerCssFile(Yii::app()->baseUrl.'/css/default/main.css')
									  ->registerCssFile(Yii::app()->baseUrl.'/css/jquery.gritter.css'); ?>


		<!--[if lt IE 9]>
                <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->


	</head>
	<body>
		<?php $this->widget('bootstrap.widgets.BootNavbar', array(
				    'fixed'=>false,
				    'brand'=>Yii::app()->name,
				    'brandUrl'=>Yii::app()->getBaseUrl(true),
				    'collapse'=>true, // requires bootstrap-responsive.css
				    'items'=>array(
				        array(
				            'class'=>'bootstrap.widgets.BootMenu',
				            'items'=>array(
				                 '---',
				                array('label'=>'Blog', 'url'=>Yii::app()->createUrl('/blog'), 'active'=>($this->id === 'content') ? true : false),
				                array('label'=>'Admin', 'url'=>Yii::app()->createUrl('/admin')),
				            ),
				        ),
				        '<form class="navbar-search pull-right" method="GET" action="' . Yii::app()->createUrl('/search'). '">' . CHtml::textField('q', isset($_GET['q']), array('placeholder'=>'Search', )) .'</form>',
				    ),
				)); ?>
		<div class="container">
  			<div class="row-fluid">
				<?php $this->widget('bootstrap.widgets.BootBreadcrumbs', array(
				    'links'=>$this->breadcrumbs
				)); ?>
				<?php echo $content; ?>
  			</div>
		</div>
		<?php if (!YII_DEBUG):
			if (Configuration::model()->findByAttributes(array('key'=>'piwikExtension','value'=>1))):
				$this->widget('ext.analytics.EPiwikAnalyticsWidget', 
					array(
						'id'=>Configuration::model()->findByAttributes(array('key'=>'piwikId'))->value, 
						'baseUrl'=>Configuration::model()->findByAttributes(array('key'=>'piwikBaseUrl'))->value
					)
				); 
			endif;
			
			if (Configuration::model()->findByAttributes(array('key'=>'gaExtension','value'=>1))):
				$this->widget('ext.analytics.EGoogleAnalyticsWidget', 
					array(
						'account'=>Configuration::model()->findByAttributes(array('key'=>'gaAccount'))->value, 
						'addThis'=>Configuration::model()->findByAttributes(array('key'=>'gaAddThis'))->value, 
						'addThisSocial'=>Configuration::model()->findByAttributes(array('key'=>'gaAddThisSocial'))->value
					)
				);
			endif; 
		endif; ?>
	</body>
</html>
