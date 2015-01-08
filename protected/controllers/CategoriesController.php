<?php

class CategoriesController extends CiiController
{
	/**
	 * Base filter, allows logged in and non-logged in users to cache the page
	 */
	public function filters()
    {
        $id = Yii::app()->getRequest()->getQuery('id');

		return CMap::mergeArray(parent::filters(), array(
			array(
                'CHttpCacheFilter + index',
                'cacheControl'=>Cii::get(Yii::app()->user->id) == NULL ? 'public' : 'private' .', no-cache, must-revalidate',
                'etagSeed'=>$id
            ),
			array(
			    'COutputCache + list',
			    'duration' => YII_DEBUG ? 1 : 86400,
			    'varyByParam' => array('page'),
			    'varyByLanguage' => true,
			    'dependency' => array(
				    'class'=>'CDbCacheDependency',
				    'sql'=>'SELECT MAX(updated) FROM content'. ($id!=NULL ? 'WHERE category_id = ' . $id : NULL),
				)
			),
			array(
			    'COutputCache + rss',
			    'duration' => YII_DEBUG ? 1 : 86400,
			    'dependency' => array(
				    'class'=>'CDbCacheDependency',
				    'sql'=>'SELECT MAX(updated) FROM content'. ($id!=NULL ? 'WHERE category_id = ' . $id : NULL),
				)
			)
		));
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

		// Set the layout
		$this->setLayout('default');
		
		$this->setPageTitle(Yii::t('ciims.controllers.Categories', '{{app_name}} | {{label}}', array(
			'{{app_name}}' => Cii::getConfig('name', Yii::app()->name),
			'{{label}}'    => $category->name
		)));	
		
		$pageSize = Cii::getConfig('categoryPaginationSize', 10);	
		
		$criteria = Content::model()->getBaseCriteria()
									->addCondition('type_id >= 2')
									->addCondition("category_id = " . $id)
									->addCondition('password = ""');

		$criteria->limit = $pageSize;			
		$criteria->order = 'created DESC';
		
		$itemCount = Content::model()->count($criteria);
		$pages=new CPagination($itemCount);
		$pages->pageSize=$pageSize;
		
		
		$criteria->offset = $criteria->limit*($pages->getCurrentPage());
		$data = Content::model()->findAll($criteria);

		$pages->applyLimit($criteria);		

		$this->render('index', array('id'=>$id, 'category'=>$category, 'data'=>$data, 'itemCount'=>$itemCount, 'pages'=>$pages, 'meta' => array('description' => $category->getDescription())));
	}

	/**
	 * Displays either all posts or all posts for a particular category_id if an $id is set in RSS Format
	 * So that RSS Readers can access the website
	 * @param  int $id
	 */
	public function actionRss($id=NULL)
	{
		Yii::app()->log->routes[0]->enabled = false; 
		ob_end_clean();
		header('Content-type: text/xml; charset=utf-8');
		$url = 'http://'.Yii::app()->request->serverName . Yii::app()->baseUrl;
		$this->setLayout(null);
		$criteria = Content::model()->getBaseCriteria()
								    ->addCondition('type_id >= 2')
									->addCondition('password = ""');
                 
		if ($id != NULL)
			$criteria->addCondition("category_id = " . $id);
					
		$criteria->order = 'created DESC';
		$data = Content::model()->findAll($criteria);
		
		$this->renderPartial('application.views.site/rss', array('data'=>$data, 'url'=> $url));
		return;
	}
}
