<?php Yii::setPathOfAlias('bootstrap', Yii::getPathOfAlias('ext.bootstrap')); ?>
<div class="form">
	<div class="header">
		<div class="pull-left">
			<p><?php echo Yii::t('Dashboard.views', 'Manage Users'); ?></p>
		</div>
		<form class="pure-form pull-right header-form">
			<span class="icon-search pull-right icon-legend"></span>
			<?php echo CHtml::textField(
	    		'Users[displayName]', 
	    		Cii::get(Cii::get($_GET, 'Users', array()), 'displayName'), 
	    		array(
	    			'id' => 'Users_displayName', 
	    			'name' => 'Users[displayName]',
	    			'class' => 'pull-right pure-input pure-search',
	    			'placeholder' => Yii::t('Dashboard.views', 'Filter by Name')
				)
	    	); ?>
	    </form>
		<!--<div class="pull-right">
			<?php echo CHtml::link(NULL, $this->createUrl('/dashboard/users/create'), array('id' => 'header-button', 'class' => 'icon-plus pure-plus pure-button pure-button-link pure-button-primary pure-button-small'), NULL); ?>
			<?php echo CHtml::link(Yii::t('Dashboard.views', 'Invite Users'), $this->createUrl('/dashboard/users/create'), array('id' => 'header-button', 'class' => 'pure-button pure-button-link pure-button-primary pure-button-small')); ?>
		</div>
	-->
		<div class="clearfix"></div>
	</div>
	<div id="main" class="nano">
		<div class="content">
			<fieldset>
				<!-- <legend>Pending Invitations</legend> -->
				<span style="padding:10px"></span>
				<legend><?php echo Yii::t('Dashboard.main', 'Users'); ?></legend>
				<div class="clearfix"></div>
				<?php $this->widget('zii.widgets.CListView', array(
				    'dataProvider'=>$model->search(),
				    'itemView'=>'userList',
				    'id' => 'categoryListView',
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