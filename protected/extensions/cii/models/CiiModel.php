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
		'register-success',
		'profile',
		'login',
		'logout',
		'admin',
		'hybridauth',
		'dashboard',
		'acceptinvite'
	);
	
	/**
	 * parseMeta pulls the metadata out of a model and returns that metadata as a usable array
	 * @param CiiModel $model - The model to pull metedata from
	 * @return array $items - The metadata in array format
	 */
	public function parseMeta($model)
	{
		$items = array();
		if ($model !== NULL)
		{
			foreach ($model as $v)
			{
				if (isset($items[$v->key]))
				{
					$v->key = $v->key;
				}
			
				$items[$v->key] = array(
					'value'=>$v->value
					);
			}
		}

		return $items;
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
	 * Consolodates $this->creatd, and $this->updated attributes to our base model, rather than defining it in every Model
	 * @return [type] [description]
	 */
	public function beforeValidate()
	{
		if ($this->hasAttribute('created'))
		{
	        if ($this->isNewRecord)
	            $this->created = new CDbExpression('NOW()');
		}

		if ($this->hasAttribute('updated'))
       		$this->updated = new CDbExpression('NOW()');

       	return parent::beforeValidate();
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
}
