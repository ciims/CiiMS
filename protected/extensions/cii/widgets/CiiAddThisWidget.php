<?php

/**
 * CiiAddThisWidget
 * Automatically loads and injects AddThis into the DOM so that the Theme doesn't have to explicity define it
 */
class CiiAddThisWidget extends CWidget
{
	public function init()
	{
		if(Cii::getConfig('addThisPublisherID') == '')
			return;

		$cs = Yii::app()->clientScript;
		$cs->registerScriptFile('https://s7.addthis.com/js/300/addthis_widget.js#pubid=' . Cii::getConfig('addThisPublisherID'));
		$cs->registerScript('CiiAddThisWidget',"
			addthis.layers({
			    'theme' : 'light',
			    'share' : {
			      'position' : 'right',
			      'numPreferredServices' : 5,
			      'services' : 'facebook,twitter,linkedin,google_plusone_share,more'
			    },
			    'visible' : 'smart'
			  });
		");
	}
}