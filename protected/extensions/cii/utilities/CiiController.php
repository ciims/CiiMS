<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class CiiController extends CController
{
	/**
	 * Default filter prevents dynamic pages (pagination, etc...) from being cached
	 */
	public function filters()
    {
        return array(
            array(
                'CHttpCacheFilter',
                'cacheControl'=>'public, no-store, no-cache, must-revalidate',
            ),
        );
    }
    
	public function beforeAction($action)
	{
	    header('Content-type: text/html; charset=utf-8');
		$theme = Cii::get(Configuration::model()->findByAttributes(array('key'=>'theme')), 'value', 'default');
		Yii::app()->setTheme(file_exists(dirname(__FILE__).'/../../themes/'.$theme) ? $theme : 'default');
		return true;
	}
	
	/**
	 * @var array the default params for any request
	 * 
	 */
	public $params = array(
		'meta'=>array(
			'keywords'=>'',
			'description'=>'',
		),
		'data'=>array(
			'extract'=>''
		)
	);
	
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/blog';
	
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();
	
    /**
     * Retrieves keywords for use in the viewfile
     */
    public function getKeywords()
    {
        $keywords = Cii::get($this->params['meta'], 'keywords', '');
        if (Cii::get($keywords, 'value', false) != false)
            $keywords = implode(',', json_decode($keywords['value']));
            
        return $keywords == "" ? Cii::get($this->params['data'], 'title', Yii::app()->name): $keywords;
    }
	
	/**
	 * Sets the layout for the view
	 * @param $layout - Layout
	 * @action - Sets the layout
	 **/
	protected function setLayout($layout)
	{
		$this->layout = $layout;
	}
	
	/**
	 * Overloaded Render allows us to generate dynamic content
	 **/
	public function render($view,$data=null,$return=false)
	{
	    if($this->beforeRender($view))
	    {
	    	if (isset($data['meta']))
	    		$this->params['meta'] = $data['meta'];
	    	
	    	if (isset($data['data']) && is_object($data['data']))
	    		$this->params['data'] = $data['data']->attributes;
	    	
    		$output=$this->renderPartial($view,$data,true);
            
    		if(($layoutFile=$this->getLayoutFile($this->layout))!==false)
    		    $output=$this->renderFile($layoutFile,array('content'=>$output, 'meta'=>isset($data['meta']) ? $this->params['meta'] : ''),true);
    
    		$this->afterRender($view,$output);
            
    		$output=$this->processOutput($output);
            $config = Yii::app()->getComponents(false);
            if (isset($config['clientScript']->compressHTML) && $config['clientScript']->compressHTML == true)
            {
                Yii::import('ext.contentCompactor.*');
                $compactor = new ContentCompactor();
                
                if($compactor == null)
                    throw new CHttpException(500, Yii::t('messages', 'Missing component ContentCompactor in configuration.'));
             
                $output = $compactor->compact($output, array());
            }
    		
    		if($return)
    		    return $output;
    		else
    		    echo $output;
	    }
	}

    /**
     * Retrieves all categories to display int he footer
     * @return array $items     The CMenu Items we are going to return
     */
    public function getCategories()
    {
        $items = array(array('label' => 'All Posts', 'url' => $this->createUrl('/blogs')));
        $categories = Yii::app()->cache->get('categories-listing');
        if ($categories == false)
        {
            $categories = Yii::app()->db->createCommand('SELECT categories.id AS id, categories.name AS name, categories.slug AS slug, COUNT(DISTINCT(content.id)) AS content_count FROM categories LEFT JOIN content ON categories.id = content.category_id WHERE content.type_id = 2 AND content.status = 1 GROUP BY categories.id')->queryAll();
            Yii::app()->cache->set('categories-listing', $categories);                          
        }
        
        foreach ($categories as $k=>$v)
        {
            if ($v['name'] != 'Uncategorized')
                $items[] = array('label' => $v['name'], 'url' => $this->createUrl('/' . $v['slug']));
        }
        
        return $items;
    }
    
    /**
     * Retrieves the recent post items so that the view is cleaned up
     * @return array $items     The CMenu items we are going to return
     */
    public function getRecentPosts()
    {
        $items = array();
        $content = Yii::app()->cache->get('content-listing');
        if ($content == false)
        {
            $content = Yii::app()->db->createCommand('SELECT title, extract, content.slug AS content_slug, categories.slug AS category_slug, categories.name AS category_name, comment_count, content.created FROM content LEFT JOIN categories ON content.category_id = categories.id WHERE vid = (SELECT MAX(vid) FROM content AS content2 WHERE content2.id = content.id) AND type_id = 2 AND status = 1 ORDER BY content.created DESC LIMIT 5')->queryAll();
            Yii::app()->cache->set('content-listing', $content);                            
        }
        
        foreach ($content as $k=>$v)
            $items[] = array('label' => $v['title'], 'url' => $this->createUrl('/' . $v['content_slug']));
        
        return $items;
    }
}
