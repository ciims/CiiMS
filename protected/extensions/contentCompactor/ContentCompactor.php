<?php 
/**
 * ContentCompactor class file.
 *
 * @author Martin Nilsson <martin.nilsson@haxtech.se>
 * @link http://www.haxtech.se
 * @copyright Copyright 2010 Haxtech
 * @license BSD
 */
 
class ContentCompactor {

	public $options = array();
	
	public function init() {
		Yii::import('ext.contentCompactor.Compactor');
	}
	
	public function compact($output, $options = null) {
		$options = $options ? array_merge($this->options, $options) : $this->options;
		$compactor = new Compactor($options);
		return $compactor->squeeze($output);
	}

}