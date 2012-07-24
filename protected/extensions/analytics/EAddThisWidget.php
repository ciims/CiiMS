<?php
/**
 * @class EAddThisWidget
 * @about This class provides functionality for display an AddThis Widget on your site
 * 
 * @author Charles R. Portwood II <charlesportwoodii@ethreal.net>
 * 
 * @license BSD
 * @created 07.18.2012
 */
class EAddThisWidget extends CWidget
{

	public $options = array(
		array('class'=>NULL),
		array('class'=>'addthis_button_preferred_1'),
		array('class'=>'addthis_button_preferred_2'),
		array('class'=>'addthis_button_preferred_3'),
		array('class'=>'addthis_button_preferred_4'),
		array('class'=>'addthis_button_compact'),
	);
	
	public $htmlOptions = array('class'=>'addthis_toolbox addthis_default_style addthis_32x32_style');
	
	public $account = '';
	
	public $trackAddress = false;
	/**
	 * @method run
	 */
    public function run()
    {
    	if ($this->account = '')
			throw new CException('Account is not provided');
		
		echo "<div class=\"" . $this->htmlOptions['class'] . "\"";
		foreach ($this->options as $option)
		{
			echo CHtml::link(NULL,NULL,$option);
		}
		echo "</div>";
		
		// Add the tracking url
		if ($this->trackAddress = true)
			Yii::app()->clientScript->registerScript('addThisTrackURL', 'var addthis_config = {"data_track_addressbar":true};');
		
		// Register the AddThis script
		Yii::app()->clientScript->registerScriptFile('http://s7.addthis.com/js/250/addthis_widget.js#pubid='.$this->account);
    }
}
?>