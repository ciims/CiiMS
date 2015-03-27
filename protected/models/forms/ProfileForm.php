<?php

class ProfileForm extends CFormModel
{
	/**
	 * The user's email address
	 * @var string $email
	 */
	public $email;

	/**
	 * The user's NEW password
	 * @var string $password
	 */
	public $password;

	/**
	 * The repeated password if a NEW password is applied
	 * @var string $password_repeat
	 */
	public $password_repeat;

	/**
	 * The user's current password
	 * This field is required to make any changes to the account
	 * @var string $currentPassword
	 */
	public $currentPassword;

	/**
	 * The user's display name
	 * @var string $username
	 */
	public $username;

	/**
	 * The user role
	 * @var int $role
	 */
	public $user_role;

	/**
	 * The user model
	 * @var Users $_user
	 */
	private $_user = NULL;

	/**
	 * This form will likely be reused in admin portals, for re-use purposes authentication is not required to change privileged information
	 * @var boolean $overridePasswordCheck
	 */
	private $overridePasswordCheck = false;


	private function canOverridePasswordCheck()
	{
		if ($this->overridePasswordCheck)
			return true;

		if (isset(Yii::app()->user) && $this->getId() == Yii::app()->user->id)
			return true;

		return false;
	}

	/**
	 * Overload of the __getter method to retrieve the user's ID
	 * @var int $id
	 */
	public function getId()
	{
		return $this->_user->id;
	}

	/**
	 * Retrieves the new email address if it is set
	 * @return mixed
	 */
	public function getNewEmail()
	{
		$metadata = UserMetadata::model()->findByAttributes(array(
			'user_id' => $this->_user->id,
			'key'     => 'newEmailAddress'
		));

		if ($metadata == NULL)
			return NULL;

		return $metadata->value;
	}

	/**
	 * Sets the new email address
	 * @return boolean
	 */
	public function setNewEmail()
	{
		$metadata = UserMetadata::model()->findByAttributes(array(
			'user_id' => $this->_user->id,
			'key'     => 'newEmailAddress'
		));

		if ($metadata == NULL)
		{
			$metadata = new UserMetadata;
			$metadata->attributes = array(
				'user_id' => $this->_user->id,
				'key'     => 'newEmailAddress'
			);
		}

		$metadata->value = $this->email;

		// Save the record
		return $metadata->save();
	}

	/**
	 * Retrieves the new email address if it is set
	 * @return mixed
	 */
	public function getNewEmailChangeKey()
	{
		$metadata = UserMetadata::model()->findByAttributes(array(
			'user_id' => $this->_user->id,
			'key'     => 'newEmailAddressChangeKey'
		));

		if ($metadata == NULL)
			return NULL;

		return $metadata->value;
	}

	/**
	 * Generates a new change key
	 * @return boolean
	 */
	public function setNewEmailChangeKey()
	{
		$metadata = UserMetadata::model()->findByAttributes(array(
			'user_id' => $this->_user->id,
			'key'     => 'newEmailAddressChangeKey'
		));

		if ($metadata == NULL)
		{
			$metadata = new UserMetadata;
			$metadata->attributes = array(
				'user_id' => $this->_user->id,
				'key'     => 'newEmailAddressChangeKey'
			);
		}

		// Generate a new key
		$metadata->value = Cii::generateSafeHash();

		// Save the record
		return $metadata->save();
	}

	/**
	 * Validation rules
	 * @return array
	 */
	public function rules()
	{
		return array(
			array('email, username', 'required'),
			array('username', 'length', 'max' => 255),
			array('currentPassword', 'validateUserPassword'),
			array('password', 'compare'),
			array('password', 'length', 'min' => 8),
			array('user_role', 'numerical'),
			array('user_role', 'validateUserRole')
		);
	}

	/**
	 * Retrieves the attributes labels from the Users model and returns them to reduce code redundancy
	 * @return array
	 */
	public function attributeLabels()
	{
		return CMap::mergeArray(Users::model()->attributeLabels(), array(
			'currentPassword' => Yii::t('ciims.models.ProfileForm', 'Your current password'),
			'password_repeat' => Yii::t('ciims.models.ProfileForm', 'Your New Password (again)')
		));
	}

	/**
	 * Validates the role
	 * @param array $attributes
	 * @param array $params
	 * return array
	 */
	public function validateUserRole($attributes, $params)
	{
		if ($this->canOverridePasswordCheck())
			return true;

		$this->addError('user_role', Yii::t('ciims.models.ProfileForm', 'You do not have permission to modify this attribute'));
		return false;
	}

	/**
	 * Ensures that the password entered matches the one provided during registration
	 * @param array $attributes
	 * @param array $params
	 * return array
	 */
	public function validateUserPassword($attributes, $params)
	{
		// Apply the override if it was set
		if ($this->canOverridePasswordCheck())
		{
			$this->password_repeat = $this->password;
			return true;
		}

		$result = password_verify($this->password, $this->_user->password);
		
		if ($result == false)
		{
			$this->addError('currentPassword', Yii::t('ciims.models.ProfileForm', 'The password you entered is invalid.'));
			return false;
		}

		return true;
	}

	/**
	 * Internally loads the user's information before attempting to validate it
	 * @param int  $id         The user's ID
	 * @param bool $override   This form may be reused
	 * @return ProfileForm
	 */
	public function load($id, $override = false)
	{
		$this->overridePasswordCheck = $override;

		// Load the user
		$this->_user = Users::model()->findByPk($id);

		if ($this->_user == NULL)
			throw new CHttpException(400, Yii::t('ciims.models.ProfileForm', 'The request user\'s profile could not be loaded'));

		// Reload the attribute labels
		$this->attributes = array(
			'email'         => $this->_user->email,
			'username'      => $this->_user->username,
			'user_role'     => $this->_user->role->id
		);

		return $this;
	}

	/**
	 * Updates the user's profile information
	 * @return boolean
	 */
	public function save()
	{
		if (!$this->validate(NULL, false))
			return false;

		// Change the email address, if necessary
		$this->changeEmail();

		$this->_user->attributes = array(
			'password'      => $this->password,
			'username'      => $this->username,
			'user_role'     => $this->user_role
		);

		if ($this->_user->save())
			return true;

		return false;
	}

	/**
	 * Changes the user's email address if necessary
	 * @return boolean
	 */
	private function changeEmail()
	{
		if ($this->email != $this->_user->email)
		{
			$this->setNewemail();
			$this->setNewEmailChangeKey();
			$this->sendVerificationEmail();
		}

		return true;
	}

	/**
	 * Sends the verification email to the user. This is broken to it's own method to allow for the resending email to be resent
	 * @return boolean
	 */
	public function sendVerificationEmail()
	{
		return Yii::app()->controller->sendEmail(
			$this->_user,
			Yii::t('ciims.models.Users', 'CiiMS Email Change Notification'),
			'webroot.themes.' . Cii::getConfig('theme', 'default') .'.views.email.email-change',
			array(
				'key' => $this->setNewEmailChangeKey(),
				'user' => $this->_user
			)
		);
	}
}
