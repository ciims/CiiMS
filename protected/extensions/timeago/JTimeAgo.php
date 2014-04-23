<?php
/**
 * User: yiqing
 * Date: 11-12-24
 * Time: 下午11:10
 * @see  https://github.com/rmm5t/jquery-timeago
 *.......................................................................................
 * @version 0.11.3   same as the timeago !
 * @requires yii 1.1.x
 * @author  yiqing
 * @license MIT License - http://www.opensource.org/licenses/mit-license.php
 *........................................................................................
 */
class JTimeAgo extends CWidget
{

//............<don't waste the widget id of yii generated ids>.....................................................................
    /**
     * @param bool $autoGenerate
     * @return string
     */
    public function getId($autoGenerate = true)
    {
        $id = parent::getId($autoGenerate);
        if ($this->startsWith($id, 'yw')) {
            return __CLASS__ . substr($id, 2);
        }
        return $id;
    }

    /**
     * @param string|null $h
     * @param string $n
     * @return bool
     * if $h  stars with $n
     *
     */
    protected function startsWith($h, $n)
    {
        return (false !== ($i = strrpos($h, $n)) &&
            $i === strlen($h) - strlen($n));
    }
//.................................................................................
    /**
     * @var CClientScript
     */
    protected $cs;

    /**
     * @var bool
     */
    public $debug = YII_DEBUG;


    /**
     * @var string
     */
    public $selector = '.timeago';


    /**
     * @var array
     */
    public $options = array();

    /**
     * @var array
     */
    public $settings = array();

    /**
     * @var array
     * canonical id to the corresponding js name ,the js file is under locales dir.
     * just used only when the file name is different from the canonical form
     * and you don't want modify the locale file manually.
     *  eg:   array('zh_cn'=>'zh-cn')
     *
     * ( In canonical form, a locale ID consists of only underscores and lower-case letters.)
     */
    public $localeIdMap = array();

    /**
     * @var bool
     */
    public $useLocale = true ;

    /**
     * @var string
     */
    protected $baseUrl ;

    /**
     * @var string
     */
    protected $assetsPath ;

    /**
     *
     */
    public function init()
    {
        //  most of places use it so make it as instance variable and for intelligence tips from IDE
        $this->cs = Yii::app()->getClientScript();
        parent::init();
    }

    /**
     * @return void
     */
    public function  run()
    {
        $this->publishAssets()
            ->registerClientScripts();
    }

    /**
     * @return JTimeAgo
     */
    public function publishAssets()
    {
        $this->assetsPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';
        $this->baseUrl = Yii::app()->getAssetManager()->publish($this->assetsPath, false, -1, $this->debug);
        return $this;
    }

    /**
     * @return JTimeAgo
     */
    public function registerClientScripts()
    {
        //> .register js file;
        if (YII_DEBUG)
            $this->cs->registerCoreScript('jquery')->registerScriptFile($this->baseUrl . '/jquery.timeago.js', CCLientScript::POS_END);
        else
            $this->cs->registerCoreScript('jquery')->registerScriptFile($this->baseUrl . '/jquery.timeago.min.js', CCLientScript::POS_END);

        if($this->useLocale == true) $this->handleLocale(true);

        if (empty($this->selector)) {
            // manually use it ?
              return  $this;
        }

        $jsCode = '';
        //> handle some settings
        if (!empty($this->settings)) {
            $settings = CJavaScript::encode($this->settings);
            $jsCode .= <<<SETTINGS
   jQuery.timeago.settings = {$settings};
SETTINGS;
            $jsCode .= "\n";
        }

        $options = CJavaScript::encode($this->options);
        //>  the js code for setup
        $jsCode .= <<<SETUP
        jQuery("{$this->selector}").timeago({$options});
SETUP;

        //> register jsCode
        $this->cs->registerScript(__CLASS__ . '#' . $this->getId(), $jsCode, CClientScript::POS_READY);
        return $this;
    }


   /**
    * @param bool $registerLocaleJs
    * @return JTimeAgo
    */
    public function handleLocale($registerLocaleJs = true)
    {
        //  $localeId = Yii::app()->getLocale()->getCanonicalID($localeId) ;
        try {
        $localeId =  Yii::app()->getLocale()->getId();
        } catch (Exception $e) {
            $localeId = 'en_us';
        }

        if(isset($this->localeIdMap[$localeId])){
            $localeId = $this->localeIdMap[$localeId] ;
        }
        $localeJsPath = $this->assetsPath . DIRECTORY_SEPARATOR . 'locales' . DIRECTORY_SEPARATOR ."jquery.timeago.{$localeId}.js";
       // echo $localeJsPath ; die(__FILE__);
        if(! is_file($localeJsPath) ){
            /**
             * try  another locale form :
             */
            $localeId = str_replace('_','-',$localeId);
            $localeJsPath = $this->assetsPath . DIRECTORY_SEPARATOR . 'locales' . DIRECTORY_SEPARATOR ."jquery.timeago.{$localeId}.js";
            if(! is_file($localeJsPath)){
                return $this;
            }
        }
        if($registerLocaleJs == true){
            $localeJsUrl = $this->baseUrl.'/locales/'."jquery.timeago.{$localeId}.js";
            $this->cs->registerScriptFile($localeJsUrl);
        }else{
            $localeJsCode = file_get_contents($localeJsPath);
            $this->cs->registerScript(__CLASS__.'_locale_'.$this->getId(),$localeJsCode,CClientScript::POS_HEAD);
        }
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if(strcasecmp($name,'debugMode') == 0 ){
            //back compatible  with previous version
            $this->debug = $value ;

        }else  try {
            //shouldn't swallow the parent ' __set operation
            parent::__set($name, $value);
        } catch (Exception $e) {
            $this->options[$name] = $value;
        }
    }


    /**
     * @static
     * @param bool $hashByName
     * @return string
     * return the widget assetsUrl
     */
    public static function getAssetsUrl($hashByName = false)
    {
        // return CHtml::asset(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets', $hashByName);
        return Yii::app()->getAssetManager()->publish(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets', $hashByName, -1, YII_DEBUG);
    }

}

