<?php

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
				$this->setPageTitle(Yii::app()->name . ' | Error ' . $error['code']);
				$this->render('error', array('error'=>$error));
			}
		}
	}
	
	public function actionSitemap()
	{		
		$this->layout = false;
		
		// Retrieve all contents and categories
		$content = Yii::app()->db->createCommand('SELECT slug, type_id, updated FROM content AS t WHERE vid=(SELECT MAX(vid) FROM content WHERE id=t.id) AND status = 1;')->queryAll();
		$categories = Yii::app()->db->createCommand('SELECT slug, updated FROM categories;')->queryAll();
		$this->renderPartial('sitemap', array('content'=>$content, 'categories'=>$categories));
		return;
	}
	
	public function actionSearch($id=1)
	{
		$this->setPageTitle(Yii::app()->name . ' | Search');
		$this->layout = '//layouts/default';
		$data = array();
		$pages = array();
		$itemCount = 0;
		$pageSize = $this->displayVar((Configuration::model()->findByAttributes(array('key'=>'searchPaginationSize'))->value), 10);
		
		if (isset($_GET['q']) && $_GET['q'] != '')
		{
					
			// Load the search data
			Yii::import('ext.sphinx.SphinxClient');
			$sphinx = new SphinxClient();
			$sphinx->setServer(Yii::app()->params['sphinxHost'], (int)Yii::app()->params['sphinxPort']);
			$sphinx->setMatchMode(SPH_MATCH_EXTENDED2);
			$sphinx->setMaxQueryTime(15);
			$result = $sphinx->query($_GET['q'], Yii::app()->params['sphinxSource']);			
			
			$criteria=new CDbCriteria;
			$criteria->addInCondition('id', array_keys(isset($result['matches']) ? $result['matches'] : array()));
			$criteria->addCondition("vid=(SELECT MAX(vid) FROM content WHERE id=t.id)");
			$criteria->addCondition('password = ""');
			
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

	public function actionMySQLSearch($id=1)
	{
		$this->setPageTitle(Yii::app()->name . ' | Search');
		$this->layout = '//layouts/default';
		$data = array();
		$pages = array();
		$itemCount = 0;
		$pageSize = $this->displayVar((Configuration::model()->findByAttributes(array('key'=>'searchPaginationSize'))->value), 10);
		
		if (isset($_GET['q']) && $_GET['q'] != '')
		{	
			$criteria=new CDbCriteria;
			$criteria->addSearchCondition('content', $_GET['q'], true, 'OR');
			$criteria->addSearchCondition('title', $_GET['q'], true, 'OR');
			$criteria->addCondition("vid=(SELECT MAX(vid) FROM content WHERE id=t.id)");
			$criteria->addCondition('password = ""');
			
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
	
	public function actionLogin()
	{
		$this->setPageTitle(Yii::app()->name . ' | Login to your account');
		$this->layout = '//layouts/main';
		$model=new LoginForm;

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
			{ 
				$this->redirect(Yii::app()->user->returnUrl);
			}
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}
	
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
			if (isset($_POST['email']))
			{
				// Verify the email is a real email
				$validator=new CEmailValidator;
				if (!$validator->validateValue($_POST['email']))
				{
					Yii::app()->user->setFlash('reset-error', 'The email your provided is not a valid email address.');
					$this->render('forgot', array('id'=>$id));
					return;
				}
				
				// Check to see if we have a user with that email address
				$user = Users::model()->findByAttributes(array('email'=>$_POST['email']));
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
					
					// Create the message to be sent to the user
					$message = "
					{$user->displayName},<br /><br />
					You recently notified us that you forgot your password. Don't worry, it happens to all of us. To reset your password, please " . CHtml::link('click here', Yii::app()->createAbsoluteUrl('/forgot/' . $hash)) .
					".<br /><br />Thank you,<br />" . Yii::app()->name . 
					" Team<br /><br />P.S. If you did not request this email, you may safely ignore it.";
					
					// Send email
					Yii::import('application.extensions.phpmailer.JPhpMailer');
					$mail = new JPhpMailer;
					$mail->IsSMTP();
					$mail->SetFrom(Configuration::model()->findByAttributes(array('key'=>'adminEmail'))->value, Yii::app()->name . ' Administrator');
					$mail->Subject = 'Your Password Reset Information';
					$mail->MsgHTML($message);
					$mail->AddAddress($user->email, $user->displayName);
					$mail->Send();
					
					// Set success flash
					Yii::app()->user->setFlash('reset-sent', 'An email has been sent to ' . $_POST['email'] . ' with further instructions on how to reset your password');
				}
				else
				{
					Yii::app()->user->setFlash('reset-error', 'No user with that email address was found');
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
				if ($this->displayVar($_POST['password']) != NULL && $this->displayVar($_POST['password2']) != NULL)
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
	 */
	public function actionActivation($email=NULL, $id=NULL) 
	{
		$this->layout = '//layouts/main';
		if ($id != NULL || $email=NULL)
		{
			$user = Users::model()->findByPk($email);
			if ($user != NULL && $user->status == 0)
			{
				$meta = UserMetadata::model()->findByAttributes(array('user_id'=>$email, 'key'=>'activationKey', 'value'=>$id));
				if ($meta != NULL)
				{
					// Update the user status
					$user->status = 1;
					$user->save();
					
					// Delete the activationKey
					$meta->delete();
					Yii::app()->user->setFlash('activation-success', 'You may now login');
				}
				else
				{
					Yii::app()->user->setFlash('activation-error', 'The activation key your provided was invalid.');
				}
			}
			else
			{
				Yii::app()->user->setFlash('activation-error', 'The user requested either does not exist, or has already been activated.');
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
		$this->setPageTitle(Yii::app()->name . ' | Sign Up');
		$this->layout = '//layouts/main';
		$model = new RegisterForm();
		$user = new Users();
		
		Yii::import('ext.recaptchalib');
		$captcha = new recaptchalib();
		$error = '';
		if (isset($_POST) && !empty($_POST))
		{
			$model->attributes = $_POST['RegisterForm'];
			
			if ($model->validate())
			{
				$user->attributes = array(
					'email'=>$_POST['RegisterForm']['email'],
					'password'=>Users::model()->encryptHash($_POST['RegisterForm']['email'], $_POST['RegisterForm']['password'], Yii::app()->params['encryptionKey']),
					'firstName'=>$_POST['RegisterForm']['firstName'],
					'lastName'=>$_POST['RegisterForm']['lastName'],
					'displayName'=>$_POST['RegisterForm']['displayName'],
					'user_role'=>1,
					'status'=>0
				);
				
				$resp = $captcha->recaptcha_check_answer(Yii::app()->params['reCaptchaPrivateKey'], $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);

				if (!$resp->is_valid) {
					$error = 'The CAPTCHA that you entered was invalid. Please try again';
					$this->render('register', array('captcha'=>$captcha, 'model'=>$model, 'error'=>$error, 'user'=>$user));
					return;
				} 
				
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
						
						// Create the message to be sent to the user
						$message = "
						{$user->displayName},<br /><br />
						Thanks for registering your account! To activate your account, " . CHtml::link('click here', Yii::app()->createAbsoluteUrl('/activation/'.$user->id.'/'.$hash)) .
						".<br /><br />Thank you,<br />" . Yii::app()->name . 
						" Team<br /><br />P.S. If you did not request this email, you may safely ignore it.";
						
						// Send email
						Yii::import('application.extensions.phpmailer.JPhpMailer');
						$mail = new JPhpMailer;
						$mail->IsSMTP();
						$mail->SetFrom(Configuration::model()->findByAttributes(array('key'=>'adminEmail'))->value, Yii::app()->name . ' Administrator');
						$mail->Subject = 'Activate Your Account';
						$mail->MsgHTML($message);
						$mail->AddAddress($user->email, $user->displayName);
						$mail->Send();
					
						$this->render('register-success');
					}
				}
				catch(CDbException $e) 
				{
					$model->addError(null, 'The email address has already been associated to an account. Do you want to login instead?');
				}
			}
		}
		$this->render('register', array('captcha'=>$captcha, 'model'=>$model, 'error'=>$error, 'user'=>$user));
	}
}
