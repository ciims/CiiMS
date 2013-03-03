<div id="posts">
    <?php foreach($data as $content): ?>
    	<?php $this->renderPartial('_post', array('content' => $content)); ?>
    <?php endforeach; ?>
</div>
<?php $this->widget('ext.yiinfinite-scroll.YiinfiniteScroller', array(
    'url'=>'blog',
    'contentSelector' => '#posts',
    'itemSelector' => 'div.post',
    'loadingText' => 'Loading...',
    'donetext' => '&nbsp;',
    'pages' => $pages,
)); ?>
<?php Yii::app()->clientScript->registerScript('unbind-infinite-scroll', "$(window).unbind('.infscr');"); ?>
