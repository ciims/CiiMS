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
        Comments.more();
	}',
    'sortableAttributes' => array(
        'title',
        'author_id',
        'category_id',
        'status',
        'created',
        'updated',
    )    
));
?>

<div class="body-content preview"></div>

<?php $asset = Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('ext.cii.assets.js'), true, -1, YII_DEBUG); ?>
<?php Yii::app()->getClientScript()->registerCssFile($this->asset.'/highlight.js/default.css')
					->registerCssFile($this->asset.'/highlight.js/github.css')
					->registerScriptFile($asset.'/marked.js', CClientScript::POS_END)
					->registerScriptFile($this->asset.'/highlight.js/highlight.pack.js', CClientScript::POS_END)
                    ->registerScriptFile($this->asset.'/js/md5.js', CClientScript::POS_END);
$this->widget('ext.timeago.JTimeAgo', array(
    'selector' => '.timeago',
)); ?>