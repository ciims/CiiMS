<!DOCTYPE html>
<html lang="en">
	<head>
	    <meta charset="UTF-8" />
	    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
	    <?php Yii::app()->clientScript->registerMetaTag('text/html; charset=UTF-8', 'Content-Type', 'Content-Type', array(), 'Content-Type')
                                      ->registerMetaTag($this->keywords, 'keywords', 'keywords', array(), 'keywords')
                                      ->registerMetaTag(strip_tags($this->params['data']['extract']), 'description', 'description', array(), 'description'); ?>
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
		<?php $this->widget('bootstrap.widgets.TbNavbar', array(
				    'fixed'=>false,
				    'brand'=>Yii::app()->name,
				    'brandUrl'=>Yii::app()->getBaseUrl(true),
				    'collapse'=>true, // requires bootstrap-responsive.css
				    'items'=>array(
				        array(
				            'class'=>'bootstrap.widgets.TbMenu',
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
				<?php $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
				    'links'=>$this->breadcrumbs
				)); ?>
				<?php echo $content; ?>
  			</div>
		</div>
		<?php if (!YII_DEBUG):
			if (Cii::get(Configuration::model()->findByAttributes(array('key'=>'piwikExtension')), 'value', 0) == 1):
				$this->widget('ext.analytics.EPiwikAnalyticsWidget', 
					array(
						'id'=>Cii::get(Configuration::model()->findByAttributes(array('key'=>'piwikId')), 'value', NULL), 
						'baseUrl'=>Cii::get(Configuration::model()->findByAttributes(array('key'=>'piwikBaseUrl')), 'value', NULL)
					)
				); 
			endif;
			
			if (Cii::get(Configuration::model()->findByAttributes(array('key'=>'gaExtension')), 'value', 0) == 1):
				$this->widget('ext.analytics.EGoogleAnalyticsWidget', 
					array(
						'account'=>Cii::get(Configuration::model()->findByAttributes(array('key'=>'gaAccount')), 'value', NULL), 
						'addThis'=>Cii::get(Configuration::model()->findByAttributes(array('key'=>'gaAddThis')), 'value', NULL), 
						'addThisSocial'=>Cii::get(Configuration::model()->findByAttributes(array('key'=>'gaAddThisSocial')), 'value', NULL)
					)
				);
			endif; 
		endif; ?>
	</body>
</html>
