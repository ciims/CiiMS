<?php

/**
 * @class CiiDiscourseComments
 * Automatically displays and renders Discourse Comments in the view 
 */
class CiiDiscourseComments extends CWidget
{
	/**
	 * Content::model attributes passed from the Controller
	 * @var array
	 */
	public $content;

	/**
	 * The Discourse URL
	 * @var string
	 */
	private $_url = NULL;

	/**
	 * Init function to start the rendering process
	 */
	public function init()
	{
		$this->_url = Cii::getConfig('discourseUrl');
		$asset = Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('ext.cii.assets.dist'), true, -1, YII_DEBUG);
		Yii::app()->clientScript->registerScriptFile($asset. (YII_DEBUG ? '/ciidiscourse.js' : '/ciidiscourse.min.js'), CClientScript::POS_END);

		if ($this->content != false)
			$this->renderCommentBox();
		else
			$this->renderCommentCount();

		$this->linkComments();
	}

	/**
	 * Renders the Disqus Comment Box on the page
	 */
	private function renderCommentBox()
	{
		$link = CHtml::link('0', Yii::app()->createAbsoluteUrl($this->content['slug']) . '#comments', array('data-disqus-identifier' => $this->content['id']));
		Yii::app()->clientScript->registerScript('DisqusComments', "
			// Load the Endpoint
			var endpoint = $('#endpoint').attr('data-attr-endpoint') + '/';

			// Set the Disqus variables
			discourseUrl = \"{$this->_url}\" + '/';

            // Update the comment div
            $('#comment').addClass('discourse');
            $('.comment-count').addClass('registered').append('$link');

            // Load Disqus
            Comments.load();
            Comments.commentCount();
		");
	}

	/**
	 * Renders the Diqsus Comment count on the page
	 */
	private function renderCommentCount()
	{
		Yii::app()->clientScript->registerScript('DisqusCommentCount', "
			// Load the Endpoint
			var endpoint = $('#endpoint').attr('data-attr-endpoint') + '/';

			discourseUrl = \"{$this->_url}\";

			Comments.commentCount();
		");
	}
}