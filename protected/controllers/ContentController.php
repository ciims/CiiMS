<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This controller provides functionality to view content
 *
 * PHP version 5
 *
 * MIT LICENSE Copyright (c) 2012-2013 Charles R. Portwood II
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation 
 * files (the "Software"), to deal in the Software without restriction, including without limitation the rights to 
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom 
 * the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF 
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE 
 * FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION 
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @category   CategoryName
 * @package    CiiMS Content Management System
 * @author     Charles R. Portwood II <charlesportwoodii@ethreal.net>
 * @copyright  Charles R. Portwood II <https://www.erianna.com> 2012-2013
 * @license    http://opensource.org/licenses/MIT  MIT LICENSE
 * @link       https://github.com/charlesportwoodii/CiiMS
 */
class ContentController extends CiiController
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
			$lastModified = Yii::app()->db->createCommand("SELECT UNIX_TIMESTAMP(GREATEST((SELECT IFNULL(MAX(updated),0) FROM content WHERE id = {$id} AND vid = (SELECT MAX(vid) FROM content AS content2 WHERE content2.id = content.id)), (SELECT IFNULL(MAX(updated), 0) FROM comments WHERE content_id = {$id})))")->queryScalar();
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
			throw new CHttpException(404,'The specified post cannot be found.');
		
		// Retrieve the HTTP Request
		$r = new CHttpRequest();
		
		// Retrieve what the actual URI
		$requestUri = str_replace($r->baseUrl, '', $r->requestUri);
		
		// Retrieve the route
		$route = '/' . $this->getRoute() . '/' . $id;
		$requestUri = preg_replace('/\?(.*)/','',$requestUri);
		
		// If the route and the uri are the same, then a direct access attempt was made, and we need to block access to the controller
		if ($requestUri == $route)
			throw new CHttpException(404, 'The requested post cannot be found.');
        
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
        
		if ($content->status != 1)
			throw new CHttpException('404', 'The article you specified does not exist. If you bookmarked this page, please delete it.');
        
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
		$this->setPageTitle(Yii::app()->name . ' | ' . $content->title);
	
		$this->render($content->view, array(
				'id'=>$id, 
				'data'=>$content, 
				'meta'=>$meta,
				'comments'=>Comments::model()->countByAttributes(array('content_id' => $content->id)),
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
		$content = Content::model()->findByPk($id);
		if ($id === NULL || $content === NULL)
		{
			echo CJavaScript::jsonEncode(array('status' => 'error', 'message' => 'Unable to access post'));
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
			$content->like_count -= 1;
			if ($content->like_count <= 0)
				$content->like_count = 0;
			$element = array_search($id, $likes);
			unset($likes[$element]);
		}
		else
		{
			$content->like_count += 1;
			array_push($likes, $id);
		}
		
		$user->value = json_encode($likes);
		if (!$user->save())
		{
			echo CJavaScript::jsonEncode(array('status' => 'error', 'message' => 'Unable to save user like'));
			return Yii::app()->end();
		}

		if (!$content->save())
		{
			echo CJavaScript::jsonEncode(array('status' => 'error', 'message' => 'Unable to save like'));
			return Yii::app()->end();
		}
		
		echo CJavaScript::jsonEncode(array('status' => 'success', 'type' => $type, 'message' => 'Liked saved'));
		return Yii::app()->end();
	}
	
	/**
	 * Forces a password to be assigned before the user can proceed to the previous page
	 * @param $id - ID of the content we want to investigate
	 **/
	public function actionPassword($id=NULL)
	{	
		$this->setPageTitle(Yii::app()->name . ' | Password Requires');
		
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
				Yii::app()->user->setFlash('error', 'Too many password attempts. Please try again in 5 minutes');
				unset($_POST['password']);
				$_SESSION['password'][$id]['expires'] 	= time() + 300;
			}
		}

		if (Cii::get($_POST, 'password', NULL) !== NULL)
		{
			$content = Content::model()->findByPk($id);

			$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(Yii::app()->params['encryptionKey']), $_POST['password'], MCRYPT_MODE_CBC, md5(md5(Yii::app()->params['encryptionKey']))));

			if ($encrypted == $content->attributes['password'])
			{
				$_SESSION['password'][$id]['password'] = $encrypted;
				$_SESSION['password'][$id]['tries'] = 0;
				$this->redirect(Yii::app()->createUrl($content->attributes['slug']));
			}
			else
			{
				Yii::app()->user->setFlash('error', 'Incorrect password');
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
		$this->setPageTitle('All Content');
		$this->setLayout('default');
		
		$this->breadcrumbs = array('Blogroll');
		
		$data = array();
		$pages = array();
		$itemCount = 0;
		$pageSize = Cii::getConfig('contentPaginationSize', 10);	
		
		$criteria=new CDbCriteria;
        $criteria->order = 'created DESC';
        $criteria->limit = $pageSize;
		$criteria->addCondition("vid=(SELECT MAX(vid) FROM content WHERE id=t.id)")
		         ->addCondition('type_id >= 2')
		         ->addCondition('password = ""')
		         ->addCondition('status = 1');
		
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
		$criteria->addCondition("vid=(SELECT MAX(vid) FROM content WHERE id=t.id)")
		         ->addCondition('type_id >= 2')
		         ->addCondition('status = 1');
                 
		if ($id != NULL)
			$criteria->addCondition("category_id = " . $id);
					
		$criteria->order = 'created DESC';
		$data = Content::model()->findAll($criteria);
		
		$this->renderPartial('application.views.site/rss', array('data'=>$data));
		return;
	}
}