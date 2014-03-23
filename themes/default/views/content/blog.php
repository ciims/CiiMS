<?php $content = &$data; ?>
<?php $meta = Content::model()->parseMeta($content->metadata); ?>

<div id="content" data-attr-id="<?php echo $content->id; ?>">
	<div class="post">
		<?php $this->renderPartial('//site/attached-content', array('meta' => Content::model()->parseMeta($content->metadata))); ?>

		<div class="post-inner">
			<div class="post-header">
				<h2><?php echo CHtml::link($content->title, Yii::app()->createUrl($content->slug)); ?></h2>
				<span class="author">
					<?php echo Yii::t('DefaultTheme.main', 'By:') . ' ' . CHtml::link(CHtml::encode($content->author->displayName), $this->createUrl("/profile/{$content->author->id}/")); ?> &#183; <?php echo Cii::timeago($content->published); ?>
					<span class="pull-right">
						<?php echo CHtml::link(CHtml::encode($content->category->name), Yii::app()->createUrl($content->category->slug)); ?>
					</span>
				</span>

				<div id="md-output"><?php echo $content->safeOutput; ?></div>
				<textarea id="markdown" style="display:none;"><?php echo $content->content; ?></textarea>

			</div>

			<div class="post-details">
				<div class="icons">
					<div class="comment-container comment-count"></div>
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

<div class="post">
	<div class="post-inner" style="margin-top: 20px;">
		<?php $this->widget('ext.cii.widgets.CiiCommentWidget'); ?>
	</div>
</div>

<?php Yii::app()->getClientScript()
                ->registerCssFile($this->asset.'/highlight.js/default.css')
				->registerCssFile($this->asset.'/highlight.js/github.css')
				->registerScriptFile($this->asset.'/js/marked.js')
				->registerScriptFile($this->asset.'/highlight.js/highlight.pack.js')
				->registerScript('loadBlog', '$(document).ready(function() { Theme.loadBlog(' . $content->id . '); });'); ?>
