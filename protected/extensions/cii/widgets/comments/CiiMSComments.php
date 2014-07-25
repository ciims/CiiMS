<?php

class CiiMSComments extends CWidget
{
	/**
	 * Content::model attributes passed from the Controller
	 * @var array
	 */
	public $content;

	/**
	 * Init function to start the rendering process
	 */
	public function init()
	{
		$asset = Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('ext.cii.assets.dist'), true, -1, YII_DEBUG);

		// Register CSS and Scripts
		Yii::app()->clientScript->registerScriptFile($asset. (YII_DEBUG ? '/ciimscomments.js' : '/ciimscomments.min.js'), CClientScript::POS_END);
		Yii::app()->clientScript->registerCssFile($asset. (YII_DEBUG ? '/ciimscomments.css' : '/ciimscomments.min.css'));
		
		if ($this->content != false)
			$this->renderCommentBox();
		else
			$this->renderCommentCount();
	}

	/**
	 * Renders the Disqus Comment Box on the page
	 */
	private function renderCommentBox()
	{
		$link = CHtml::link('0', Yii::app()->createAbsoluteUrl($this->content['slug']) . '#comment', array('data-ciimscomments-identifier' => $this->content['id']));
		$id = $this->content['id'];

		Yii::app()->clientScript->registerScript('CiiMSComments', "
            $(document).ready(function() {
				// Load the Endpoint
				var endpoint = $('#endpoint').attr('data-attr-endpoint') + '/';

				// Update the comments div
	            $('.comment-count').attr('data-attr-id', '$id').addClass('registered').append('$link');

	            // Load the comments
	            Comments.load();
	            Comments.commentCount();
	        });
	        ");
	}

	/**
	 * Renders the Diqsus Comment count on the page
	 */
	private function renderCommentCount()
	{
		Yii::app()->clientScript->registerScript('CiiMSCommentCount', "
            $(document).ready(function() {
				var endpoint = $('#endpoint').attr('data-attr-endpoint') + '/';
				Comments.commentCount();
			});
		");
	}
}
