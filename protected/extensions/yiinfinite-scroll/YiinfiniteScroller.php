<?php

/**
 * This extension uses the infinite scroll jQuery plugin, from
 * http://www.infinite-scroll.com/ to create an infinite scrolling pagination,
 * like in twitter.
 *
 * It uses javascript to load and parse the new pages, but gracefully degrade
 * in cases where javascript is disabled and the users will still be able to
 * access all the pages.
 *
 * @author davi_alexandre
 */
class YiinfiniteScroller extends CBasePager {

    public $contentSelector = '#content';

    private $_options = array(
        'loading' => array(
            'finished'      => null,
            'finishedMsg'   => null,
            'img'           => null,
            'msg'           => null,
            'msgText'       => null,
            'selector'      => null,
            'speed'         => 'fast',
            'start'         => null
        ),
        'pages'             => null,   
        'path'              => null,
        'callback'          => null,
        'path'              => null,
        'debug'             => null,
        'behavior'          => null,
        'nextSelector'      => 'div.infinite_navigation',
        'navSelector'       => 'div.infinite_navigation a:first',
        'contentSelector'   => null,
        'extraScrollPx'     => 150,
        'itemSelector'      => 'div.post',
        'animate'           => false,
        'pathParse'         => null,
        'dataType'          => 'html',
        'appendCallback'    => true,
        'bufferPx'          => '300',
        'errorCallback'     => null,
        'infid'             => null,
        'pixelsFromNavToBottom' => null,
    );

    private $_callback = null;

    public function init()
    {
        $this->getPages()->validateCurrentPage = false;
        $this->_options['loadingImg'] = Yii::app()->baseUrl.'/images/infinite-loading.gif';
        parent::init();
    }

    public function run()
    {
        $this->registerClientScript();
        $this->createInfiniteScrollScript();
        $this->renderNavigation();

        if($this->getPages()->getPageCount() > 0 && $this->theresNoMorePages())
            throw new CHttpException(404);
    }

    public function __get($name)
    {
        if(array_key_exists($name, $this->_options))
            return $this->_options[$name];

        return parent::__get($name);
    }

    public function __set($name, $value)
    {
        if(array_key_exists($name, $this->_options))
        {
            return $this->_options[$name] = $value;
        }

        return parent::__set($name, $value);
    }

    public function registerClientScript() {
        $url = CHtml::asset(Yii::getPathOfAlias('ext.yiinfinite-scroll.assets').'/jquery.infinitescroll.min.js');
        Yii::app()->clientScript->registerScriptFile($url);
    }

    private function createInfiniteScrollScript()
    {
        // Allow for callback function
        if ($this->_options['callback'] !== null)
        {
            $this->_callback = $this->_options['callback'];
            unset($this->_options['callback']);
        }

        Yii::app()->clientScript->registerScript(uniqid(), "$('{$this->contentSelector}').infinitescroll(".$this->buildInifiniteScrollOptions().");");
    }

    private function buildInifiniteScrollOptions()
    {
        $options = array_filter( $this->_options );
        $options = CJavaScript::encode($options);
        return $options;
    }

    private function renderNavigation()
    {
        $next_link = CHtml::link('<strong style="width: 93px;">Load More</strong>
			<span></span>',$this->createPageUrl($this->getCurrentPage()+1), array('id' => 'more', 'escape' => true));
		Yii::app()->clientScript->registerScript(uniqid() . 'bind-scroll', "$('#more').click(function(e) { e.preventDefault(); $(document).trigger('retrieve.infscr'); });");
        echo '<div class="infinite_navigation">'.$next_link.'</div>';
    }

    private function theresNoMorePages()
    {
        return $this->getPages()->getCurrentPage() >= $this->getPages()->getPageCount();
    }
    
    public function createPageUrl($id=1)
    {
        return $id;
    }
}