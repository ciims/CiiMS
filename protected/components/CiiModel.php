<?php
/**
 * CiiModel is the base ActiveRecord class which all Models in Cii are derived from
 * @author Charles R. Portwood II <charlesportwoodii@ethreal.net>
 */
class CiiModel extends CActiveRecord
{
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
		'login',
		'logout',
		'admin',
		'hybridauth'
	);
	
	/**
	 * parseMeta pulls the metadata out of a model and returns that metadata as a usable array
	 * @param CiiModel $model - The model to pull metedata from
	 * @return array $items - The metadata in array format
	 */
	public function parseMeta($model)
	{
		$items = array();
		if (!empty($model))
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
	 * verifySlug - Verifies that the provided slug is able to be used and does not conflict with an existing route
	 * @param string $slug - the slug to be used
	 * @param string $title - the title to be used if no slug is provided
	 * @return string $slug - the slug to be used
	 */
	public function verifySlug($slug = '', $title = '')
	{
		$slug = str_replace('/', '-', str_replace('\'', '-', str_replace(' ', '', $slug)));
		if ($slug == '')
			$slug = str_replace('/', '-', str_replace('\'', '-', str_replace(' ', '', $title)));
		
		return strToLower($this->checkSlug($slug));
	}
	
	/**
	 * checkSlug - Recursive method to verify that the slug can be used
	 * @param string $slug - the slug to be checked
	 * @param int $id - the numeric id to be appended to the slug if a conflict exists
	 * @return string $slug - the final slug to be used
	 */
	public function checkSlug($slug, $id=NULL)
	{
		// Find the number of items that have the same slug as this one
		$count = $this->countByAttributes(array('slug'=>$slug . $id));
		
		// If we found an item that matched, it's possible that it is the current item, in which case we don't need to alter the slug
		if ($count >= 1)
		{
			if (!$this->isNewRecord)
			{
				// Pull the data that matches
				$data = $this->findByPk($this->id);
				
				// Check the pulled data id to the current item
				if ($data->id == $this->id)
					$count = 0;	
			}
		}
		
		if ($count == 0 && !in_array($slug, $this->forbiddenRoutes))
			return $slug . $id;
		else
		{
			if ($id == NULL)
				$id = 1;
			else 
				$id++;
			return $this->checkSlug($slug, $id);
		}
	}
}
?>
