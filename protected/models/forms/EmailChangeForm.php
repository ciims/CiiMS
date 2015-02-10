<?php

class EmailChangeForm extends CFormModel
{
	/**
	 * The user's existing password
	 * @var string $password
	 */
	public $password;

	/**
	 * The veification key
	 * @var string $verificationKey
	 */
	public $verificationKey;

	/**
	 * The user model
	 * @var Users $_user
	 */
	private $_user;

	/**
	 * The expiration time of the key. Set to 3 days in seconds
	 * @var string $_expirationTime
	 */
	private $_expirationTime = 259200;

	/**
	 * The new email address change key
	 * @var UserMetadata $_newEmailAddressChangeKey
	 */
	private $_newEmailAddressChangeKey;

	/**
	 * The new email address record on file
	 * @var UserMetadata $_newEmailAddress
	 */
	private $_newEmailAddress;

	/**
	 * Validation rules
	 * @return array
	 */
	public function rules()
	{
		return array(
			array('password, verificationKey', 'required'),
			array('password', 'length', 'min' => 8),
			array('password', 'validateUserPassword'),
			array('verificationKey', 'validateVerificationKey')
		);
	}

	/**
	 * Attribute labels
	 * @return array
	 */
	public function attributeLabels()
	{
		return array(
			'password'         => Yii::t('ciims.models.EmailChangeForm', 'Your Password'),
			'verificationKey'  => Yii::t('ciims.models.EmailChangeForm', 'The Verification Key')
		);
	}

	/**
	 * Validates the tokens supplied and that the request hasn't expired
	 * @param array $attributes
	 * @param array $params
	 * return array
	 */
	public function validateVerificationKey($attributes = array(), $params = array())
	{
		$this->_newEmailAddressChangeKey =  UserMetadata::model()->findByAttributes(array(
												'key' => 'newEmailAddressChangeKey',
												'value' => $this->verificationKey
										    ));

		if ($this->_newEmailAddressChangeKey == NULL)
		{
			$this->addError('verificationKey', Yii::t('ciims.models.EmailChangeForm', 'The activation key you provided is invalid'));
			return false;
		}

		$this->_newEmailAddress = UserMetadata::model()->findByAttributes(array(
									  'user_id' => $this->_newEmailAddressChangeKey->user_id,
									  'key' => 'newEmailAddress'
								  ));

		if ($this->_newEmailAddress == NULL)
		{
			$this->addError('verificationKey', Yii::t('ciims.models.EmailChangeForm', 'The activation key you provided is invalid'));
			return false;
		}

		// Load the user
		$this->_user = Users::model()->findByPk($this->_newEmailAddressChangeKey->user_id);

		return true;
	}

	/**
	 * Ensures that the password entered matches the one provided during registration
	 * @param array $attributes
	 * @param array $params
	 * return array
	 */
	public function validateUserPassword($attributes, $params)
	{
		$hash = Users::model()->encryptHash($this->_user->email, $this->password, Yii::app()->params['encryptionKey']);

		$result = password_verify($hash, $this->_user->password);

		if ($result == false)
		{
			$this->addError('password', Yii::t('ciims.models.ActivationForm', 'The password you entered does not match the password you registered with.'));
			return false;
		}

		return true;
	}

	/**
	 * Updates the user's email address and rehashes their password since the password is bound to the email
	 * @return boolean
	 */
	public function save()
	{
		if (!$this->validate())
			return false;

		$this->_user->attributes = array(
			'email' => $this->_newEmailAddress->value,
			'password' => $this->password
		);

		// Save the model
		if ($this->_user->save())
		{
			// Delete the metadata
			$this->_newEmailAddressChangeKey->delete();
			$this->_newEmailAddress->delete();
			return true;
		}

		return false;
	}

	/**
	 * Retrieves the user's email address from the model
	 * @return string
	 */
	public function getEmail()
	{
		return $this->_user->email;
	}
}
