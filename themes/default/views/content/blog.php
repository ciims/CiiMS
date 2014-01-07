<?php $content = &$data; ?>
<?php $meta = Content::model()->parseMeta($content->metadata); ?>

<div id="content" data-attr-id="<?php echo $content->id; ?>">
	<div class="post">
		<?php $this->renderPartial('//site/attached-content', array('meta' => Content::model()->parseMeta($content->metadata))); ?>

		<div class="post-inner">
			<div class="post-header">
				<h2><?php echo CHtml::link($content->title, Yii::app()->createUrl($content->slug)); ?></h2>
				<span class="author">
					<?php echo Yii::t('DefaultTheme', 'By:') . ' ' . CHtml::link(CHtml::encode($content->author->displayName), $this->createUrl("/profile/{$content->author->id}/")); ?> 
					<span class="pull-right">
						<?php echo CHtml::link(CHtml::encode($content->category->name), Yii::app()->createUrl($content->category->slug)); ?>
					</span>
				</span>
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

			<div class="post-details">
				<div class="icons">
					<span class="comment-container">
						<?php if (Cii::getConfig('useDisqusComments')): ?>
							<?php echo CHtml::link(0, Yii::app()->createUrl($content->slug) . '#disqus_thread'); ?>
						<?php else: ?>
                            <?php echo Chtml::link($content->getCommentCount(),Yii::app()->createUrl($content->slug) . '#comments'); ?>
						<?php endif; ?>				
					</span>
					<div class="likes-container">
						<div class="likes <?php echo Yii::app()->user->isGuest ?: (Users::model()->findByPk(Yii::app()->user->id)->likesPost($content->id) ? 'liked' : NULL); ?>">     
						    <a href="#" id="upvote">
							<span class="counter">
							    <span id="like-count"><?php echo $content->like_count; ?></span>
							</span>      
						    </a>
						</div>
					</div>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	    <div style="clear:both;"><br /></div>
	</div>
</div>
	
<div id="comments"></div>
<div class="comments <?php echo Cii::getConfig('useDisqusComments') ? 'disqus' : NULL; ?>">
	<?php if (Cii::getConfig('useDisqusComments')): ?>
		<?php $shortname = Cii::getConfig('disqus_shortname'); ?>
		<div class="post"><div class="post-inner" style="margin-top: 20px;"><div id="disqus_thread"></div></div></div>
        <?php Yii::app()->getClientScript()->registerScript('disqus-comments', "Theme.Blog.loadDisqus(\"{$shortname}\", \"{$content->id}\", \"{$content->title}\", \"{$content->slug}\");"); ?>
    <?php else: ?>
		<?php $count = 0;?>
		<?php echo CHtml::link(NULL, NULL, array('name'=>'comments')); ?>
		<div class="post">
			<div class="post-inner">
				<div class="post-header post-header-comments">
					<h3 class="comment-count pull-left left-header"><?php echo Yii::t('DefaultTheme', '{{count}} Comments', array('{{count}}' => $comments)); ?></h3>


                    <div class="post-details">
                        <div class="icons">
                            <div class="likes-container pull-right inner-likes-container">
                                <div class="likes <?php echo Yii::app()->user->isGuest ?: (Users::model()->findByPk(Yii::app()->user->id)->likesPost($content->id) ? 'liked' : NULL); ?>">     
                                    <a href="#" id="upvote">
                                    <span class="counter">
                                        <span id="like-count"><?php echo $content->like_count; ?></span>
                                    </span>      
                                    </a>
                                </div>
                            </div>
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
					<div style="clearfix"></div>
				    </div>
				    <div id="b"><?php echo Yii::t('DefaultTheme', 'Comment on this post'); ?></div> 
				</div>

				<a id="submit-comment" class="sharebox-submit pure-button pure-button-primary pure-button-xsmall pull-right" href="#">
					<i class="icon-spin icon-spinner" style="display:none;"></i>
					<?php echo Yii::t('DefaultTheme', 'Submit'); ?>
				</a>

			<?php endif; ?>
		    <?php else: ?>
					<div class="alert alert-warning">
						<?php echo Yii::t('DefaultTheme', '{{heythere}} Before leaving a comment you must {{signup}} or {{register}}', array(
							'{{heythere}}' => CHtml::tag('strong', array(), Yii::t('DefaultTheme', 'Hey there!')),
							'{{signup}}' => CHtml::link(Yii::t('DefaultTheme', 'login'), $this->createUrl('/login?next=' . $content->slug)),
							'{{register}}' => CHtml::link(Yii::t('DefaultTheme', 'signup'), $this->createUrl('/register'))
						)); ?>
					</div>
			<?php endif; ?>
		    <div id="comment-container" ></div>
		    <div class="comment"></div>
		    <div class="clearfix"></div>
		</div>
	</div>
	<?php endif; ?>
</div>

<?php Yii::app()->getClientScript()
                ->registerCssFile($this->asset.'/highlight.js/default.css')
				->registerCssFile($this->asset.'/highlight.js/github.css')
				->registerScriptFile($this->asset.'/js/marked.js')
				->registerScriptFile($this->asset.'/highlight.js/highlight.pack.js')
				->registerScript('loadBlog', '$(document).ready(function() { Theme.loadBlog(' . $content->id . '); });');
$this->widget('ext.timeago.JTimeAgo', array(
    'selector' => ' .timeago',
));
?>
