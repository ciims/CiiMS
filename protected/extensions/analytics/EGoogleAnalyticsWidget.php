<?php
/**
 * @class EGoogleAnalyticsWidget
 * @about This class provides a build in method for displaying the Google Analytics widget with AddThis integration
 * 
 * @author Charles R. Portwood II <charlesportwoodii@ethreal.net>
 * @adaptedFrom EGoogleAnalyticsWidget <http://www.yiiframework.com/extension/google-analytics/>
 * 				by Vitaliy Stepanenko <mail@vitaliy.in>
 * 
 * @license BSD
 * @created 07.18.2012
 */
class EGoogleAnalyticsWidget extends CWidget
{
	/**
	 * @var string domainName
	 * The domain name we wish to track. Defaults to auto
	 */
	public $domainName = 'auto';
	
	/**
	 * @var string account
	 * The account ID provided by GA
	 */
	public $account = '';
	
	/**
	 * @var bool addThis
	 * AddThis is now a first class citizen in GA, so we can provide it to GA for tracking
	 */
	public $addThis = false;
	
	/**
	 * @var string/bool addThisSocial
	 * Whether or not AddThis should share social data with GA
	 */
	public $addThisSocial = 'false';
	
	/**
	 * @method run
	 */
    public function run()
    {
    	$gaq = "";
    	if ($this->addThis)
		{
			$at = "var addthis_config = {
				      data_ga_property: '{$this->account}',
				      data_ga_social: {$this->addThisSocial}
				   };";
		}

    	if (!empty($this->domainName))
    	{
    		$gaq = "_gaq.push(['_setDomainName','$this->domainName']);";
    	}
		

    	$gaq .= " setTimeout(\"_gaq.push(['_trackEvent', '15_seconds', 'read'])\",15000);";
    	$gaq .= " setTimeout(\"_gaq.push(['_trackEvent', '30_seconds', 'read'])\",30000);";
    	$gaq .= " setTimeout(\"_gaq.push(['_trackEvent', '60_seconds', 'read'])\",60000);";


        Yii::app()->clientScript->registerScript('GoogleAnalytics',
            "var _gaq = _gaq || [];_gaq.push(['_setAccount', '{$this->account}']);{$gaq}_gaq.push(['_trackPageview']);{$at}(function() {var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);})();"    
            ,CClientScript::POS_END
        );   
        
    }
}
?>