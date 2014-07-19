<?php

class SiteController extends CiiController
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
		if (!Yii::app()->getRequest()->isSecureConnection && Cii::getConfig('forceSecureSSL', false))
            $this->redirect('https://' . Yii::app()->getRequest()->serverName . Yii::app()->getRequest()->requestUri);

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
				echo $error['message'];
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
     * Provides basic sitemap functionality via XML
     */
	public function actionSitemap()
	{
		ob_end_clean();
		Yii::app()->log->routes[0]->enabled = false; 
		header('Content-type: text/xml; charset=utf-8');
		$url = 'http://'.Yii::app()->request->serverName . Yii::app()->baseUrl;
		$this->setLayout(null);
		$content = Yii::app()->db->createCommand('SELECT slug, password, type_id, updated FROM content AS t WHERE vid=(SELECT MAX(vid) FROM content WHERE id=t.id) AND status = 1 AND published <= UTC_TIMESTAMP();')->queryAll();
		$categories = Yii::app()->db->createCommand('SELECT slug, updated FROM categories;')->queryAll();
		$this->renderPartial('sitemap', array('content'=>$content, 'categories'=>$categories, 'url' => $url));
		Yii::app()->end();
	}

    /**
     * Provides basic searching functionality
     * @param int $id   The search pagination id
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

		if (Cii::get($_GET, 'q', false))
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

		if(Cii::get($_POST, 'LoginForm', false))
		{
			$model->attributes = Cii::get($_POST, 'LoginForm', array());

			if($model->login())
				$this->redirect(isset($_GET['next']) ? $_GET['next'] : Yii::app()->user->returnUrl);
		}

		$this->render('login',array('model'=>$model));
	}

    /**
     * Provides functionality to log a user out
     */
	public function actionLogout()
	{
		if (Yii::app()->request->getParam('next', false))
			$redirect = $this->createUrl('site/login', array('next' => Yii::app()->request->getParam('next')));
		else
			$redirect = Yii::app()->user->returnUrl;
		
		// Purge the active sessions API Key
		$apiKey = UserMetadata::model()->findByAttributes(array('user_id' => Yii::app()->user->id, 'key' => 'api_key'));
	  	if ($apiKey != NULL)
	  		$apiKey->delete();


		Yii::app()->user->logout();
		$this->redirect($redirect);
	}

	/**
	 * Handles resetting a users password should they forgot it
	 * @param hash $id
	 */
    public function actionForgot()
    {
        $this->layout = '//layouts/main';

        $this->setPageTitle(Yii::t('ciims.controllers.Site', '{{app_name}} | {{label}}', array(
			'{{app_name}}' => Cii::getConfig('name', Yii::app()->name),
			'{{label}}'    => Yii::t('ciims.controllers.Site', 'Forgot Your Password?')
		)));

        $model = new ForgotForm;

        if (Cii::get($_POST, 'ForgotForm', false))
        {
            $model->attributes = $_POST['ForgotForm'];

            if ($model->initPasswordResetProcess())
            {
                Yii::app()->user->setFlash('success', Yii::t('ciims.controllers.Site', 'A password reset link has been sent to your email address'));
                $this->redirect($this->createUrl('site/login'));
            }
        }

        $this->render('forgot', array('model' => $model));
    }

    /**
     * Alows a user to reset their password if they initiated a forgot password request
     * @param string $id
     */
    public function actionResetPassword($id=NULL)
    {
        $this->layout = '//layouts/main';

        $this->setPageTitle(Yii::t('ciims.controllers.Site', '{{app_name}} | {{label}}', array(
			'{{app_name}}' => Cii::getConfig('name', Yii::app()->name),
			'{{label}}'    => Yii::t('ciims.controllers.Site', 'Reset Your password')
		)));

        $model = new PasswordResetForm;
        $model->reset_key = $id;

        if (!$model->validateResetKey())
            throw new CHttpException(403, Yii::t('ciims.controllers.Site', 'The password reset key provided is invalid'));

        if (Cii::get($_POST, 'PasswordResetForm', false))
        {
            $model->attributes = $_POST['PasswordResetForm'];

            if ($model->save())
            {
                Yii::app()->user->setFlash('success', Yii::t('ciims.controllers.Site', 'Your password has been reset, and you may now login with your new password'));
                $this->redirect($this->createUrl('site/login'));
            }
        }

        $this->render('resetpassword', array('model' => $model));
    }

	/**
	 * Allows the user to securely change their email address
	 * @param  string $key the user's secure key
	 */
	public function actionEmailChange($key=null)
	{
		$this->layout = '//layouts/main';

		$this->setPageTitle(Yii::t('ciims.controllers.Site', '{{app_name}} | {{label}}', array(
			'{{app_name}}' => Cii::getConfig('name', Yii::app()->name),
			'{{label}}'    => Yii::t('ciims.controllers.Site', 'Change Your Email Address')
		)));

        $model = new EmailChangeForm;
        $model->verificationKey = $key;

        if (!$model->validateVerificationKey())
            throw new CHttpException(403, Yii::t('ciims.controllers.Site', 'The verification key provided is invalid.'));

        if (Cii::get($_POST, 'EmailChangeForm', false))
        {
            $model->attributes = $_POST['EmailChangeForm'];

            if ($model->save())
            {
                Yii::app()->user->logout();
                Yii::app()->user->setFlash('success', Yii::t('ciims.controllers.Site', 'Your new email address has been verified.'));

                $this->redirect($this->createUrl('profile/edit'));
            }
        }

        $this->render('emailchange', array('model' => $model));
	}

	/**
	 * Activates a new user's account
	 * @param int $id 			The activation key
	 */
	public function actionActivation($id=NULL)
	{
		$this->layout = '//layouts/main';

		$this->setPageTitle(Yii::t('ciims.controllers.Site', '{{app_name}} | {{label}}', array(
			'{{app_name}}' => Cii::getConfig('name', Yii::app()->name),
			'{{label}}'    => Yii::t('ciims.controllers.Site', 'Activate Your Account')
		)));

		$model = new ActivationForm;
        $model->activationKey = $id;

        if (!$model->validateKey())
            throw new CHttpException(403, Yii::t('ciims.models.ActivationForm', 'The activation key you provided is invalid.'));

        if (Cii::get($_POST, 'ActivationForm', false))
        {
            $model->attributes = $_POST['ActivationForm'];

            if ($model->save())
            {
                Yii::app()->user->setFlash('success', Yii::t('ciims.controllers.Site', 'Your account has successfully been activated. You may now login'));
                $this->redirect($this->createUrl('site/login'));
            }
        }

		$this->render('activation', array('model' => $model));
	}

	/**
	 * Handles the registration of new users on the site
	 */
	public function actionRegister()
	{
		$this->layout = '//layouts/main';

		$this->setPageTitle(Yii::t('ciims.controllers.Site', '{{app_name}} | {{label}}', array(
			'{{app_name}}' => Cii::getConfig('name', Yii::app()->name),
			'{{label}}'    => Yii::t('ciims.controllers.Site', 'Sign Up')
		)));

		$model = new RegisterForm;

		if (Cii::get($_POST, 'RegisterForm', false))
		{
			$model->attributes = $_POST['RegisterForm'];

            // Save the user's information
			if ($model->save())
		    {
                // Set a flash message
                Yii::app()->user->setFlash('success', Yii::t('ciims.controllers.Site', 'You have successfully registered an account. Before you can login, please check your email for activation instructions'));
                $this->redirect($this->createUrl('site/login'));
            }
		}

		$this->render('register', array('model'=>$model));
	}

	/**
	 * Enables users who have recieved an invitation to setup a new account
	 * @param string $id	The activation id the of the user that we want to activate
	 */
	public function actionAcceptInvite($id=NULL)
	{
		$this->layout = '//layouts/main';

        $this->setPageTitle(Yii::t('ciims.controllers.Site', '{{app_name}} | {{label}}', array(
            '{{app_name}}' => Cii::getConfig('name', Yii::app()->name),
            '{{label}}'    => Yii::t('ciims.controllers.Site', 'Accept Invitation')
        )));

		if ($id == NULL)
			throw new CHttpException(400, Yii::t('ciims.controllers.Site', 'There was an error fulfilling your request.'));

		// Make sure we have a user first
		$meta = UserMetadata::model()->findByAttributes(array('key' => 'invitationKey', 'value' => $id));
		if ($meta == NULL)
			throw new CHttpException(400, Yii::t('ciims.controllers.Site', 'There was an error fulfilling your request.'));

		$model = new InviteForm;
		$model->email = Users::model()->findByPk($meta->user_id)->email;

		if (Cii::get($_POST, 'InviteForm', NULL) != NULL)
		{
			$model->attributes = Cii::get($_POST, 'InviteForm', NULL);
			$model->id = $meta->user_id;

			if ($model->acceptInvite())
			{
				$meta->delete();
				return $this->render('invitesuccess');
			}
		}

		$this->render('acceptinvite', array('model' => $model));
	}
}
