<?php

class CategoriesController extends CiiController

	public function filters()
    {
        $id = Yii::app()->getRequest()->getQuery('id');
        if ($id != NULL)
		{
			$lastModified = Yii::app()->db->createCommand("SELECT UNIX_TIMESTAMP(GREATEST( (SELECT IFNULL(MAX(updated), 0) FROM categories WHERE categories.id = {$id}),(SELECT IFNULL(MAX(content.updated), 0) FROM categories LEFT JOIN content ON categories.id = content.category_id WHERE categories.id = {$id} AND vid = (SELECT MAX(vid) FROM content AS content2 WHERE content2.id = content.id)),(SELECT IFNULL(MAX(comments.updated), 0) FROM categories LEFT JOIN content ON categories.id = content.category_id LEFT JOIN comments ON content.id = comments.content_id WHERE categories.id = {$id} AND vid = (SELECT MAX(vid) FROM content AS content2 WHERE content2.id = content.id) )))")->queryScalar();
			$eTag = $this->id . $this->action->id . $id . Cii::get(Yii::app()->user->id, 0);
			
            return array(
                array(
                    'CHttpCacheFilter + index',
                    'cacheControl'=>Cii::get(Yii::app()->user->id) == NULL ? 'public' : 'private' .', no-cache, must-revalidate',
                    'lastModified'=>$lastModified,
                    'etagSeed'=>$eTag
                ),
            );
		}
		return parent::filters();
    }

	/**
	 * Verifies that our request does not produce duplicate content (/about == /content/index/2), and prevents direct access to the controller
	 * protecting it from possible attacks.
	 * @param $id	- The content ID we want to verify before proceeding
	 **/
	private function beforeCiiAction($id)
	{
		// If we do not have an ID, consider it to be null, and throw a 404 error
		if ($id == NULL)
		{
			throw new CHttpException(404,'The specified category cannot be found.');
		}
		
		// Retrieve the HTTP Request
		$r= new CHttpRequest();
		
		// Retrieve what the actual URI
		$requestUri = str_replace($r->baseUrl, '', $r->requestUri);
		
		// Retrieve the route
		$route = '/' . $this->getRoute() . '/' . $id;
		
		$requestUri = preg_replace('/\?(.*)/','',$requestUri);
		
		// If the route and the uri are the same, then a direct access attempt was made, and we need to block access to the controller
		if ($requestUri == $route)
		{
			throw new CHttpException(404,'The specified category cannot be found.');
		}
	}
	
	/**
	 * Handles all incoming requests for the entire site that are not previous defined in CUrlManager
	 * Requests come in, are verified, and then pulled from the database dynamically
	 * Shows all blog posts for a particular category_id
	 * @param $id	- The content ID that we want to pull from the database
	 * @return $this->render() - Render of page that we want to display
	 **/
	public function actionIndex($id=NULL)
	{
		// Run a pre check of our data
		$this->beforeCiiAction($id);
		
		// Retrieve the data
		$category = Categories::model()->findByPk($id);
		$this->breadcrumbs = Categories::model()->getParentCategories($id);
		
		// Parse Metadata
		$meta = Categories::model()->parseMeta($category->metadata);		
		
		$this->setPageTitle(Yii::app()->name . ' | ' . $category->name);
		$layout = isset($meta['layout']) ? $meta['layout']['value'] : 'default';		

		// Set the layout
		$this->setLayout($layout);
		
		$data = array();
		$pages = array();
		$itemCount = 0;
		$pageSize = $this->displayVar((Configuration::model()->findByAttributes(array('key'=>'categoryPaginationSize'))->value), 10);
		
		$criteria=new CDbCriteria;
		$criteria->addCondition("vid=(SELECT MAX(vid) FROM content WHERE id=t.id)");
		$criteria->addCondition('type_id >= 2');
		$criteria->addCondition("category_id = " . $id);
		$criteria->addCondition('password = ""');
		$criteria->addCondition('status = 1');
		$criteria->limit = $pageSize;			
		$criteria->order = 'created DESC';
		
		$itemCount = Content::model()->count($criteria);
		$pages=new CPagination($itemCount);
		$pages->pageSize=$pageSize;
		
		
		$criteria->offset = $criteria->limit*($pages->getCurrentPage());			
		$data = Content::model()->findAll($criteria);
		$pages->applyLimit($criteria);		
		
		$this->render('index', array('id'=>$id, 'category'=>$category, 'data'=>$data, 'itemCount'=>$itemCount, 'pages'=>$pages));
	}
	
	/**
	 * Displays a listing of all blog posts
	 */
	public function actionList()
	{
		$this->setPageTitle(Yii::app()->name . ' | Categories');
		$this->setLayout('main');
		$this->breadcrumbs = array('All Categories');
		$criteria = new CDbCriteria();
		$criteria->addCondition('id != 1');
		$categories = Categories::model()->findAll($criteria);
		$this->render('list', array('categories'=>$categories));
	}
}

?>
