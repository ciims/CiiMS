<?php Yii::setPathOfAlias('bootstrap', Yii::getPathOfAlias('ext.bootstrap')); ?>
<div class="header">
	<div class="pull-left">
		<h3>Manage Users</h3>
		<p>Manage users, user metadata, and permissions</p>
	</div>
	<!--<div class="pull-right">
		<?php echo CHtml::link(NULL, $this->createUrl('/dashboard/users/create'), array('id' => 'header-button', 'class' => 'icon-plus pure-plus pure-button pure-button-link pure-button-primary pure-button-small'), NULL); ?>
		<?php echo CHtml::link('Invite Users', $this->createUrl('/dashboard/users/create'), array('id' => 'header-button', 'class' => 'pure-button pure-button-link pure-button-primary pure-button-small')); ?>
	</div>
-->
	<div class="clearfix"></div>
</div>
<div id="main" class="nano">
	<div class="content">
		<fieldset>
			<!-- <legend>Pending Invitations</legend> -->
			<span style="padding:10px"></span>
			<legend>Users
				<form class="pure-form pull-right">
					<span class="icon-search pull-right icon-legend"></span>
					<?php echo CHtml::textField(
			    		'Users[displayName]', 
			    		Cii::get(Cii::get($_GET, 'Users', array()), 'displayName'), 
			    		array(
			    			'id' => 'Users_displayName', 
			    			'name' => 'Users[displayName]',
			    			'class' => 'pull-right pure-input pure-search',
			    			'placeholder' => 'Filter by Name'
						)
			    	); ?>
			    </form>
			</legend>
			<?php $this->widget('zii.widgets.CListView', array(
			    'dataProvider'=>$model->search(),
			    'itemView'=>'userList',
			    'id' => 'ajaxListView',
			    'summaryText' => false,
			    'pagerCssClass' => 'pagination',
	    		'pager' => array('class'=>'bootstrap.widgets.TbPager'),
			)); ?>
		<fieldset>
	</div>
</div>

<?php $asset = Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('application.extensions.cii.assets'), true, -1, YII_DEBUG); ?>
<?php $cs = Yii::app()->getClientScript(); ?>
<?php $cs->registerCssFile($asset.'/css/pure.css');  ?>
<?php $cs->registerCssFile($asset.'/prism/prism-light.css');  ?>
<?php $cs->registerScriptFile($asset.'/prism/prism.js', CClientScript::POS_END); ?>

<?php $cs->registerScriptFile($this->asset.'/js/jquery.nanoscroller.min.js', CClientScript::POS_END); ?>
<?php $cs->registerScript('nano-scroller', '
	$(document).ready(function() {
		$("#main.nano").nanoScroller();
	});
'); ?>

<?php $cs->registerScript('search',
    "var ajaxUpdateTimeout;
    var ajaxRequest;
    $('input#Users_displayName').keyup(function(){
        ajaxRequest = $(this).serialize();
        clearTimeout(ajaxUpdateTimeout);
        ajaxUpdateTimeout = setTimeout(function () {
            $.fn.yiiListView.update(
                'ajaxListView',
                {data: ajaxRequest}
            )
        },
        300);
    });"
); ?>

<script type="text/javascript">
 $(".rounded-img, .rounded-img2").load(function() {
    $(this).wrap(function(){
      return '<span class="' + $(this).attr('class') + '" style="background:url(' + $(this).attr('src') + ') no-repeat center center; width: ' + $(this).width() + 'px; height: ' + $(this).height() + 'px;" />';
    });
    $(this).css("opacity","0");
  });
</script>