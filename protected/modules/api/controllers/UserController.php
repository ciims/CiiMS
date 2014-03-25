<?php

class UserController extends ApiController
{
	/**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {   
        return array(
        	array('allow',
        		'actions' => array('tokenPost')
        	),
        	array('allow',
        		'actions' => array('tokenDelete'),
        		'expression' => '$user!=NULL'
        	),
            array('allow',
                'actions' => array('index', 'indexPost'),
                'expression' => '$user!=NULL&&($user->isSiteManager()||$user->isAdmin()||||(Yii::app()->request->getParam("id")==$user->id))'
            ),
            array('deny') 
        );  
    }

    /**
     * [POST] [/user/token]
     * Allows for the generation of new LL API Token
     * @return array
     */
    public function actionTokenPost()
    {
    	$model = new LoginForm;
    	$model->username = Cii::get($_POST, 'email');
    	$model->password = Cii::get($_POST, 'password');

    	if (Cii::get($_POST, 'name', NULL) == NULL)
    		throw new CHttpException(400, Yii::t('Api.user', 'Application name must be defined.'));
    	else
    		$model->app_name = Cii::get($_POST, 'name', 'api');

    	if ($model->validate())
    	{
    		if ($model->login())
    			return UserMetadata::model()->findByAttributes(array('user_id' => Users::model()->findByAttributes(array('email' => $_POST['email']))->id, 'key' => 'api_key' . $_POST['name']))->value;
    	}

    	throw new CHttpException(403, Yii::t('Api.user', 'Unable to authenticate.'));
    }

    /**
     * [DELETE] [/user/token]
     * Allows for the deletion of the active API token
     * @return array
     */
    public function actionTokenDelete()
    {
    	$model = UserMetadata::model()->findByAttributes(array('user_id' => $this->user->id, 'value' => $this->xauthtoken));

    	if ($model === NULL)
    		throw new CHttpException(500, Yii::t('Api.user', 'An unexpected error occured while deleting the token. Please re-generate a new token for subsequent requests.'));
    	return $model->delete();
    }

	/**
	 * [GET] [/user/<id>]
	 * @return array    List of users
	 */
	public function actionIndex($id=NULL)
	{
        if ($id !== NULL)
        {
            $user = Users::model()->findByPk($id);
            if ($user == NULL)
                throw new CHttpException(404, Yii::t('Api.user', 'A user with the id of {{id}} was not found.', array('{{id}}' => $id)));

            return $user->getAPIAttributes();
		}
        
        $users = Users::model()->findAll();
		$response = array();

		foreach ($users as $user)
			$response[] = $user->getAPIAttributes(array('password', 'activation_key'));

		return $response;
	}

	/**
	 * [POST] [/user/<id>]
	 * @return array    Updated user details
	 */
	public function actionIndexPost($id=NULL)
	{
		if ($id === NULL)
		{
			if ($this->user->user_role==6||$this->user->user_role==9)
				return $this->createUser();
			else
				throw new CHttpException(403, Yii::t('Api.user', 'You do not have sufficient privileges to create a new user.'));
		}
		else
			return $this->updateUser($id);
	}

	/**
	 * Updates the attributes for a given user with $id. Administrators can always access this method. Users can also edit their own information
	 * @param  int    $id The user's ID
	 * @return array
	 */
	public function updateUser($id)
	{
		// Verify a user with that ID exists
		$user = Users::model()->findByPk($id);
		if ($user === NULL)
			throw new CHttpException(404, Yii::t('Api.user', 'A user with the id of {{id}} was not found.', array('{{id}}' => $id)));

		// Load the bcrypt hashing tools if the user is running a version of PHP < 5.5.x
		if (!function_exists('password_hash'))
			require_once YiiBase::getPathOfAlias('ext.bcrypt.bcrypt').'.php';

		$cost = Cii::getBcryptCost();

		// If the password is set, permit it to be changed
		if (Cii::get($_POST, 'password', NULL) != NULL)
			$_POST['password'] = password_hash(Users::model()->encryptHash(Cii::get($_POST, 'email', $user->email), Cii::get($_POST, 'password', NULL), Yii::app()->params['encryptionKey']), PASSWORD_BCRYPT, array('cost' => $cost));
		else
			unset($_POST['password']);

		unset($_POST['activation_key']);
		if ($this->user->user_role!=6 && $this->user->user_role!=9)
		{
			unset($_POST['status']);
			unset($_POST['user_role']);
		}

		$user->attributes=$_POST;

		if($user->save())
			return $user->getAPIAttributes(array('password', 'activation_key'));

		return $this->returnError(400, NULL, $user->getErrors());
	}

	/** 
	 * Provides functionality to create a new user. This method will create a new user if the user does not already exist.
	 * And then it will send an email invitation to the user so that they can join the blog.
	 * @return array
	 */
	private function createUser()
	{
		$validator=new CEmailValidator;
        if (!$validator->validateValue(Cii::get($_POST, 'email', NULL)))
			throw new CHttpException(400, Yii::t('Api.user', 'The email address you provided is invalid.'));

		if (Users::model()->countByAttributes(array('email' => Cii::get($_POST, 'email', NULL))))
			throw new CHttpException(400, Yii::t('Api.user', 'A user with that email address already exists.'));

		// Passowrds cannot be set through the API
		unset($_POST['password']);

		// Relational data cannot be set through this API
		unset($_POST['comments']);
		unset($_POST['content']);
		unset($_POST['tags']);
		unset($_POST['metadata']);
		unset($_POST['role']);

		$user = new Users;
		$user->attributes = array(
			'status' => Users::PENDING_INVITATION,
			'email' => Cii::get($_POST, 'email', NULL),
			'user_role' => 1,
			'about' => '',
			'password' => '',
			'displayName' => '',
			'firstName' => '',
			'lastName' => '',
		);

		$user->attributes = $_POST;

		$user->created = new CDbExpression('UTC_TIMESTAMP()');
		$user->updated =  new CDbExpression('UTC_TIMESTAMP()');

		// Save the user, and ignore all validation
		if ($user->save(false))
		{
			$hash = mb_strimwidth(hash("sha256", md5(time() . md5(hash("sha512", time())))), 0, 16);
			$meta = new UserMetadata;
			$meta->user_id = $user->id;
			$meta->key = 'activationKey';
			$meta->value = $hash;
			$meta->save();

			// Send an invitation email
			$this->sendEmail($user, Yii::t('Api.user', "You've Been Invited To Join a Blog!"), 'application.modules.dashboard.views.email.invite', array('user' => $user, 'hash' => $hash), true, true);

			// End the request
			return $user->getAPIAttributes(array('password', 'activation_key'));
		}

		throw new CHttpException(400, Yii::t('Api.user', 'An unexpected error occured fulfilling your request.'));
	}
}
