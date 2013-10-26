<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
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
	public $rememberMe;

	/**
	 * Whether or not we should perform a forced authentication. By default we aren't going to do this
	 * @var boolean
	 */
	private $force = false;

	/**
	 * The identity of the user
	 * @var CUserIdentity
	 */
	private $_identity;

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
			// username and password are required
			array('username, password', 'required'),
			// rememberMe needs to be a boolean
			array('rememberMe', 'boolean'),
			// password needs to be authenticated
			array('password', 'authenticate'),
		);
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'rememberMe'=> Yii::t('ciims.models.LoginForm', 'Remember me next time'),
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
			$this->_identity=new UserIdentity($this->username,$this->password);
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
		if($this->_identity===null)
		{
			$this->_identity=new UserIdentity($this->username,$this->password);
			$this->_identity->authenticate();
		}

		if($this->_identity->errorCode===UserIdentity::ERROR_NONE)
		{
			$duration=$this->rememberMe ? 3600*24 : 0; // 30 days
			Yii::app()->user->login($this->_identity,$duration);
			return true;
		}
		else
			return false;
	}
}
