<!DOCTYPE html>
<html lang="en">
	<head>
		<meta name="viewport" content="initial-scale=1.0">
	    <meta charset="UTF-8" />
	    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
	    <?php $asset=Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('webroot.themes.default.assets'), true, -1, YII_DEBUG); ?>
	    <?php Yii::app()->clientScript->registerMetaTag('text/html; charset=UTF-8', 'Content-Type', 'Content-Type', array(), 'Content-Type')
                                      ->registerMetaTag($this->keywords, 'keywords', 'keywords', array(), 'keywords')
                                      ->registerMetaTag(strip_tags($this->params['data']['extract']), 'description', 'description', array(), 'description')
                                      ->registerCssFile($asset .'/css/main.css')
		                              ->registerCoreScript('jquery')
								      ->registerScriptFile($asset .'/js/script.js')
								      ->registerScript('load', '$(document).ready(function() { DefaultTheme.load(); });'); ?>
		<!--[if lt IE 9]>
            <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
	</head>
	<body>
		<header>
		    <div class="header-top-bar"></div>
		    <div class="row-fluid header-middle-bar">
			    	<?php $this->widget('bootstrap.widgets.TbNavbar', array(
						'brand' => Cii::getConfig('name', Yii::app()->name),
						'fixed' => false,
						'items' => array(
							array(
								'class' => 'bootstrap.widgets.TbMenu',
								'items' => $this->getCiiMenu()
							)
						)
					)); ?>
		    </div>		    
		</header>
		
		<main class="main">
		    <div class="container image-container">
		    	<div class="row-fluid image-viewport">
		    		<?php echo CHtml::image(Yii::app()->getBaseUrl(true) . Cii::getConfig('splash-logo', $asset.'/images/splash-logo.jpg', Yii::app()->theme->name .'_settings_')); ?>
		    	</div>
		   	</div>
		   	<div class="container main-container">
                <div class="row-fluid main-body">
                    <?php echo $content; ?>
                </div>
            </div>
		</main>
		
		<footer>
		    <div class="footer-top-block">
		        <div class="container"></div>
		    </div>
		    <div class="footer-main-block">
		        <div class="row-fluid">
		            <div class="inner-container">
                        <div class="span3 well" id="twitterFeed">
                        </div>
		                <div class="span3">
                            <h5>Categories</h5>
                            <?php $this->widget('bootstrap.widgets.TbMenu', array(
                                'items' => $this->getCategories()
                            )); ?>
                        </div>
                        <div class="span3">
                            <h5>Recent Posts</h5>
                            <?php $this->widget('bootstrap.widgets.TbMenu', array(
                                'items' => $this->getRecentPosts()
                            )); ?>
                        </div>
                        <div class="span3">
                            <h5>Search</h5>
                            <p>Looking for something on the blog?</p>
                            <?php echo CHtml::beginForm($this->createUrl('/search'), 'get', array('id' => 'search')); ?>
                                <div class="input-append">
                                    <?php echo CHtml::textField('q', Cii::get($_GET, 'q', ''), array('type' => 'text', 'style' => 'width: 75%', 'placeholder' => 'Search...')); ?>
                                </div>
                            <?php echo CHtml::endForm(); ?>
                        </div>
		            </div>
		        </div>
		    </div>
		    <div class="footer-bottom-block">
		        <div class="container">
                        <div class="pull-left">Copyright &copy <?php echo date('Y'); ?> <?php echo Cii::getConfig('name', Yii::app()->name); ?></div>
                        <div class="pull-right cii-menu"><?php $this->widget('cii.widgets.CiiMenu', array('items' => $this->getCiiMenu(), 'htmlOptions' => array('class' => 'footer-nav'))); ?></div>
		        </div>
		    </div>
		</footer>

		<span id="endpoint" data-attr-endpoint="<?php echo Yii::app()->getBaseUrl(true); ?>" style="display:none"></span>
	</body>
</html>
