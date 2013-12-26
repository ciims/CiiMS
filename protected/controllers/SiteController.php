<?php

class SiteController extends CiiSiteController
{
	public function filters()
	{
		return CMap::mergeArray(parent::filters(), array('accessControl'));
	}

	/**
	 * Setup access controls to prevent guests from changing their emaila ddress
	 */
	public function accessRules()
	{
		return array(
			array('deny',  // allow authenticated admins to perform any action
				'users'=>array('*'),
				'expression'=>'Yii::app()->user->isGuest==true',
				'actions' => array('emailchange')
			)
		);
	}

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
	public function actionError($code=NULL)
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
				$this->setPageTitle(Yii::t('ciims.controllers.Site', '{{app_name}} | {{label}} {{code}}', array(
					'{{app_name}}' => Cii::getConfig('name', Yii::app()->name),
					'{{label}}'    => Yii::t('ciims.controllers.Site', 'Error'),
					'{{code}}'     => $error['code']
				)));

				$this->render('error', array('error'=>$error));
			}
		}
		else
		{
			$message = Yii::app()->user->getFlash('error_code');
			Yii::app()->user->setFlash('error_code', $message);
			throw new CHttpException($code, $message);
		}
	}

	/**
	 * Allows themes to have their own dedicated callback resources.
	 *
	 * This enables theme developers to not have to hack CiiMS Core in order to accomplish stuff
	 * @param  string $method The method of the current theme they want to call
	 * @return The output or action of the callback
	 */
	public function actionThemeCallback($method)
	{
		$currentTheme = Yii::app()->getTheme()->name;

		Yii::import('webroot.themes.' . $currentTheme . '.Theme');
		$theme = new Theme();

		return $theme->$method($_POST);
	}
	
    /**
     * Provides basic sitemap functionality via XML
     */
	public function actionSitemap()
	{
		ob_end_clean();
		header('Content-type: text/xml; charset=utf-8');
		$url = 'http://'.Yii::app()->request->serverName . Yii::app()->baseUrl;
		$this->setLayout(null);
		$content = Yii::app()->db->createCommand('SELECT slug, password, type_id, updated FROM content AS t WHERE vid=(SELECT MAX(vid) FROM content WHERE id=t.id) AND status = 1 AND published <= UTC_TIMESTAMP();')->queryAll();
		$categories = Yii::app()->db->createCommand('SELECT slug, updated FROM categories;')->queryAll();
		$this->renderPartial('sitemap', array('content'=>$content, 'categories'=>$categories, 'url' => $url));
		//Yii::app()->end();
	}
	
    /**
     * Provides search functionality through Sphinx
     * @param int $id   The pagination $id
     */
	public function actionSearch($id=1)
	{
		$this->setPageTitle(Yii::t('ciims.controllers.Site', '{{app_name}} | {{label}}', array(
			'{{app_name}}' => Cii::getConfig('name', Yii::app()->name),
			'{{label}}'    => Yii::t('ciims.controllers.Site', 'Search')
		)));

		$this->layout = '//layouts/default';
		$data = array();
		$pages = array();
		$itemCount = 0;
		$pageSize = Cii::getConfig('searchPaginationSize', 10);
		
		if (Cii::get($_GET, 'q', "") != "")
		{	
			$criteria = Content::model()->getBaseCriteria();

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
				$sphinx->setServer(Cii::getConfig('sphinxHost'), (int)Cii::getConfig('sphinxPort'));
				$sphinx->setMatchMode(SPH_MATCH_EXTENDED2);
				$sphinx->setMaxQueryTime(15);
				$result = $sphinx->query(Cii::get($_GET, 'q', NULL), Cii::getConfig('sphinxSource'));	
				$criteria->addInCondition('id', array_keys(isset($result['matches']) ? $result['matches'] : array()));
				
    		}	

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
		
		$this->render('search', array('url' => 'search', 'id'=>$id, 'data'=>$data, 'itemCount'=>$itemCount, 'pages'=>$pages));
	}

    /**
     * Provides basic MySQL searching functionality
     * @param int $id   The search pagination id
     */
	public function actionMySQLSearch($id=1)
	{
		$this->setPageTitle(Yii::t('ciims.controllers.Site', '{{app_name}} | {{label}}', array(
			'{{app_name}}' => Cii::getConfig('name', Yii::app()->name),
			'{{label}}'    => Yii::t('ciims.controllers.Site', 'Search')
		)));

		$this->layout = '//layouts/default';
		$data = array();
		$pages = array();
		$itemCount = 0;
		$pageSize = Cii::getConfig('searchPaginationSize', 10);
		
		if (Cii::get($_GET, 'q', "") != "")
		{	
			$criteria = new CDbCriteria;
			$criteria->addCondition('status = 1')
		         	 ->addCondition('published <= UTC_TIMESTAMP()');

			if (strpos($_GET['q'], 'user_id') !== false)
			{
				$criteria->addCondition('author_id = :author_id')
						 ->addCondition("vid=(SELECT MAX(vid) FROM content AS v WHERE v.id=t.id)");
				$criteria->params = array(
					':author_id' => str_replace('user_id:', '', Cii::get($_GET, 'q', 0))
				);
			}
			else
			{
				$param = Cii::get($_GET, 'q', 0);
				$criteria->addCondition("vid=(SELECT MAX(vid) FROM content AS v WHERE v.id=t.id) AND ((t.content LIKE :param) OR (t.title LIKE :param2))");
				$criteria->params = array(
					':param' => '%' . $param . '%',
					':param2' =>'%' . $param . '%'
				);
    		}	

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
		
		$this->render('search', array('url' => 'search', 'id'=>$id, 'data'=>$data, 'itemCount'=>$itemCount, 'pages'=>$pages));
	}
	
    /**
     * Provides functionality to log a user into the system
     */
	public function actionLogin()
	{
		$this->setPageTitle(Yii::t('ciims.controllers.Site', '{{app_name}} | {{label}}', array(
			'{{app_name}}' => Cii::getConfig('name', Yii::app()->name),
			'{{label}}'    => Yii::t('ciims.controllers.Site', 'Login to your account')
		)));

		$this->layout = '//layouts/main';
		$model=new LoginForm;

		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
            
			if($model->validate() && $model->login())
				$this->redirect(isset($_GET['next']) ? $_GET['next'] : Yii::app()->user->returnUrl);
		}
        
		$this->render('login',array('model'=>$model));
	}
	
    /**
     * Provides functionality to log a user out
     */
	public function actionLogout()
	{
		// Pure the active sessions API Key
		$apiKey = UserMetadata::model()->findByAttributes(array('user_id' => Yii::app()->user->id, 'key' => 'api_key'));
	  	if ($apiKey != NULL)
	  		$apiKey->delete();

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
					Yii::app()->user->setFlash('reset-error', Yii::t('ciims.controllers.Site', 'The email your provided is not a valid email address.'));
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
					

					$this->sendEmail($user, Yii::t('ciims.email', 'Your Password Reset Information'), '//email/forgot', array('user' => $user, 'hash' => $hash), true, true);
					
					// Set success flash
					Yii::app()->user->setFlash('reset-sent', Yii::t('ciims.controllers.Site', 'An email has been sent to {{email}} with further instructions on how to reset your password', array(
						'{{email}}' => Cii::get($_POST, 'email', NULL)
					)));
				}
				else
				{
					Yii::app()->user->setFlash('reset-sent', Yii::t('ciims.controllers.Site', 'An email has been sent to {{email}} with further instructions on how to reset your password', array(
						'{{email}}' => Cii::get($_POST, 'email', NULL)
					)));

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
							Yii::app()->user->setFlash('reset', Yii::t('ciims.controllers.Site', 'Your password has been reset, and you may now login with your new password'));
							
							// Redirect to the login page
							$this->redirect('/login');
						}
	
						Yii::app()->user->setFlash('reset-error', Yii::t('ciims.controllers.Site', 'The password you provided must be at least 8 characters.'));
						$this->render('forgot', array('id'=>$id, 'badHash'=>false));
						return;
					}
					
					Yii::app()->user->setFlash('reset-error', Yii::t('ciims.controllers.Site', 'The passwords you provided do not match.'));
					$this->render('forgot', array('id'=>$id, 'badHash'=>false));
					return;
				}
				
				Yii::app()->user->setFlash('reset-error', Yii::t('ciims.controllers.Site', 'You must provide your password twice for us to reset your password.'));
				$this->render('forgot', array('id'=>$id, 'badHash'=>false));
				return;
			}
		}
		$this->render('forgot', array('id'=>$id, 'badHash'=>false));
	}
	
	/**
	 * Allows the user to securely change their email address
	 * @param  string $key the user's secure key
	 */
	public function actionEmailChange($key=null)
	{
		$this->setPageTitle(Yii::t('ciims.controllers.Site', '{{app_name}} | {{label}}', array(
			'{{app_name}}' => Cii::getConfig('name', Yii::app()->name),
			'{{label}}'    => Yii::t('ciims.controllers.Site', 'Change Your Email Address')
		)));

		$success = false;

		$this->layout = '//layouts/main';

		if ($key == NULL)
			throw new CHttpException(400, Yii::t('ciims.controllers.Site', 'You are not authorized to change this email address.'));

		$meta = UserMetadata::model()->findByAttributes(array('key' => 'newEmailAddressChangeKey', 'value' => $key));

		if ($meta == NULL || $meta->value != $key)
			throw new CHttpException(400, Yii::t('ciims.controllers.Site', 'You are not authorized to change this email address.'));

		$threeDays = 259200;
		if ((strtotime($meta->created) + $threeDays) > time())
		{
			try {
				// Delete the associated metadata when this error is thrown
				$meta2 = UserMetadata::model()->findByAttributes(array('key' => 'newEmailAddress', 'user_id' => $meta->user_id));
				$meta2->delete();
				$meta->delete();
			} catch (Exception $e) {}

			throw new CHttpException(400, Yii::t('ciims.controllers.Site', 'The request to change your email has expired.'));
		}

		$user = Users::model()->findByPk($meta->user_id);

		if ($user->id != Yii::app()->user->id)
			throw new CHttpException(400, Yii::t('ciims.controllers.Site', 'You are not authorized to change this email address.'));

		if (Cii::get($_POST, 'password') !== NULL)
		{
			// Retrieve the user's NEW email address
			$meta = UserMetadata::model()->findByAttributes(array('key' => 'newEmailAddress', 'user_id' => $meta->user_id));

			$identity = new UserIdentity($user->email, $_POST['password']);

			// If we can authenticate that the user wants to change their email address
			if ($identity->authenticate())
			{
				// Generate a new hash for the user
				$email = $meta->value;
				$id = $meta->user_id;
				$password = $identity->updatePassword($email, $_POST['password']);

				// Manually update the db via CActiveDataProvider, since Users::beforeSave() will block the request
				try {
					$ret = Yii::app()->db->createCommand('UPDATE users SET email = :email, password = :password WHERE id = :id')
								  ->bindParam(':id', $id)
								  ->bindParam(':password', $password)
								  ->bindParam(':email', $email)
								  ->execute();
					
					$success = Yii::t('ciims.controllers.Site', 'Your email address has been sucessfully changes. Please {{login}} again for the changes to take effect.', array('{{login}}' => CHtml::link(Yii::t('ciims.controllers.Site', 'Login'), Yii::app()->createUrl('/login'))));

					// Kill the users session and force them to re-authenticate with their new credentials.
					Yii::app()->user->logout();
				} catch (Exception $e) {
					// This error indicates
					Yii::app()->user->setFlash('authenticate-error', Yii::t('ciims.controllers.Site', 'The requested email address has already been taken. Please re-submit your request with a new email address.'));
				}

				// Delete the metadata and ignore any errors as they aren't really
				try {
					$meta->delete();
					UserMetadata::model()->findByAttributes(array('key' => 'newEmailAddressChangeKey', 'value' => $key))->delete();
					UserMetadata::model()->findByAttributes(array('user_id' => $id, 'key' => 'newEmailAddressChangeKeyTime'))->delete();
				} catch (Exception $e) {}
			}
			else
				Yii::app()->user->setFlash('authenticate-error', Yii::t('ciims.controllers.Site', 'We were unable to verify your current password. Please verify your password and try again.'));
		}


		$this->render('emailchange', array('key' => $key, 'success' => $success));

	}

	/**
	 * Activation handler
	 * @param string $email 	The user's email
	 * @param int $id 			The activation key
	 */
	public function actionActivation($email=NULL, $id=NULL) 
	{
		$this->layout = '//layouts/main';

		$this->setPageTitle(Yii::t('ciims.controllers.Site', '{{app_name}} | {{label}}', array(
			'{{app_name}}' => Cii::getConfig('name', Yii::app()->name),
			'{{label}}'    => Yii::t('ciims.controllers.Site', 'Activate Your Account')
		)));

		if ($id != NULL || $email = NULL)
		{
			$record = $user = Users::model()->findByPk($email);
			if ($user != NULL && $user->status == Users::INACTIVE)
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
							$user->status = Users::ACTIVE;
							$user->save();
							
							// Delete the activationKey
							$meta->delete();
							Yii::app()->user->setFlash('activation-success', Yii::t('ciims.controllers.Site', 'Activation was successful! You may now {{login}}', array(
								'{{login}}' => CHtml::link(Yii::t('ciims.controllers.Site', 'login'), $this->createUrl('/login')))));
							return $this->render('activation');
						}
						else
						{
							Yii::app()->user->setFlash('activation-error', Yii::t('ciims.controllers.Site', 'Please provide the password you used during the signup process.'));
						}
					}

					Yii::app()->user->setFlash('activation-info', Yii::t('ciims.controllers.Site', 'Enter the password you used to register your account to verify your email address.'));
				}
				else
				{
					Yii::app()->user->setFlash('activation-error', Yii::t('ciims.controllers.Site', 'The activation key your provided was invalid.'));
				}
			}
			else
			{
				Yii::app()->user->setFlash('activation-error', Yii::t('ciims.controllers.Site', 'Unable to activate user using the provided details.'));
			}
		}
		else
		{
			Yii::app()->user->setFlash('activation-error', Yii::t('ciims.controllers.Site', 'The activation key your provided was invalid.'));
		}
		
		$this->render('activation');
	}
	
	/**
	 * Registration page
	 *
	 **/
	public function actionRegister()
	{
		$this->setPageTitle(Yii::t('ciims.controllers.Site', '{{app_name}} | {{label}}', array(
			'{{app_name}}' => Cii::getConfig('name', Yii::app()->name),
			'{{label}}'    => Yii::t('ciims.controllers.Site', 'Sign Up')
		)));

		$this->layout = '//layouts/main';
		$model = new RegisterForm;
		$user = new Users;
		
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
					'status'=>Users::INACTIVE
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
						$this->sendEmail($user, Yii::t('ciims.email','Activate Your Account'), '//email/register', array('user' => $user, 'hash' => $hash), true, true);
					
						$this->redirect($this->createUrl('/register-success'));
						return;
					}
				}
				catch(CDbException $e) 
				{
					$model->addError(null, Yii::t('ciims.controllers.Site','The email address has already been associated to an account. Do you want to login instead?'));
				}
			}
		}

		$this->render('register', array('model'=>$model, 'error'=>$error, 'user'=>$user));
	}

	/**
	 * Handles successful registration
	 */
	public function actionRegisterSuccess()
	{
		$this->setPageTitle(Yii::t('ciims.controllers.Site', '{{app_name}} | {{label}}', array(
			'{{app_name}}' => Cii::getConfig('name', Yii::app()->name),
			'{{label}}'    => Yii::t('ciims.controllers.Site', 'Registration Successful')
		)));

		$notifyUser  = new stdClass;
		$notifyUser->email       = Cii::getConfig('notifyEmail', NULL);
		$notifyUser->displayName = Cii::getConfig('notifyName',  NULL);

		if ($notifyUser->email == NULL && $notifyUser->displayName == NULL)
		    $notifyUser = Users::model()->findByPk(1);

		$this->layout = '//layouts/main';
		$this->render('register-success', array('notifyUser' => $notifyUser));
	}

	/**
	 * Enables users who have recieved an invitation to setup a new account
	 * @param string $id	The activation id the of the user that we want to activate
	 */
	public function actionAcceptInvite($id=NULL)
	{
		$this->layout = "main";
		if ($id == NULL)
			throw new CHttpException(400, Yii::t('ciims.controllers.Site', 'There was an error fulfilling your request.'));

		// Make sure we have a user first
		$meta = UserMetadata::model()->findByAttributes(array('key' => 'activationKey', 'value' => $id));
		if ($meta == NULL)
			throw new CHttpException(400, Yii::t('ciims.controllers.Site', 'There was an error fulfilling your request.'));

		$model = new InviteModel;
		
		$model->email = Users::model()->findByPk($meta->user_id)->email;
		if (Cii::get($_POST, 'InviteModel', NULL) != NULL)
		{
			$model->attributes = Cii::get($_POST, 'InviteModel', NULL);
		
			if ($model->save($meta->user_id))
			{
				$meta->delete();
				return $this->render('invitesuccess');	
			}	
		}
		
		$this->render('acceptinvite', array('model' => $model));
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
