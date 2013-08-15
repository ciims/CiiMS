<div class="posts-container">
	<?php Yii::setPathOfAlias('bootstrap', Yii::getPathOfAlias('ext.bootstrap')); ?>
	<?php Yii::import('application.modules.dashboard.components.ContentListView.ContentListView'); ?>
	<?php $this->widget('ContentListView', array(
		'id' => 'ajaxListView',
	    'dataProvider' => $model->search(),
	    'preview' => isset($preview) ? $preview : NULL,
	    'summaryText' => false,
	    'itemView' => 'post',
	    'sorterHeader' => '<div class="content">' . CHtml::tag('strong', array(), Yii::t('Dashboard.views', 'Manage Content')),
	    'itemsCssClass' => 'posts nano',
	    'pagerCssClass' => 'pagination',
	    'pager' => array('class'=>'bootstrap.widgets.TbPager'),
	    'sorterCssClass' => 'sorter',
	    'beforeAjaxUpdate' => 'js:function() {
	    	CiiDashboard.Content.futurePerspective.beforeAjaxUpdate();
	    }',
	    'afterAjaxUpdate' => 'js:function() { 
	    	CiiDashboard.Content.futurePerspective.afterAjaxUpdate();
		}',
	    'sortableAttributes' => array(
	        'title',
	        'author_id',
	        'like_count',
	        //'comment_count', // Until I can re-order CActiveDataProvidor on the fly, we can't order byu this correctly
	        'category_id',
	        'status',
	        'created',
	        'updated',
	    )    
	));
	?>
	<div class="clearfix"></div>
</div>
<?php echo CHtml::tag('span', array('style' => 'display: none', 'id' => 'currentPerspective', 'value' => Yii::app()->session['admin_perspective']), NULL); ?>

<?php Yii::app()->getClientScript()->registerCssFile($this->asset.'/highlight.js/default.css')
					->registerCssFile($this->asset.'/highlight.js/github.css')
					->registerScriptFile($this->asset.'/js/marked.js', CClientScript::POS_END)
					->registerScriptFile($this->asset.'/highlight.js/highlight.pack.js', CClientScript::POS_END); ?>
<?php $this->widget('ext.timeago.JTimeAgo', array(
    'selector' => '.timeago',
)); ?>