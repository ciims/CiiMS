<?php

class ContentController extends CiiController
{	
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
			throw new CHttpException(404,'The specified post cannot be found.');
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
			throw new CHttpException(404, 'The requested post cannot be found.');
		}
	}
	
	/**
	 * Handles all incoming requests for the entire site that are not previous defined in CUrlManager
	 * Requests come in, are verified, and then pulled from the database dynamically
	 * @param $id	- The content ID that we want to pull from the database
	 * @return $this->render() - Render of page that we want to display
	 **/
	public function actionIndex($id=NULL)
	{
		// Session is not automatically starting. VM issue?
		session_start();
		
		// Run a pre check of our data
		$this->beforeCiiAction($id);
		
		// Retrieve the data
		$content = Content::model()->with('category')->findByPk($id);
		if ($content->status != 1)
			throw new CHttpException('404', 'The article you specified does not exist. If you bookmarked this page, please delete it.');
		$this->breadcrumbs = array_merge(Categories::model()->getParentCategories($content['category_id']), array($content['title']));
		
		// Check for a password
		if ($content->attributes['password'] != '')
		{
			// Check SESSION to see if a password is set
			$tmpPassword = $_SESSION['password'][$id];
			
			if ($tmpPassword != $content->attributes['password'])
			{
				$this->redirect(Yii::app()->createUrl('/content/password/' . $id));
			}
		}
		
		// Parse Metadata
		$meta = Content::model()->parseMeta($content->metadata);
		
		$layout = isset($meta['layout']) ? $meta['layout']['value'] : 'blog';
		
		// Set the layout
		$this->setLayout($layout);
		
		$view = isset($meta['view']) ? $meta['view']['value'] : 'blog';
		
		$this->setPageTitle(Yii::app()->name . ' | ' . $content->title);
		
		$this->render($view, array('id'=>$id, 'data'=>$content, 'meta'=>$meta, 'comments'=>$content->comments, 'model'=>Comments::model()));
	}
	
	/**
	 * Forces a password to be assigned before the user can proceed to the previous page
	 * @param $id - ID of the content we want to investigate
	 **/
	public function actionPassword($id=NULL)
	{	
		$this->setPageTitle(Yii::app()->name . ' | Password Requires');
		
		// Session is not automatically starting. VM issue?
		session_start();
		
		if ($id == NULL)
		{
			$this->redirect(Yii::app()->user->returnUrl);
		}
		
		if (!isset($_SESSION['password']))
		{
			$_SESSION['password'] = array('tries'=>0);
		}
			
		if (isset($_POST['password']))
		{
			$content = Content::model()->findByPk($id);
			$this->debug($_POST); $this->debug($content->attributes);
			if ($_POST['password'] == $content->attributes['password'])
			{
				$_SESSION['password'][$_POST['id']] = $_POST['password'];
				$_SESSION['password']['tries'] = 0;
				$this->redirect(Yii::app()->createUrl($content->attributes['slug']));
			}
			else
			{
				$_SESSION['password']['tries'] = $_SESSION['password']['tries'] + 1;
			}
		}
		$themeView = Configuration::model()->findByAttributes(array('key'=>'themePasswordView'))->value;
		if ($themeView === NULL || $themeView != 1)
			Yii::app()->setTheme('default');
		
		$this->layout = 'main';
		$this->render('password', array('id'=>$id));
	}
	
	/*
	 * Displays a listing of all blog posts for all time in all categories
	 * Is used as a generic catch all behavior
	 */
	public function actionList()
	{
		$this->setPageTitle('All Content');
		$this->setLayout('default');
		
		$this->breadcrumbs = array('Blogroll');
		
		$data = array();
		$pages = array();
		$itemCount = 0;
		$pageSize = $this->displayVar((Configuration::model()->findByAttributes(array('key'=>'contentPaginationSize'))->value), 10);		
		
		$criteria=new CDbCriteria;
		$criteria->addCondition("vid=(SELECT MAX(vid) FROM content WHERE id=t.id)");
		$criteria->addCondition('type_id >= 2');
		$criteria->addCondition('password = ""');
		$criteria->order = 'created DESC';
		$criteria->limit = $pageSize;			
		
		$itemCount = Content::model()->count($criteria);
		$pages=new CPagination($itemCount);
		$pages->pageSize=$pageSize;		
		
		$criteria->offset = $criteria->limit*($pages->getCurrentPage());			
		$data = Content::model()->findAll($criteria);
		$pages->applyLimit($criteria);	
		
		$this->render('all', array('data'=>$data, 'itemCount'=>$itemCount, 'pages'=>$pages));
	}
	
	/**
	 * Displays either all posts or all posts for a particular category_id if an $id is set in RSS Format
	 * So that RSS Readers can access the website
	 */
	public function actionRss($id=NULL)
	{
		$this->layout=false;
		$criteria=new CDbCriteria;
		$criteria->addCondition("vid=(SELECT MAX(vid) FROM content WHERE id=t.id)");
		$criteria->addCondition('type_id >= 2');
		
		if ($id != NULL)
			$criteria->addCondition("category_id = " . $id);
					
		$criteria->order = 'created DESC';
		$data = Content::model()->findAll($criteria);
		
		$this->renderPartial('application.views.site/rss', array('data'=>$data));
		return;
	}
}
?>
