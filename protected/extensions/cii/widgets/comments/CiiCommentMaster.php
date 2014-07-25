<?php

/**
 * Class wrapper for displaying the correct comment container type
 */
class CiiCommentMaster extends CWidget
{
	/**
	 * The content passed from CiiController
	 * @var Content|null
	 */
	public $content;

	/**
	 * The type of comments we want to render
	 * @var string
	 */
	public $type = 'CiiMSComments';

	/**
	 * Renders the appropriate comment container with the content provided from the controller
	 */
	public function init()
	{
		Yii::app()->controller->widget('ext.cii.widgets.comments.' . $this->type, array('content' => $this->content));
	}
}