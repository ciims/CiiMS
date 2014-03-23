<?php $this->widget('application.modules.dashboard.components.ContentListView.ContentListView', array(
	'htmlOptions' => array(
		'class' => 'settings-container', 
	),
    'id' => 'ajaxListView',
    'dataProvider' => $model->search(),
    'template' => '{items}',
    'preview' => isset($preview) ? $preview : NULL,
    'summaryText' => false,
    'itemView' => 'post',
    'sorterHeader' => '',
    'itemsCssClass' => 'posts nano',
    'pagerCssClass' => 'pagination',
    'pager' => array('class'=>'cii.widgets.CiiPager'),
    'sorterCssClass' => 'sorter',
    'beforeAjaxUpdate' => 'function() {
    	CiiDashboard.Content.Preview.beforeAjaxUpdate();
    }',
    'afterAjaxUpdate' => 'function() { 
    	CiiDashboard.Content.Preview.afterAjaxUpdate();
	}',
    'sortableAttributes' => array(
        'title',
        'author_id',
        //'like_count',    // Same issue as comment_count
        //'comment_count', // Until I can re-order CActiveDataProvidor on the fly, we can't order byu this correctly
        'category_id',
        'status',
        'created',
        'updated',
    )    
));
?>

<div class="body-content preview"></div>

<?php Yii::app()->getClientScript()->registerCssFile($this->asset.'/highlight.js/default.css')
					->registerCssFile($this->asset.'/highlight.js/github.css')
					->registerScriptFile($this->asset.'/js/marked.js', CClientScript::POS_END)
					->registerScriptFile($this->asset.'/highlight.js/highlight.pack.js', CClientScript::POS_END)
                    ->registerScriptFile($this->asset.'/js/md5.js', CClientScript::POS_END); ?>

<?php $this->widget('ext.timeago.JTimeAgo', array(
    'selector' => '.timeago',
)); ?>
