<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This controller provides generic functionality for basic site actions such as errors, login, logout, registration, activation, etc...
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
class SiteController extends CiiController
{
	/**
	 * beforeAction method, performs operations before an action is presented
	 * @param $action, the action being called
	 * @see http://www.yiiframework.com/doc/api/1.1/CController#beforeAction-detail
	 * @return CiiController::beforeAction
	 */
	public function beforeAction($action)
	{
		$this->breadcrumbs[] = ucwords(Yii::app()->controller->action->id);
		return parent::beforeAction($action);
	}
	
	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
		$this->layout = '//layouts/main';

		if($error=Yii::app()->errorHandler->error)
		{
			if(Yii::app()->request->isAjaxRequest)
			{
				echo $error['message'];
			}
			else
			{
				$this->setPageTitle(Cii::getConfig('name', Yii::app()->name) . ' | Error ' . $error['code']);
				$this->render('error', array('error'=>$error));
			}
		}
	}
	
    /**
     * Provides basic sitemap functionality via XML
     */
	public function actionSitemap()
	{		
		$this->layout = false;
		$content = Yii::app()->db->createCommand('SELECT slug, password, type_id, updated FROM content AS t WHERE vid=(SELECT MAX(vid) FROM content WHERE id=t.id) AND status = 1;')->queryAll();
		$categories = Yii::app()->db->createCommand('SELECT slug, updated FROM categories;')->queryAll();
		$this->renderPartial('sitemap', array('content'=>$content, 'categories'=>$categories));
		Yii::app()->end();
	}
	
    /**
     * Provides search functionality through Sphinx
     * @param int $id   The pagination $id
     */
	public function actionSearch($id=1)
	{
		$this->setPageTitle(Cii::getConfig('name', Yii::app()->name) . ' | Search');
		$this->layout = '//layouts/default';
		$data = array();
		$pages = array();
		$itemCount = 0;
		$pageSize = Cii::getConfig('searchPaginationSize', 10);
		
		if (Cii::get($_GET, 'q', "") != "")
		{	
			$criteria=new CDbCriteria;
			$criteria->addCondition("vid=(SELECT MAX(vid) FROM content WHERE id=t.id)");

			if (strpos($_GET['q'], 'user_id') !== false)
			{
				$criteria->addCondition('author_id = :author_id');
				$criteria->params = array(
					':author_id' => str_replace('user_id:', '', $_GET['q'])
				);
			}
			else
			{

				// Load the search data
				Yii::import('ext.sphinx.SphinxClient');
				$sphinx = new SphinxClient();
				$sphinx->setServer(Cii::getConfig('sphinxHost'), (int)Cii::getConfig('sphinxPort');
				$sphinx->setMatchMode(SPH_MATCH_EXTENDED2);
				$sphinx->setMaxQueryTime(15);
				$result = $sphinx->query(Cii::get($_GET, 'q', NULL), Cii::getConfig('sphinxSource'));	
				$criteria->addInCondition('id', array_keys(isset($result['matches']) ? $result['matches'] : array()));
				
    		}	

			$criteria->addCondition('password = ""');
			$criteria->addCondition('status = 1');
			$criteria->limit = $pageSize;	
			$criteria->order = 'id DESC';		
			$itemCount = Content::model()->count($criteria);
			$pages=new CPagination($itemCount);
			$pages->pageSize=$pageSize;			
			
			$criteria->offset = $criteria->limit*($pages->getCurrentPage());			
			$data = Content::model()->findAll($criteria);
    		$pages->applyLimit($criteria);
			
		}		
		
		$this->render('search', array('id'=>$id, 'data'=>$data, 'itemCount'=>$itemCount, 'pages'=>$pages));
	}

    /**
     * Provides basic MySQL searching functionality
     * @param int $id   The search pagination id
     */
	public function actionMySQLSearch($id=1)
	{
		$this->setPageTitle(Cii::getConfig('name', Yii::app()->name) . ' | Search');
		$this->layout = '//layouts/default';
		$data = array();
		$pages = array();
		$itemCount = 0;
		$pageSize = Cii::getConfig('searchPaginationSize', 10);
		
		if (Cii::get($_GET, 'q', "") != "")
		{	
			$criteria=new CDbCriteria;
			if (strpos($_GET['q'], 'user_id') !== false)
			{
				$criteria->addCondition('author_id = :author_id');
				$criteria->params = array(
					':author_id' => str_replace('user_id:', '', $_GET['q'])
				);
			}
			else
			{
				$criteria->addSearchCondition('content', Cii::get($_GET, 'q', NULL), true, 'OR');
				$criteria->addSearchCondition('title', Cii::get($_GET, 'q', NULL), true, 'OR');
    		}	

    		$criteria->addCondition("vid=(SELECT MAX(vid) FROM content WHERE id=t.id)");
			$criteria->addCondition('password = ""');
			$criteria->addCondition('status = 1');
			$criteria->limit = $pageSize;	
			$criteria->order = 'id DESC';		
			$itemCount = Content::model()->count($criteria);
			$pages=new CPagination($itemCount);
			$pages->pageSize=$pageSize;			
			
			$criteria->offset = $criteria->limit*($pages->getCurrentPage());			
			$data = Content::model()->findAll($criteria);
    		$pages->applyLimit($criteria);	
		}		
		
		$this->render('search', array('id'=>$id, 'data'=>$data, 'itemCount'=>$itemCount, 'pages'=>$pages));
	}
	
    /**
     * Provides functionality to log a user into the system
     */
	public function actionLogin()
	{
		$this->setPageTitle(Cii::getConfig('name', Yii::app()->name) . ' | Login to your account');
		$this->layout = '//layouts/main';
		$model=new LoginForm;

		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
            
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
        
		$this->render('login',array('model'=>$model));
	}
	
    /**
     * Provides functionality to log a user out
     */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->user->returnUrl);
	}
	
	/**
	 * Handles resetting a users password should they forgot it
	 * @param hash $id
	 */
	public function actionForgot($id=NULL)
	{
		$this->layout = '//layouts/main';
		if ($id == NULL)
		{
			if (Cii::get($_POST, 'email', NULL))
			{
				// Verify the email is a real email
				$validator=new CEmailValidator;
				if (!$validator->validateValue(Cii::get($_POST, 'email', NULL)))
				{
					Yii::app()->user->setFlash('reset-error', 'The email your provided is not a valid email address.');
					$this->render('forgot', array('id'=>$id));
					return;
				}
				
				// Check to see if we have a user with that email address
				$user = Users::model()->findByAttributes(array('email'=>Cii::get($_POST, 'email', NULL)));
				if (count($user) == 1)
				{
					// Generate hash and populate db
					$hash = mb_strimwidth(hash("sha256", md5(time() . md5(hash("sha512", time())))), 0, 16);
					$expires = strtotime("+5 minutes");
					
					$meta = UserMetadata::model()->findByAttributes(array('user_id'=>$user->id, 'key'=>'passwordResetCode'));
					if ($meta === NULL)
						$meta = new UserMetadata;
					
					$meta->user_id = $user->id;
					$meta->key = 'passwordResetCode';
					$meta->value = $hash;
					$meta->save();
					
					$meta = UserMetadata::model()->findByAttributes(array('user_id'=>$user->id, 'key'=>'passwordResetExpires'));
					if ($meta === NULL)
						$meta = new UserMetadata;
					
					$meta->user_id = $user->id;
					$meta->key = 'passwordResetExpires';
					$meta->value = $expires;
					$meta->save();
					

					$this->sendEmail($user, 'Your Password Reset Information', '//email/forgot', array('user' => $user, 'hash' => $hash), true, true);
					
					// Set success flash
					Yii::app()->user->setFlash('reset-sent', 'An email has been sent to ' . Cii::get($_POST, 'email', NULL) . ' with further instructions on how to reset your password');
				}
				else
				{
					Yii::app()->user->setFlash('reset-sent', 'An email has been sent to ' . Cii::get($_POST, 'email', NULL) . ' with further instructions on how to reset your password');
					$this->render('forgot', array('id'=>$id));
					return;
				}
				
			}
		}
		else
		{
			$hash = UserMetadata::model()->findByAttributes(array('key'=>'passwordResetCode', 'value'=>$id));
			$expires = UserMetadata::model()->findByAttributes(array('user_id'=>$hash->user_id, 'key'=>'passwordResetExpires'));
			
			if ($hash == NULL || $expires == NULL || time() > $expires->value)
			{
				$this->render('forgot', array('id'=>$id, 'badHash'=>true));
				return;
			}
			
			if (isset($_POST['password']))
			{
				if (Cii::get($_POST, 'password') != NULL && Cii::get($_POST,'password2') != NULL)
				{
					if ($_POST['password'] === $_POST['password2'])
					{
						if (strlen($_POST['password']) >= 8)
						{
							// Reset the password
							$user = Users::model()->findByPk($hash->user_id);
							$user->password = Users::model()->encryptHash($user->email, $_POST['password'], Yii::app()->params['encryptionKey']);
							$user->save();
							
							// Delete the password hash and expires from the database
							$hash->delete();
							$expires->delete();
							
							// Set a success flash message
							Yii::app()->user->setFlash('reset', 'Your password has been reset, and you may now login with your new password');
							
							// Redirect to the login page
							$this->redirect('/login');
						}
	
						Yii::app()->user->setFlash('reset-error', 'The password you provided must be at least 8 characters.');
						$this->render('forgot', array('id'=>$id, 'badHash'=>false));
						return;
					}
					
					Yii::app()->user->setFlash('reset-error', 'The passwords you provided do not match');
					$this->render('forgot', array('id'=>$id, 'badHash'=>false));
					return;
				}
				
				Yii::app()->user->setFlash('reset-error', 'You must provide your password twice for us to reset your password.');
				$this->render('forgot', array('id'=>$id, 'badHash'=>false));
				return;
			}
		}
		$this->render('forgot', array('id'=>$id, 'badHash'=>false));
	}
	
	/**
	 * Activation handler
	 * @param string $email 	The user's email
	 * @param int $id 			The activation key
	 */
	public function actionActivation($email=NULL, $id=NULL) 
	{
		$this->layout = '//layouts/main';
		if ($id != NULL || $email = NULL)
		{
			$record = $user = Users::model()->findByPk($email);
			if ($user != NULL && $user->status == 0)
			{
				$meta = UserMetadata::model()->findByAttributes(array('user_id'=>$email, 'key'=>'activationKey', 'value'=>$id));
				if ($meta != NULL)
				{
					if ($password = Cii::get($_POST, 'password', NULL))
					{
						$cost = Cii::getBcryptCost();

						// Load the bcrypt hashing tools if the user is running a version of PHP < 5.5.x
						if (!function_exists('password_hash'))
							require_once YiiBase::getPathOfAlias('ext.bcrypt.bcrypt').'.php';

						// We still want to secure our password using this algorithm
						$hash = Users::model()->encryptHash($record->email, $password, Yii::app()->params['encryptionKey']);

						if (password_verify($hash, $record->password))
						{
							// Update the user status
							$user->status = 1;
							$user->save();
							
							// Delete the activationKey
							$meta->delete();
							Yii::app()->user->setFlash('activation-success', 'Activation was successful! You may now ' . CHtml::link('login', $this->createUrl('/login')));
							return $this->render('activation');
						}
						else
						{
							Yii::app()->user->setFlash('activation-error', 'Please provide the password you used during the signup process.');
						}
					}

					Yii::app()->user->setFlash('activation-info', 'Enter the password you used to register your account to verify your email address.');
				}
				else
				{
					Yii::app()->user->setFlash('activation-error', 'The activation key your provided was invalid.');
				}
			}
			else
			{
				Yii::app()->user->setFlash('activation-error', 'Unable to activate user using the provided details');
			}
		}
		else
		{
			Yii::app()->user->setFlash('activation-error', 'The activation key your provided was invalid.');
		}
		
		$this->render('activation');
	}
	
	/**
	 * Registration page
	 *
	 **/
	public function actionRegister()
	{
		$this->setPageTitle(Cii::getConfig('name', Yii::app()->name) . ' | Sign Up');
		$this->layout = '//layouts/main';
		$model = new RegisterForm();
		$user = new Users();
		
		$error = '';
		if (isset($_POST) && !empty($_POST))
		{
			$model->attributes = $_POST['RegisterForm'];
			
			if ($model->validate())
			{
				if (!function_exists('password_hash'))
					require_once YiiBase::getPathOfAlias('ext.bcrypt.bcrypt').'.php';
				
				// Bcrypt the initial password instead of just using the basic hashing mechanism
				$hash = Users::model()->encryptHash(Cii::get($_POST['RegisterForm'], 'email'), Cii::get($_POST['RegisterForm'], 'password'), Yii::app()->params['encryptionKey']);
				$cost = Cii::getBcryptCost();

				$password = password_hash($hash, PASSWORD_BCRYPT, array('cost' => $cost));

				$user->attributes = array(
					'email'=>Cii::get($_POST['RegisterForm'], 'email'),
					'password'=>$password,
					'firstName'=> NULL,
					'lastName'=> NULL,
					'displayName'=>Cii::get($_POST['RegisterForm'], 'displayName'),
					'user_role'=>1,
					'status'=>0
				);
				
				try 
				{
					if($user->save())
					{
						$hash = mb_strimwidth(hash("sha256", md5(time() . md5(hash("sha512", time())))), 0, 16);
						$meta = new UserMetadata;
						$meta->user_id = $user->id;
						$meta->key = 'activationKey';
						$meta->value = $hash;
						$meta->save();
						
						// Send the registration email
						$this->sendEmail($user, 'Activate Your Account', '//email/register', array('user' => $user, 'hash' => $hash), true, true);
					
						$this->redirect('/register-success');
						return;
					}
				}
				catch(CDbException $e) 
				{
					$model->addError(null, 'The email address has already been associated to an account. Do you want to login instead?');
				}
			}
		}

		$this->render('register', array('model'=>$model, 'error'=>$error, 'user'=>$user));
	}

	/**
	 * Handles successful registration
	 */
	public function actionRegistersuccess()
	{
		$this->setPageTitle(Cii::getConfig('name', Yii::app()->name) . ' | Registration Successful');
		$this->layout = '//layouts/main';
		$this->render('register-success');
	}

    /**
     * Migrate Action
     * Allows the Site to perform migrations during the installation process
     * @return CiiMigrate Output
     */
    public function actionMigrate()
    {
        $runner=new CConsoleCommandRunner();
        $runner->commands=array(
            'migrate' => array(
                'class' => 'system.cli.commands.MigrateCommand',
                'interactive' => false,
            ),
        );
        
        ob_start();
        $runner->run(array(
            'yiic',
            'migrate',
        ));
        echo htmlentities(ob_get_clean(), null, Yii::app()->charset);
    }
}