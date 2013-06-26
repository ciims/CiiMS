<?php 
Yii::import('zii.widgets.CListView');
class ContentListView extends CListView {

	/**
	 * Renders the data item list.
	 */
	public function renderItems()
	{
		parent::renderItems();
		echo CHtml::tag('div', array('class' => 'preview'));
		echo CHtml::tag('div', array('class' => 'clearfix'));
		return;
	}
}