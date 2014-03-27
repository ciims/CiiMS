<?php

/**
 * @class CiiCommentWidget
 * @description Renders the appropriate comment container for the commenting system in use
 *
 * @usage Yii::app()->controller->widget('ext.cii.widgets.CiiCommentWidget');
 */
class CiiCommentWidget extends CWidget
{
	public function init()
	{
		echo CHtml::openTag('div', array('class' => 'comments', 'id' => 'comment'));
			if (Cii::getConfig('useDisqusComments'))
				echo CHtml::tag('div', array('id' => 'disqus_thread'), NULL);
			else if (Cii::getConfig('useDiscourseComments'))
				echo CHtml::tag('div', array('id' => 'discourse-comments'), NULL);
			else
				echo CHtml::tag('div', array('id' => 'ciims_comments'), NULL);
		echo CHtml::closeTag('div');
	}
}