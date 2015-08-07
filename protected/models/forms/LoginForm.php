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
	 * Two factor authentication code
	 * @var string
	 */
	public $twoFactorCode = false;

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
	private $_identity = NULL;

	/**
	 * The Application Name for API requests
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
			array('twoFactorCode', 'hasTwoFactorCode')
		);
	}

	/**
	 * Yii attribute labels
	 * @return array
	 */
	public function attributeLabels()
	{
		return array(
			'username' 		=> Yii::t('ciims.models.LoginForm', 'Username'),
			'password' 		=> Yii::t('ciims.models.LoginForm', 'Password'),
			'twoFactorCode' => Yii::t('ciims.models.LoginForm', 'Two Factor Authentication Code'),
		);
	}

	/**
	 * Returns an instance of CiiUserIdentity
	 * @return CiiUserIdentity
	 */
	public function getIdentity()
	{
		if ($this->_identity === NULL)
			$this->_identity = new CiiUserIdentity($this->username, $this->password, $this->twoFactorCode);
		
		return $this->_identity;
	}
	
	/**
	 * Authenticates the password.
	 * This is the 'authenticate' validator as declared in rules().
	 */
	public function authenticate($attribute, $params)
	{
		if (!$this->hasErrors())
		{
			$this->getIdentity()->app_name = $this->app_name;
			if (!$this->getIdentity()->authenticate($this->force))
			{
				if ($this->getIdentity()->errorCode === 5)
				{
					$this->addError('twoFactorCode', Yii::t('ciims.models.LoginForm', 'Two factor code was not valid. Please try again.'));
					$this->username = NULL;
					$this->password = NULL;
					$this->twoFactorCode = NULL;
				}
				else
					$this->addError('password', Yii::t('ciims.models.LoginForm', 'Incorrect username or password.'));
			}
		}
	}

	/**
	 * Validator for two factor authentication codes
	 */
	public function hasTwoFactorCode($attribute, $params)
	{
		if ($this->twoFactorCode === false && $this->needsTwoFactorAuth())
			$this->addError('twoFactorCode', Yii::t('ciims.models.LoginForm', 'Please enter your two factor authentication code to proceed'));
	}

	/**
	 * Determines if two factor authentication code is required
	 * @return boolean
	 */
	public function needsTwoFactorAuth()
	{
		$user = $this->getIdentity()->getUser();

		// If the user is bad, we don't need a 2fa code
		if ($user == NULL)
			return false;

		// Only return true if the user needs a 2fa code and their password validation succeeded
		if ($user->needsTwoFactorAuth() && $this->getIdentity()->validatePassword())
			return true;
			
		return false;
	}
	
	/**
	 * Logs in the user using the given username and password in the model.
	 * @return boolean whether login is successful
	 */
	public function login()
	{
		if (!$this->validate())
			return false;
			
		if ($this->getIdentity()->errorCode === CiiUserIdentity::ERROR_NONE)
		{
			$duration = $this->rememberMe ? 3600*24 : 0; // 30 days
			Yii::app()->user->login($this->getIdentity(), $duration);
			
			// Store the API key and session_identifier as a key-set in cache
			Yii::app()->cache->set($this->getIdentity()->getState('apiKey'), session_id(), 1800);
			return true;
		}	
		else
			return false;
	}
}
