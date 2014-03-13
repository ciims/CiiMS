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
				<div class="alert-secondary alert in alert-block fade alert-error" style="display:none">
					<a class="close" data-dismiss="alert">×</a>
				</div>
				<span style="padding:10px"></span>
				<legend>
					<?php echo Yii::t('Dashboard.main', 'Invited Users'); ?>
				</legend>
	
				<div class="invite-field">
					<label><?php echo Yii::t('Dashboard.main', 'Email Address'); ?></label>
					<input type="email" name="Invite[email]" id="Invite_email" placeholder="<?php echo Yii::t('Dashboard.main', 'Enter a email address to invite a user'); ?>"/>
					<a class="pure-button pure-button-success pure-button-small invite-button">
						<span id="spinner">
							<span class="icon-spinner icon-spin icon-spinner-form"></span>
							<span class="icon-spacer"></span>
						</span>
						<?php echo Yii::t('Dashboard.main', 'Invite User'); ?>
					</a>
				</div>
				<div class="clearfix"></div>
				<?php $this->widget('zii.widgets.CListView', array(
				    'dataProvider'=>$invitees,
				    'itemView'=>'userList',
				    'id' => 'inviteesListView',
				    'summaryText' => false,
				    'pagerCssClass' => 'pagination',
		    		'pager' => array('class'=>'cii.widgets.CiiPager'),
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
		    		'pager' => array('class'=>'cii.widgets.CiiPager'),
				)); ?>
			</fieldset>
		</div>
	</div>
</div>

<?php $asset = Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('application.extensions.cii.assets'), true, -1, YII_DEBUG); ?>
<?php $cs = Yii::app()->getClientScript(); ?>
<?php $cs->registerCssFile($asset.'/css/pure.css');  ?>
<?php $cs->registerCssFile($asset.'/prism/prism-light.css');  ?>
<?php $cs->registerScriptFile($asset.'/prism/prism.js', CClientScript::POS_END); ?>
