<?php

/**
 */
class RegisterForm extends CFormModel
{
	/**
	 * The submitted email address
	 * @var string|email
	 */
	public $email;

	/**
	 * The submitted password
	 * @var string
	 */
	public $password;

	/**
	 * The password verification
	 * @var string
	 */
	public $password2;

	/**
	 * The submitted first name if it was supplied
	 * @var string
	 */
	public $firstName;

	/**
	 * The submitted last name if it was supplied
	 * @var string
	 */
	public $lastName;

	/**
	 * The display name as we will show it on the site
	 * @var string
	 */
	public $displayName;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return array(
			// username and password are required
			array('email, password, password2, displayName', 'required'),
			// password needs to be authenticated
			array('password', 'compare', 'compareAttribute'=>'password2'),
			array('password', 'length', 'min'=>8),
			array('email', 'email'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'email'       => Yii::t('ciims.models.RegisterForm', 'Email Address'),
			'password'    => Yii::t('ciims.models.RegisterForm', 'Password'),
			'password2'   => Yii::t('ciims.models.RegisterForm', 'Password (again)'),
			'displayName' => Yii::t('ciims.models.RegisterForm', 'Display Name'),
			'firstName'   => Yii::t('ciims.models.RegisterForm', 'First Name'),
			'lastName'    => Yii::t('ciims.models.RegisterForm', 'Last Name')
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
			if(!$this->_identity->authenticate())
				$this->addError('password',Yii::t('ciims.models.RegisterForm', 'Incorrectly filled out details.'));
		}
	}
}
