<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class CiiController extends CController
{
	
	public function beforeAction($action)
	{
		Yii::app()->setTheme($this->displayVar(Configuration::model()->findByAttributes(array('key'=>'theme'))->value, 'default'));
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
	    	{
	    		$this->params['meta'] = $data['meta'];
	    	}
	    	
	    	if (isset($data['data']) && is_object($data['data']))
	    	{
	    		$this->params['data'] = $data['data']->attributes;
	    	}
	    	
		$output=$this->renderPartial($view,$data,true);
		if(($layoutFile=$this->getLayoutFile($this->layout))!==false)
		    $output=$this->renderFile($layoutFile,array('content'=>$output, 'meta'=>isset($data['meta']) ? $this->params['meta'] : ''),true);

		$this->afterRender($view,$output);

		$output=$this->processOutput($output);

		if($return)
		    return $output;
		else
		    echo $output;
	    }
	}

	/**
	 * Performs default isset()/empty() type checking on an object, as well as
	 * addition methods if requested
	 * @param	mixed	$var	The variable we want to do data checking on
	 * @param 	string	$default	The default value we want to return if false
	 * @param  	$mode 	array 	The method(s) we would like to apply to the variable
	 * @return 	$var	mixed	The variable depending upon the mode setting
	 */
	public function displayVar($var, $default = NULL)
	{
		if (is_array($var))
			return is_array($var) && !empty($var) ? $var : $default;
		else
			return isset($var) ? $var : $default;
		
	}
	
	/**
	 * Outputs readable debug information at request
	 * @param $array - Data to be outputted
	 * @action - Outputs readable debug info
	 **/
	public function debug($array)
	{
		if (!YII_DEBUG)
			return;
		
		echo '<pre class="cii-debug">';
		print_r($array);
		echo '</pre>';
	}
}
