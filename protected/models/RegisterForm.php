<?php

/**
 */
class RegisterForm extends CFormModel
{
	public $email;
	public $password;
	public $password2;
	public $firstName;
	public $lastName;
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
			array('email, password, password2, firstName, lastName, displayName', 'required'),
			// password needs to be authenticated
			array('password', 'compare', 'compareAttribute'=>'password2'),
			array('password', 'length', 'min'=>8),
			array('email', 'email'),
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
				$this->addError('password','Incorrectly filled out details');
		}
	}
}
