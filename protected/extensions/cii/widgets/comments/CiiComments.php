<?php

class CiiComments extends CWidget
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
		$asset = Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('ext.cii.assets.js'), true, -1, YII_DEBUG);
		Yii::app()->clientScript->registerScriptFile($asset. '/ciimscomments.js');
		Yii::app()->clientScript->registerScriptFile($asset. '/date.format/date.format.js');
		Yii::app()->clientScript->registerScriptFile($asset. '/marked.js');
		Yii::app()->clientScript->registerScriptFile($asset. '/md5.js');

		$css = Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('ext.cii.assets.css'), true, -1, YII_DEBUG);
		Yii::app()->clientScript->registerCssFile($css. '/ciimscomments.css');
		
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
		$link = CHtml::link('0', Yii::app()->createAbsoluteUrl($this->content['slug']) . '#comments', array('data-ciimscomments-identifier' => $this->content['id']));
		$id = $this->content['id'];
		Yii::app()->clientScript->registerScript('CiiMSComments', "
			// Load the Endpoint
			var endpoint = $('#endpoint').attr('data-attr-endpoint') + '/';

			// Update the comments div
            $('.comment-count').attr('data-attr-id', '$id').addClass('registered').append('$link');

            // Load the comments
            CiiMSComments.load();
            CiiMSComments.commentCount();
        ");
	}

	/**
	 * Renders the Diqsus Comment count on the page
	 */
	private function renderCommentCount()
	{
		Yii::app()->clientScript->registerScript('CiiMSCommentCount', "
			// Load the Endpoint
			var endpoint = $('#endpoint').attr('data-attr-endpoint') + '/'

			CiiMSComments.commentCount();
		");
	}
}