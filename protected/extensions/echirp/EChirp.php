<?php
/*
* Chirp Your Tweet (chirp.js) for Yii - Tweet on your website
* author : ary.wibowo@nucreativa.com
*/
class EChirp extends CWidget
{
	/*
	* @var options for chirp.js
	*/
	public $options = array();
	
	public function run()
	{
		$asset=Yii::app()->assetManager->publish(dirname(__FILE__).'/assets');
    	$cs=Yii::app()->clientScript;
    	$cs->registerScriptFile($asset.'/chirp.min.js');
        if ($this->options['user'] != "")
        {
            $user = $this->options['user'];
            echo "<script>Chirp({ user : '{$user}', max : 1, retweets : false, replies : false, cacheExpires : 1000 *60 * 15 })</script>";
        }
	}
}
?>