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
		<div class="clearfix"></div>
	</div>
	<div id="main" class="nano">
		<div class="content">
			<fieldset>
				<span style="padding:10px"></span>
				<legend>
					<?php echo Yii::t('Dashboard.main', 'Invited Users'); ?>

					<div class="pull-right">
						<?php echo CHtml::link(NULL, $this->createUrl('/dashboard/users/create'), array('id' => 'header-button', 'class' => 'icon-plus pure-plus pure-button pure-button-link pure-button-success pure-button-small'), NULL); ?>
					</div>
				</legend>
				<div class="clearfix"></div>
				<?php $this->widget('zii.widgets.CListView', array(
				    'dataProvider'=>$invitees,
				    'itemView'=>'userList',
				    'id' => 'inviteesListView',
				    'summaryText' => false,
				    'pagerCssClass' => 'pagination',
		    		'pager' => array('class'=>'bootstrap.widgets.TbPager'),
				)); ?>

				<div class="clearfix"></div>
				<span style="padding:20px"></span>

				<legend><?php echo Yii::t('Dashboard.main', 'Users'); ?></legend>

				<span style="padding:10px"></span>
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