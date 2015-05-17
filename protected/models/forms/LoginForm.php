<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
Yii::import('cii.components.CiiUserIdentity');
class LoginForm extends CFormModel
{
	/**
	 * The submitted username(email)
	 * @var string
	 */
	public $username;

	/**
	 * The submitted password
	 * @var string
	 */
	public $password;

	/**
	 * Whether or not we should remember the user.
	 * This isn't uses as of CiiMS 1.1
	 * @var boolean ?
	 */
	public $rememberMe = true;

	/**
	 * Whether or not we should perform a forced authentication. By default we aren't going to do this
	 * @var boolean
	 */
	private $force = false;

	/**
	 * The identity of the user
	 * @var CiiUserIdentity
	 */
	private $_identity;

	/**
	 * The Application Name (??)
	 * // TODO: Remember what this is used for
	 * @var $app_name
	 */
	public $app_name = NULL;

	/**
	 * Determines whether or not we should do a forced authentication and bypass the user's actual password
	 * @see  application.modules.HybridAuth for more details
	 * @param boolean $force
	 */
	public function __construct($force=false)
	{
		$this->force = $force;
	}

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			array('username, password', 'required'),
			array('username', 'email'),
			array('password', 'authenticate'),
		);
	}

	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute,$params)
	{
		if(!$this->hasErrors())
		{
			$this->_identity = new CiiUserIdentity($this->username,$this->password);
			$this->_identity->app_name = $this->app_name;
			if(!$this->_identity->authenticate($this->force))
				$this->addError('password', Yii::t('ciims.models.LoginForm', 'Incorrect username or password.'));
		}
	}

	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if (!$this->validate())
			return false;

		if($this->_identity===null)
		{
			$this->_identity=new CiiUserIdentity($this->username,$this->password);
			$this->_identity->authenticate(); 
		}
			
		if($this->_identity->errorCode===CiiUserIdentity::ERROR_NONE)
		{
			$duration=$this->rememberMe ? 3600*24 : 0; // 30 days
			Yii::app()->user->login($this->_identity,$duration);
			
			// Store the API key and session_identifier as a key-set in cache
			Yii::app()->cache->set($this->_identity->getState('apiKey'), session_id(), 1800);
			return true;
		}
		else
			return false;
	}
}
