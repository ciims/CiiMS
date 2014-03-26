<?php

class DefaultController extends CiiController
{
	/**
	 * Disable filters. This should always return a valid non 304 response
	 */
	public function filters()
	{
		return array();
	}
	
	public function actionIndex($provider=NULL)
	{
		if ($provider == 'callback')
			$this->callback();
		
		try {
			$this->hybridAuth($provider);
		} catch (Exception $e) { 
			throw new CHttpException(400, Yii::t('Hybridauth.main', 'Oh Snap! Something went wrong. Please try again later.')); 
		}

		return;
	}

	/**
	 * Main mehod to handle login attempts.  If the user passes authentication with their
	 * chosen provider then it displays a form for them to choose their username and email.
	 * The email address they choose is *not* verified.
	 * 
	 * @throws Exception if a provider isn't supplied, or it has non-alpha characters
	 */
	private function hybridAuth($provider=NULL)
	{
		if ($provider==NULL)
			throw new CException(Yii::t('Hybridauth.main', "You haven't supplied a provider"));

		$identity = new RemoteUserIdentity();

		if ($identity->authenticate($provider))
		{
			// If we found a user and authenticated them, bind this data to the user if it does not already exist
			$user = UserMetadata::model()->findByAttributes(array('key'=>$provider.'Provider', 'value'=>$identity->userData['id']));
			if ($user === NULL)
			{
				$user = new UserMetadata;
				$user->user_id = Users::model()->findByAttributes(array('email'=>$identity->userData['email']))->id;
				$user->key = $provider.'Provider';
				$user->value = $identity->userData['id'];
				$user->save();
			}
			
			$user = Users::model()->findByPk($user->user_id);

			// Log the user in with just their email address
			$model=new LoginForm(true);

			// If the prevvious authentication failed, then the user has been upgraded, and we should attempt to use the bcrypt hash isntead of the md5 one
			$model->attributes=array(
				'username'=>isset($user->email) ? $user->email : $identity->userData['email'],
				'password'=>password_hash($identity->userData['email'], PASSWORD_BCRYPT, array('cost' => 13)),
			);

			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);

			throw new CException(Yii::t('Hybridauth.main', 'Unable to bind to local user'));
		}
		else if ($identity->errorCode == RemoteUserIdentity::ERROR_USERNAME_INVALID)
		{
			// If the user authenticated against the remote network, but we didn't find them locally
			// Create a local account, and bind this information to it.
			$user = new Users;
			$user->attributes = array(
					'email'=>$identity->userData['email'],
					'password'=>password_hash($identity->userData['email'], PASSWORD_BCRYPT, array('cost' => 13)),
					'firstName'=>Cii::get($identity->userData, 'firstName', 'UNKNOWN'),
					'lastName'=>Cii::get($identity->userData, 'lastName', 'UNKNOWN'),
					'displayName'=>($provider == 'twitter' ? $identity->userData['firstName'] : $identity->userData['displayName']),
					'user_role'=>1,
					'status'=>1
				);
			
			$user->save();
			
			$meta = new UserMetadata;
			$meta->user_id = $user->id;
			$meta->key = $provider.'Provider';
			$meta->value = $identity->userData['id'];
			$meta->save();
			
			// Log the user in with just their email address
			$model=new LoginForm(true);
			
			$model->attributes=array(
				'username'=>$identity->userData['email'],
				'password'=>password_hash($identity->userData['email'], PASSWORD_BCRYPT, array('cost' => 13)),
			);

			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);

			throw new CException(Yii::t('Hybridauth.main', 'Unable to bind new user locally'));
		}
		else 
		{
			// Panic?	
			throw new CException(Yii::t('Hybridauth.main', 'We were able to authenticate you against the remote network, but could not sign you in locally.'));
		}
	}

	/** 
	 * Action for URL that Hybrid_Auth redirects to when coming back from providers.
	 * Calls Hybrid_Auth to process login. 
	 */
	private function callback()
	{
		Hybrid_Endpoint::process();
	}

}