<form id="contact" method="GET" action="<?php echo Yii::app()->createUrl('/search'); ?>">
	<center>
		<?php 
			echo CHtml::textField('q', Cii::get($_GET, 'q', ''), array('style'=>'width: 400px; float:none;', 'placeholder'=>'What are you looking for?', ));
			echo CHtml::submitButton('Search', array('class'=>'btn btn-primary', 'style'=>'float:none; margin-top: -9px; margin-left: 15px;')); 
		?>
	</center>
</form>

<div class="three-fourth">		
	<?php if (isset($_GET['q'])): ?>
		<?php if ($itemCount == 0): ?>
			<h2>No Results Found</h2>
			<p>Sorry, we tried looking but we didn't find a match for the specified criteria. Try refining your search.</p>
		<?php endif; ?>
	<?php endif; ?>
	
	<div id="posts">
	    <?php foreach ($data as $k=>$v): ?>
	        <div class="post">
                <h1><?php echo CHtml::link($v->title, Yii::app()->createUrl($v->slug)); ?></h1>
                <div class="blog-data">
                    <?php echo $v->created == $v->updated ? 'Posted' : 'Updated'; ?>
                    <strong><?php echo date('F jS, Y @ H:i', strtotime($v->created)); ?></strong>
                    by <strong><?php echo $v->author->displayName; ?></strong> 
                    in <?php echo CHtml::link($v->category->name, Yii::app()->createUrl($v->category->slug)); ?> - 
                    <?php if ($v->commentable): ?>
                        <span class="label label-info"><?php echo $v->comment_count; ?> Comments</span>
                    <?php endif; ?>
                </div>
                <p><?php echo strip_tags($v->extract, '<p><br>'); ?></p>
                <?php echo CHtml::link('Read More', Yii::app()->createUrl($v->slug), array('class'=>'btn btn-inverse', 'style'=>'float:right')); ?>
                <br />
            </div>
        <?php endforeach; ?>
	</div>
	
    <br /><br />
   <?php $this->widget('ext.yiinfinite-scroll.YiinfiniteScroller', array(
    'url'=>'search',
    'param'=>array(
        'getParam'=>'q',
        'param'     => Cii::get($_GET, 'q', '')
    ),
    'contentSelector' => '#posts',
    'itemSelector' => 'div.post',
    'loadingText' => 'Loading...',
    'donetext' => 'There are no more posts in this category',
    'pages' => $pages,
)); ?>
	<META NAME="robots" CONTENT="noindex,nofollow">
</div>
