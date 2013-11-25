<?php

class ContentController extends CiiSiteController
{
	/**
	 * Base filter, allows logged in and non-logged in users to cache the page
	 */
	public function filters()
    {
        $id = Yii::app()->getRequest()->getQuery('id');
        $key = false;
        
        if ($id != NULL)
		{
			$lastModified = Yii::app()->db->createCommand("SELECT UNIX_TIMESTAMP(GREATEST((SELECT IFNULL(MAX(updated),0) FROM content WHERE id = {$id} AND vid = (SELECT MAX(vid) FROM content AS content2 WHERE content2.id = content.id)), (SELECT IFNULL(MAX(updated), 0) FROM comments WHERE content_id = :id)))")->bindParam(':id', $id)->queryScalar();
			$theme = Cii::getConfig('theme', 'default');
			
			$keyFile = ContentMetadata::model()->findByAttributes(array('content_id'=>$id, 'key'=>'view'));
			
			if ($keyFile != NULL)
			    $key = dirname(__FILE__) . '/../../themes/' . $theme . '/views/content/' . $keyFile->value . '.php';
			
			if ($key && file_exists($key))
				$lastModified = filemtime($key) >= $lastModified ? filemtime($key) : $lastModified;
			
			$eTag = $this->id . Cii::get($this->action, 'id', NULL) . $id . Cii::get(Yii::app()->user->id, 0) . $lastModified;
			
            return array(
                'accessControl',
                array(
                    'CHttpCacheFilter + index',
                    'cacheControl'=>Cii::get(Yii::app()->user->id) == NULL ? 'public' : 'private' .', no-cache, must-revalidate',
                    'etagSeed'=> YII_DEBUG ? mt_rand() : $eTag
                ),
            );
		}

		return parent::filters();
    }
	
	
	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // Allow all users to any section
				'actions' => array('index', 'password', 'list', 'rss'),
				'users'=>array('*'),
			),
			array('allow',  // deny all users
				'actions' => array('like'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
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
			throw new CHttpException(404, Yii::t('ciims.controllers.Content', 'The specified post cannot be found.'));
		
		// Retrieve the HTTP Request
		$r = new CHttpRequest();
		
		// Retrieve what the actual URI
		$requestUri = str_replace($r->baseUrl, '', $r->requestUri);
		
		// Retrieve the route
		$route = '/' . $this->getRoute() . '/' . $id;
		$requestUri = preg_replace('/\?(.*)/','',$requestUri);
		
		// If the route and the uri are the same, then a direct access attempt was made, and we need to block access to the controller
		if ($requestUri == $route)
			throw new CHttpException(404, Yii::t('ciims.controllers.Content', 'The requested post cannot be found.'));
        
        return str_replace($r->baseUrl, '', $r->requestUri);;
	}
	
	/**
	 * Handles all incoming requests for the entire site that are not previous defined in CUrlManager
	 * Requests come in, are verified, and then pulled from the database dynamically
	 * @param $id	- The content ID that we want to pull from the database
	 * @return $this->render() - Render of page that we want to display
	 **/
	public function actionIndex($id=NULL)
    {
		// Run a pre check of our data
		$requestUri = $this->beforeCiiAction($id);
		
        // Set the ReturnURL to this page so that the user can be redirected back to her after login
        Yii::app()->user->setReturnUrl($requestUri);
        
		// Retrieve the data
		$content = Content::model()->with('category')->findByPk($id);

		if ($content->status != 1 || strtotime($content->published) > time())
			throw new CHttpException(404, Yii::t('ciims.controllers.Content', 'The article you specified does not exist. If you bookmarked this page, please delete it.'));
        
		$this->breadcrumbs = array_merge(Categories::model()->getParentCategories($content['category_id']), array($content['title']));
		
		// Check for a password
		if ($content->attributes['password'] != '')
		{
			// Check SESSION to see if a password is set
			$tmpPassword = Cii::get(Cii::get(Cii::get($_SESSION, 'password', array()), $id, array()), 'password', NULL);
			
			if ($tmpPassword != $content->attributes['password'])
				$this->redirect(Yii::app()->createUrl('/content/password/' . $id));
		}
		
		// Parse Metadata
		$meta = Content::model()->parseMeta($content->metadata);
		$this->setLayout($content->layout);
		
		$this->setPageTitle(Yii::t('ciims.controllers.Content', '{{app_name}} | {{label}}', array(
			'{{app_name}}' => Cii::getConfig('name', Yii::app()->name),
			'{{label}}'    => $content->title
		)));

        $this->params['meta']['description'] = $content->extract;	
		$this->render($content->view, array(
				'id'=>$id, 
				'data'=>$content, 
				'meta'=>$meta,
				'comments'=>Comments::model()->countByAttributes(array('content_id' => $content->id, 'approved' => 1)),
				'model'=>Comments::model()
			)
		);
	}
	
	/**
	 * Provides functionality for "liking and un-liking" a post
	 * @param int $id		The Content ID
	 */
	public function actionLike($id=NULL)
	{
		$this->layout=false;
		header('Content-type: application/json');
		
		// Load the content
		$content = ContentMetadata::model()->findByAttributes(array('content_id' => $id, 'key' => 'likes'));

		if ($content === NULL)
		{
			$content = new ContentMetadata;
			$content->content_id = $id;
			$content->key = 'likes';
			$content->value = 0;
		}

		if ($id === NULL || $content === NULL)
		{
			echo CJavaScript::jsonEncode(array('status' => 'error', 'message' => Yii::t('ciims.controllers.Content', 'Unable to access post')));
			return Yii::app()->end();
		}
		
		// Load the user likes, create one if it does not exist
		$user = UserMetadata::model()->findByAttributes(array('user_id' => Yii::app()->user->id, 'key' => 'likes'));

		if ($user === NULL)
		{
			$user = new UserMetadata;
			$user->user_id = Yii::app()->user->id;
			$user->key = 'likes';
			$user->value = json_encode(array());
		}
		
		$type = "inc";
		$likes = json_decode($user->value, true);
		if (in_array($id, array_values($likes)))
		{
			$type = "dec";
			$content->value -= 1;
			if ($content->value <= 0)
				$content->value = 0;
			$element = array_search($id, $likes);
			unset($likes[$element]);
		}
		else
		{
			$content->value += 1;
			array_push($likes, $id);
		}
		
		$user->value = json_encode($likes);

		if (!$user->save())
		{
			echo CJavaScript::jsonEncode(array('status' => 'error', 'message' => Yii::t('ciims.controllers.Content', 'Unable to save user like')));
			return Yii::app()->end();
		}

		if (!$content->save())
		{
			echo CJavaScript::jsonEncode(array('status' => 'error', 'message' => Yii::t('ciims.controllers.Content', 'Unable to save like')));
			return Yii::app()->end();
		}
		
		echo CJavaScript::jsonEncode(array('status' => 'success', 'type' => $type, 'message' => Yii::t('ciims.controllers.Content', 'Liked saved')));
		return Yii::app()->end();
	}
	
	/**
	 * Forces a password to be assigned before the user can proceed to the previous page
	 * @param $id - ID of the content we want to investigate
	 **/
	public function actionPassword($id=NULL)
	{	
		$this->setPageTitle(Yii::t('ciims.controllers.Content', '{{app_name}} | {{label}}', array(
			'{{app_name}}' => Cii::getConfig('name', Yii::app()->name),
			'{{label}}'    => Yii::t('ciims.controllers.Content', 'Password Required')
		)));
		
		if ($id == NULL)
			$this->redirect(Yii::app()->user->returnUrl);
		
		// Set some default data
		if (Cii::get(Cii::get($_SESSION, 'password', array()), $id, NULL) == NULL)
			$_SESSION['password'][$id] = array('tries'=>0, 'expires' => time() + 300);

		// If the number of attempts is >= 3
		if (Cii::get(Cii::get(Cii::get($_SESSION, 'password', array()), $id, array()), 'tries', 0) >= 3)
		{
			// If the expires time has already passed, unlock the account
			if (Cii::get(Cii::get(Cii::get($_SESSION, 'password', array()), $id, array()), 'expires', 0) <= time())
			{
				$_SESSION['password'][$id] = array('tries'=>0, 'expires' => time() + 300);
			}
			else
			{
				// Otherwise prevent access to it
				Yii::app()->user->setFlash('error', Yii::t('ciims.controllers.Content', 'Too many password attempts. Please try again in 5 minutes'));
				unset($_POST['password']);
				$_SESSION['password'][$id]['expires'] 	= time() + 300;
			}
		}

		if (Cii::get($_POST, 'password', NULL) !== NULL)
		{
			$content = Content::model()->findByPk($id);

			$encrypted = Cii::encrypt(Cii::get($_POST, 'password'));

			if ($encrypted == $content->attributes['password'])
			{
				$_SESSION['password'][$id]['password'] = $encrypted;
				$_SESSION['password'][$id]['tries'] = 0;
				$this->redirect(Yii::app()->createUrl($content->attributes['slug']));
			}
			else
			{
				Yii::app()->user->setFlash('error', Yii::t('ciims.controllers.Content', 'Incorrect password'));
				$_SESSION['password'][$id]['tries'] 	= $_SESSION['password'][$id]['tries'] + 1;
				$_SESSION['password'][$id]['expires'] 	= time() + 300;
			}
            
		}
		
		$this->layout = 'password';
		$this->render('password', array('id'=>$id));
	}
	
	/*
	 * Displays a listing of all blog posts for all time in all categories
	 * Is used as a generic catch all behavior
	 */
	public function actionList()
	{
		$this->setPageTitle(Yii::t('ciims.controllers.Content', 'All Content'));
		$this->setLayout('default');
		
		$this->breadcrumbs = array(Yii::t('ciims.controllers.Content', 'Blogroll'));
		
		$data = array();
		$pages = array();
		$itemCount = 0;
		$pageSize = Cii::getConfig('contentPaginationSize', 10);	
		
		$criteria = Content::model()->getBaseCriteria()
								    ->addCondition('type_id >= 2')
		         				    ->addCondition('password = ""');
		$criteria->order = 'published DESC';

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
	 * @param  int $id
	 */
	public function actionRss($id=NULL)
	{
		ob_end_clean();
		header('Content-type: text/xml; charset=utf-8');
		$url = 'http://'.Yii::app()->request->serverName . Yii::app()->baseUrl;
		$this->setLayout(null);
		$criteria = Content::model()->getBaseCriteria()
								   ->addCondition('type_id >= 2');
                 
		if ($id != NULL)
			$criteria->addCondition("category_id = " . $id);
					
		$criteria->order = 'created DESC';
		$data = Content::model()->findAll($criteria);
		
		$this->renderPartial('application.views.site/rss', array('data'=>$data, 'url'=> $url));
		return;
	}
}
