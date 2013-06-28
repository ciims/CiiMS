<div class="posts-container">
	<?php Yii::import('application.modules.dashboard.components.ContentListView.ContentListView'); ?>
	<?php $this->widget('ContentListView', array(
	    'dataProvider' => $model->search(),
	    'summaryText' => false,
	    'itemView' => 'post',
	    'sorterHeader' => '<div class="content"><strong>Manage Content</strong>',
	    'itemsCssClass' => 'posts nano',
	    'sorterCssClass' => 'sorter',
	    'afterAjaxUpdate' => 'js:function() { $(".nano").nanoScroller({ iOSNativeScrolling: true });}',
	    'sortableAttributes' => array(
	        'title',
	        'created',
	        'updated',
	        'status',
	    )    
	));
	?>
	<div class="clearfix"></div>
</div>
<?php $asset=Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('application.modules.dashboard.assets'), true, -1, YII_DEBUG); ?>
<?php Yii::app()->getClientScript()->registerScriptFile($asset.'/js/jquery.nanoscroller.min.js', CClientScript::POS_END); ?>
<?php Yii::app()->getClientScript()->registerScript('nano-scroller', '$(document).ready(function() { $(".nano").nanoScroller({ iOSNativeScrolling: true }); });'); ?>