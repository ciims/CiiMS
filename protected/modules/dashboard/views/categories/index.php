<?php Yii::setPathOfAlias('bootstrap', Yii::getPathOfAlias('ext.bootstrap')); ?>
<div class="form">
	<div class="header">
		<div class="pull-left">
			<p><?php echo Yii::t('Dashboard.views', 'Manage Categories'); ?></p>
		</div>
		<form class="pure-form pull-right header-form">
			<span class="icon-search pull-right icon-legend"></span>
			<?php echo CHtml::textField(
	    		'Categories[name]', 
	    		Cii::get(Cii::get($_GET, 'Categories', array()), 'name'), 
	    		array(
	    			'id' => 'Categories_name', 
	    			'name' => 'Categories[name]',
	    			'class' => 'pull-right pure-input pure-search',
	    			'placeholder' => Yii::t('Dashboard.views', 'Filter by Name')
				)
	    	); ?>
	    </form>
		<div class="clearfix"></div>
	</div>
	<div id="main" class="nano">
		<div class="content">
			<fieldset>
				<!-- <legend>Pending Invitations</legend> -->
				<span style="padding:10px"></span>
				<legend>Categories

				<div class="pull-right">
					<?php echo CHtml::link(NULL, $this->createUrl('/dashboard/categories/save'), array('id' => 'header-button', 'class' => 'icon-plus pure-plus pure-button pure-button-link pure-button-primary pure-button-small'), NULL); ?>
				</div>
				</legend>
				<?php $this->widget('zii.widgets.CListView', array(
				    'dataProvider'=>$model->search(),
				    'itemView'=>'categoryList',
				    'id' => 'ajaxListView',
				    'summaryText' => false,
				    'pagerCssClass' => 'pagination',
		    		'pager' => array('class'=>'bootstrap.widgets.TbPager'),
				)); ?>
			<fieldset>
		</div>
	</div>
</div>

<?php $asset = Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('application.extensions.cii.assets'), true, -1, YII_DEBUG); ?>
<?php $cs = Yii::app()->getClientScript(); ?>
<?php $cs->registerCssFile($asset.'/css/pure.css');  ?>
<?php $cs->registerCssFile($asset.'/prism/prism-light.css');  ?>
<?php $cs->registerScriptFile($asset.'/prism/prism.js', CClientScript::POS_END); ?>