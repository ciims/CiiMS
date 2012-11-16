<?php
/**
 * @class EPiwikAnalyticsWidget
 * @about This class provides a functionality to display the Piwik Tracking code through the Widget
 * 
 * @author Charles R. Portwood II <charlesportwoodii@ethreal.net>
 *
 * @license BSD
 * @created 07.18.2012
 **/

class EPiwikAnalyticsWidget extends CWidget
{
	/**
	 * @var string baseUrl
	 * The base url where the piwik server is located
	 **/
	public $baseUrl = '';
	
	/**
	 * @var int id
	 * The Piwik tracking code id
	 **/
	public $id = -1;
	
	/**
	 * @method run
	 **/
	public function run()
	{
		if ($this->baseUrl == '')
			throw new CException('BaseURL for EPiwikAnalyticsWidget is not set.');
		
		if ($this->id == -1)
			throw new CException('Piwik ID not set.');
		
		Yii::app()->clientScript->registerScriptFile($this->baseUrl.'/piwik.js')
								->registerScript('PiwikAnalytics',
			"try {var piwikTracker = Piwik.getTracker(\"{$this->baseUrl}\" + \"/piwik.php\", {$this->id});piwikTracker.trackPageView();piwikTracker.enableLinkTracking();} catch( err ) {}"			
		);
		echo "<noscript><img src='{$this->baseUrl}/piwik.php?idsite=2' style='border:0'  /></noscript>";
	}
}

?>
