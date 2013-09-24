<?php $content = &$data; ?>
<?php $meta = Content::model()->parseMeta($content->metadata); ?>

<div class="content" data-attr-id="<?php echo $content->id; ?>">
	<div class="post">
		<?php if (Cii::get(Cii::get($meta, 'blog-image', array()), 'value', '') != ""): ?>
			<p style="text-align:center;"><?php echo CHtml::image(Yii::app()->baseUrl . $meta['blog-image']['value'], NULL, array('class'=>'image')); ?></p>
		<?php endif; ?>
		<div class="post-inner">
			<div class="post-header">
				<h3 class="pull-left"><?php echo CHtml::link(CHtml::encode($content->title), Yii::app()->createUrl($content->slug)); ?></h3>
				<div class="likes-container likes-container--topfix pull-right">
					<div class="likes <?php echo Yii::app()->user->isGuest ?: (Users::model()->findByPk(Yii::app()->user->id)->likesPost($content->id) ? 'liked' : NULL); ?>">     
					    <a href="#" id="upvote" title="Like this post and discussion">
					    	<span class="icon-heart icon-red"></span>
					        <span class="counter">
					            <span id="like-count"><?php echo $content->like_count; ?></span>
					        </span>      
					    </a>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="blog-meta inline">
				<span class="date"><?php echo $content->getCreatedFormatted() ?></span>
				<span class="separator">⋅</span>
				<span class="blog-author minor-meta">
					<?php
						echo Yii::t('DefaultTheme', '{{by}} {{author}}', array(
							'{{by}}' => CHtml::tag('strong', array(), Yii::t('DefaultTheme', 'by')),
							'{{author}}' => CHtml::tag('span', array(), CHtml::link(CHtml::encode($content->author->displayName), $this->createUrl("/profile/{$content->author->id}/")))
						)); ?>
					<span class="separator">⋅</span> 
				</span> 
				<span class="minor-meta-wrap">
					<span class="blog-categories minor-meta"><strong>in </strong>
					<span>
						<?php echo CHtml::link(CHtml::encode($content->category->name), Yii::app()->createUrl($content->category->slug)); ?>
					</span> 
					<span class="separator">⋅</span> 
				</span> 					
				<span class="comment-container">
					<?php echo Yii::t('DefaultTheme', '{{count}} Comments', array('{{count}}' => $content->getCommentCount())); ?>			
				</span>
			</div>
			<div class="clearfix"></div>
				<?php
					$md = new CMarkdownParser;
					$dom = new DOMDocument();
					$dom->loadHtml('<?xml encoding="UTF-8">'.$md->safeTransform($content->content));
					$x = new DOMXPath($dom);

					foreach ($x->query('//a') as $node)
					{
						$element = $node->getAttribute('href');

						// Don't follow links outside of this site, and always open them in a new tab
						if ($element[0] !== "/")
						{
							$node->setAttribute('rel', 'nofollow');
							$node->setAttribute('target', '_blank');
						}
					}
				?>

				<div id="md-output"><?php echo $md->safeTransform($dom->saveHtml()); ?></div>
				<textarea id="markdown" style="display:none;"><?php echo $content->content; ?></textarea>
				
				
		</div>
	    <div style="clear:both;"><br /></div>
	</div>
</div>

<div class="comments">
	<?php $count = 0;?>
	<?php echo CHtml::link(NULL, NULL, array('name'=>'comments')); ?>
	<div class="post">
		<div class="post-inner">
			<div class="post-header post-header-comments">
				<h3 class="comment-count pull-left left-header"><?php echo Yii::t('DefaultTheme', '{{count}} Comments', array('{{count}}' => $comments)); ?></h3>
				
				<div class="likes-container pull-right">
					<div class="likes <?php echo Yii::app()->user->isGuest ?: (Users::model()->findByPk(Yii::app()->user->id)->likesPost($content->id) ? 'liked' : NULL); ?>">     
					    <a href="#" id="upvote">
					    	<span class="icon-heart icon-red"></span>
					        <span class="counter">
					            <span id="like-count"><?php echo $content->like_count; ?></span>
					        </span>      
					    </a>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
			<?php if (!Yii::app()->user->isGuest): ?>
				<?php if ($data->commentable): ?>
    				<a id="comment-box"></a>
    	                <div id="sharebox" class="comment-box">
    	                    <div id="a">
    	                        <div id="textbox" contenteditable="true"></div>
    	                        <div id="close"></div>
    	                        <div style="clear:both"></div>
    	                    </div>
    	                    <div id="b" style="color:#999"><?php echo Yii::t('DefaultTheme', 'Comment on this post'); ?></div> 
    	                </div>
    	                <?php $this->widget('bootstrap.widgets.TbButton', array(
    	                    'type' => 'success',
    	                    'label' => Yii::t('DefaultTheme', 'Submit'),
    	                    'url' => '#',
    	                    'htmlOptions' => array(
    	                        'id' => 'submit-comment',
    	                        'class' => 'sharebox-submit',
    	                        'style' => 'display:none; margin-bottom: 5px;'
    	                    )
    	                )); ?>
    	        <?php endif; ?>
            <?php else: ?>
				<div class="alert">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					<?php echo Yii::t('DefaultTheme', '{{heythere}} Before leaving a comment you must {{signup}} or {{register}}', array(
						'{{heythere}}' => CHtml::tag('strong', array(), Yii::t('DefaultTheme', 'Hey there!')),
						'{{signup}}' => CHtml::link(Yii::t('DefaultTheme', 'login'), $this->createUrl('/login')),
						'{{register}}' => CHtml::link(Yii::t('DefaultTheme', 'signup'), $this->createUrl('/register'))
					)); ?>
				</div>
        	<?php endif; ?>
            <div id="comment-container" style="display:none; margin-top: -1px;"></div>
            <div class="comment"></div>
            <div class="clearfix"></div>
		</div>
	</div>
</div>

<?php Yii::app()->getClientScript()
                ->registerCssFile($this->asset.'/highlight.js/default.css')
				->registerCssFile($this->asset.'/highlight.js/github.css')
				->registerScriptFile($this->asset.'/js/marked.js')
				->registerScriptFile($this->asset.'/highlight.js/highlight.pack.js')
				->registerScript('loadBlog', '$(document).ready(function() { DefaultTheme.loadBlog(' . $content->id . '); });');
$this->widget('ext.timeago.JTimeAgo', array(
    'selector' => ' .timeago',
));
?>
