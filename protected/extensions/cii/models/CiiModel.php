<?php
/**
 * CiiModel is the base ActiveRecord class which all Models in Cii are derived from
 * @author Charles R. Portwood II <charlesportwoodii@ethreal.net>
 */
class CiiModel extends CActiveRecord
{
	// Attributes before they had changes
	public $_oldAttributes = array();

	/**
	 * @var array $forbiddenRoutes - an array or routes that the user should not be able to set the slug to
	 */
	public $forbiddenRoutes = array(
		'sitemap.xml',
		'search',
		'contact',
		'blog.rss',
		'blog',
		'activation',
		'forgot',
		'register',
		'register',
		'resetpassword',
		'profile',
		'login',
		'logout',
		'hybridauth',
		'dashboard',
		'acceptinvite',
		'api'
	);

	/**
	 * Adds the CTimestampBehavior to this class
	 * @return array
	 */
	public function behaviors()
	{
		return array(
			'CTimestampBehavior' => array(
				'class' 			=> 'zii.behaviors.CTimestampBehavior',
				'createAttribute' 	=> 'created',
				'updateAttribute' 	=> 'updated',
				'timestampExpression' => time(),
				'setUpdateOnCreate' => true
			)
		);
	}

    /**
     * Returns attributes suitable for the API
     * @return array
     */
    public function getAPIAttributes($params=array(), $relations = false)
    {
        $attributes = array();
        foreach ($this->attributes as $k=>$v)
        {
        	if (in_array($k, $params))
        		continue;

            $attributes[$k] = $v;
        }

        if ($relations != false)
        {
	        foreach ($relations as $relation=>$params)
	        {
	        	if (is_integer($relation))
	        	{
	        		$relation = $params;
	        		$params = array();
	        	}

	        	if (is_array($this->$relation))
	        	{
	        		$attributes[$relation] = array();
	        		foreach ($this->$relation as $k)
	        			$attributes[$relation][] = $k->getAPIAttributes($params);
	        	}
	        	else
	        	{
	        		if (isset($this->$relation))
	        			$attributes[$relation] = $this->$relation->getAPIAttributes($params);
	        		else
	        			$attributes[$relation] = array();
	        	}
	        }
        }

        return $attributes;
    }

	/**
	 * parseMeta pulls the metadata out of a model and returns that metadata as a usable array
	 * @param int  $id        The content ID to pull data from
	 * @return array $items - The metadata in array format
	 */
	public function parseMeta($id)
	{
		$items = array();
		$data = ContentMetadata::model()->findAllByAttributes(array('content_id' => $id));
		foreach ($data as $element)
			$items[$element->key] = $this->isJson($element->value) ? CJSON::decode($element->value) : $element->value;
		
		return $items;
	}

	/**
	 * Determines if a string is JSON or not
	 * @param  string $string  JSON string
	 * @return boolean 
	 */
	private function isJson($string)
	{
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}

	/**
	 * Enables us to cache old attributes for comparison
	 */
	protected function afterFind()
	{
	    $this->_oldAttributes = $this->attributes;
	    return parent::afterFind();
	}
    
	/**
	 * verifySlug - Verifies that the provided slug is able to be used and does not conflict with an existing route
	 * @param string $slug - the slug to be used
	 * @param string $title - the title to be used if no slug is provided
	 * @return string $slug - the slug to be used
	 */
	public function verifySlug($slug = '', $title = '')
	{
		$slug = str_replace('/', '-', str_replace('\'', '-', str_replace(' ', '-', $slug)));
		if ($slug == '')
			$slug = str_replace('/', '-', str_replace('\'', '-', str_replace(' ', '-', $title)));
		
		// Remove all of the extra junk characters that aren't valid urls
		$slug = preg_replace("/[^A-Za-z0-9 ]/", "-", $slug);

		// Allow the slug to be the root directory for setting the homepage
		if ($slug == '-')
			$slug = "/";
		
		return strToLower($this->checkSlug($slug));
	}
	
    /**
     * checkSlug - Recursive method to verify that the slug can be used
     * This method is purposfuly declared here to so that Content::findByPk is used instead of CiiModel::findByPk
     * @param string $slug - the slug to be checked
     * @param int $id - the numeric id to be appended to the slug if a conflict exists
     * @return string $slug - the final slug to be used
     */
    public function checkSlug($slug, $id=NULL)
    {
        // Find the number of items that have the same slug as this one
        $count = $this->countByAttributes(array('slug'=>$slug . $id));

        // If we found an item that matched, it's possible that it is the current item (or a previous version of it)
        // in which case we don't need to alter the slug
        if ($count >= 1)
        {
            // Pull the data that matches
            $data = $this->findByPk($this->id == NULL ? -1 : $this->id);
            
            // Check the pulled data id to the current item
            if ($data !== NULL && $data->id == $this->id)
                return $slug;
        }
        
        if ($count == 0 && !in_array($slug, $this->forbiddenRoutes))
            return $slug . $id;
        else
            return $this->checkSlug($slug, ($id == NULL ? 1 : ($id+1)));
    }

    /**
     * Model populate override
     * @param  array  $data $_POST data
     */
    public function populate($data=array())
    {
    	foreach ($data as $k=>$v)
    	{
    		// Promise that if this is a new record, we aren't handling any end-user data for $data
    		if ($this->isNewRecord)
    			$this->$k = $v;
    		else
    		{
	    		if(isset($this->attributes[$k]))
	    			$this->$k = $v;
	    	}
    	}

    	return $this->attributes;
    }

    /**
     * Instead of doing $model->find(), $model == NULL, $model = new Model, $model->attributes = $attributes, this method does it for us
     * The intention is to reduce the lines of code necessary to get a new Metadata model object
     * @param  CiiModel $class      A CiiModel or child instance
     * @param  array    $attributes Attributes to search for, or to prepopulate
     * @return CiiModel
     */
    public function getPrototype($class, $attributes=array(), $defaults=array())
    {
    	if (empty($attributes))
    	{
    		$model = new $class;
    		if (!empty($defaults))
    			$model->populate($defaults);
    		return $model;
    	}

    	$model = $class::model()->findByAttributes($attributes);
    	if ($model === NULL)
    		$model = new $class;

    	if ($model->isNewRecord)
			$model->populate($defaults);
		
    	$model->populate($attributes);
    	return $model;
    }
}
