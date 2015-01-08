<?php

class ContentController extends CiiController
{
	/**
	 * Base filter, allows logged in and non-logged in users to cache the page
	 */
	public function filters()
    {
        $id = Yii::app()->getRequest()->getQuery('id');

        if ($id != NULL)
		{
       		$vid =  Yii::app()->getRequest()->getQuery('vid');
            return array(
                'accessControl',
                array(
                    'CHttpCacheFilter + index',
                    'cacheControl'=>Cii::get(Yii::app()->user->id) == NULL ? 'public' : 'private' .', no-cache, must-revalidate',
                    'etagSeed' => $id.$vid
                ),
                array(
                    'COutputCache + index',
                    'duration' => YII_DEBUG ? 1 : 86400, // 24 hour cache duration
                    'varyByParam' => array('id', 'vid'),
                    'varyByLanguage' => true,
                    'varyByExpression' => 'Yii::app()->user->isGuest'
                )
            );
		}

		return CMap::mergeArray(parent::filters(), array(array(
		    'COutputCache + list',
		    'duration' => YII_DEBUG ? 1 : 86400,
		    'varyByParam' => array('page'),
		    'varyByLanguage' => true,
		    'dependency' => array(
			    'class'=>'CDbCacheDependency',
			    'sql'=>'SELECT MAX(updated) FROM content',
			)
		)));
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
				'actions' => array('index', 'password', 'list'),
				'users'=>array('*'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	/**
	 * Handles all incoming requests for the entire site that are not previous defined in CUrlManager
	 * Requests come in, are verified, and then pulled from the database dynamically
	 * @param $id	- The content ID that we want to pull from the database
	 * @return $this->render() - Render of page that we want to display
	 **/
	public function actionIndex($id=NULL, $vid=NULL)
    {
        // Set the ReturnURL to this page so that the user can be redirected back to here after login
        Yii::app()->user->setReturnUrl($this->beforeCiiAction($id));
        
		// Retrieve the data
		$content = Content::model()->findByPk($id);

		if ($content->status != 1 || !$content->isPublished())
			throw new CHttpException(404, Yii::t('ciims.controllers.Content', 'The article you specified does not exist. If you bookmarked this page, please delete it.'));
		
		// Check for a password
		if ($content->password != '' || Cii::decrypt($content->password) != '')
		{
			// Check SESSION to see if a password is set
			$tmpPassword = Cii::get(Cii::get(Cii::get($_SESSION, 'password', array()), $id, array()), 'password', NULL);
			
			if ($tmpPassword != $content->password)
				$this->redirect(Yii::app()->createUrl('/content/password/' . $id));
		}
		
		// Parse Metadata
		$this->setLayout($content->layout);
		
		$this->setPageTitle(Yii::t('ciims.controllers.Content', '{{app_name}} | {{label}}', array(
			'{{app_name}}' => Cii::getConfig('name', Yii::app()->name),
			'{{label}}'    => $content->title
		)));

        $this->params['meta']['description'] = $content->extract;	
		$this->render($content->view, array(
				'id'=>$content->id, 
				'data'=>$content, 
				'meta'=>$content->parseMeta($content->id)
			)
		);
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
		$this->setPageTitle(Yii::t('ciims.controllers.Content', '{{app_name}} | {{label}}', array(
			'{{app_name}}' => Cii::getConfig('name', Yii::app()->name),
			'{{label}}'    => Yii::t('ciims.controllers.Content', 'All Content')
		)));

		$this->setLayout('default');
		
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
}
