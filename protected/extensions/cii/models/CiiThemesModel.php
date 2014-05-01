<?php

class CiiThemesModel extends CiiSettingsModel
{
	/**
     * @var string  The theme name
     */
	private $theme = NULL;

    /**
     * Tells CiiMS to route everything to a single point so that a JS app can handle the rendering
     * @var boolean $noRouting
     */
    public $noRouting = false;

	/**
     * Retrieves all categories to display int he footer
     * @return array $items     The CMenu Items we are going to return
     */
    public function getCategories()
    {
        $items = array(array('label' =>Yii::t('ciims.models.theme',  'All Posts'), 'url' => Yii::app()->createUrl('/blog')));
        $categories = Yii::app()->cache->get('CiiMS::Categories::list');
        if ($categories == false)
        {
            $criteria = new CDbCriteria();
            $criteria->addCondition('type_id = 2')
                     ->addCondition('status = 1');
            $criteria->with = 'content';
            $criteria->select = 't.id, t.slug, t.name';

            $categories = Categories::model()->findAll($criteria);
            Yii::app()->cache->set('CiiMS::Categories::list', $categories);
        }

        foreach ($categories as $k=>$v)
        {
            if ($v['name'] != 'Uncategorized')
                $items[] = array('label' => $v->name, 'url' => Yii::app()->createUrl('/' . $v->slug));
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
        $content = Yii::app()->cache->get('CiiMS::Content::list');
        if ($content == false)
        {
            $criteria = Content::model()->getBaseCriteria()
                        ->addCondition('type_id = 2')
                        ->addCondition('password = ""');
            $criteria->order = 'published DESC';
            $criteria->limit = 5;

            $content = Content::model()->findAll($criteria);
            Yii::app()->cache->set('CiiMS::Content::list', $content);
        }

        foreach ($content as $v)
			$items[] = array('label' => $v->title, 'url' => Yii::app()->createAbsoluteUrl($v->slug), 'itemOptions' => array('id' => $v->id, 'published' => $v->published));

        return $items;
    }

    /**
	 * Retrieves related posts to a given post
	 */
	public function getRelatedPosts($id, $category_id)
	{
		$items = array();
        $criteria = Content::model()->getBaseCriteria()
                    ->addCondition('category_id = :category_id')
                    ->addCondition('id != :id')
                    ->addCondition('type_id = 2')
                    ->addCondition('password = ""');
        $criteria->order = 'published DESC';
        $criteria->limit = 5;
        $criteria->params = array(':id' => $id, ':category_id' => $category_id);

        $related = Content::model()->findAll($criteria);

		 foreach ($related as $v)
		 	$items[] = array('label' => $v->title, 'url' => Yii::app()->createAbsoluteUrl($v->slug), 'itemOptions' => array('id' => $v->id, 'published' => $v->published));

        return $items;
	}

    /**
     * Retrieves the posts authored by a given user
     * @param  integer $id the id of the user
     * @return array of items
     */
    public function getPostsByAuthor($id=1)
    {
        $items = array();
        $criteria = Content::model()->getBaseCriteria()
                    ->addCondition('author_id = :author_id')
                    ->addCondition('type_id = 2')
                    ->addCondition('password = ""');
        $criteria->order = 'published DESC';
        $criteria->limit = 5;
        $criteria->params = array(':author_id' => $id);

        $related = Content::model()->findAll($criteria);

        foreach ($related as $v)
            $items[] = array('label' => $v['title'], 'url' => Yii::app()->createAbsoluteUrl($v->slug), 'itemOptions' => array('id' => $v->id, 'published' => $v->published));

        return $items;
    }

    /**
     * Retrieves the CiiMenuItems from the configuration. If the items are not populated, then it
     * builds them out from CiiMenu::$defaultItems
     */
    public function getMenu()
    {
        // Retrieve the item from cache since we're going to have to build this out manually
        $items = array();
        $fullRoutes = explode('|', $this->menu);
        foreach ($fullRoutes as $route)
        {
            if ($route == "")
                continue;
            $items[] = array('label' => ucwords(str_replace('-', ' ', $route)), 'url' => Yii::app()->createUrl('/' . $route), 'active' => false);
        }

        return $items;
    }

    /**
     * Retrieves the tags for a particular article and flattens them to a pretty array
     * @param  int $id     The content id
     * @return array
     */
    public function getContentTags($id)
    {
        $items = array();
        $tags = Content::model()->findByPk($id)->getTags();
        foreach ($tags as $item)
            $items[] = array('label' => $item, 'url' => $this->createUrl('/search?q=' . $item));

        return $items;
    }
}
