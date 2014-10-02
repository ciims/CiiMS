<?php

class InviteForm extends CFormModel
{
	/**
	 * @var $id int		The User's ID that we are working with 
	 */
	public $id = NULL;
	
	/**
	 * @var $displayName string	The user's requested display name
	 */
	public $username = NULL;
	
	/**
	 * @var $email string
	 */
	public $email = NULL;

	/**
	 * @var $password string	The user's selected password
	 */
	public $password = NULL;

	/**
	 * Validation Rules
	 * @return array
	 */
	public function rules()
	{
		return array(
			array('id, username, password', 'required'),
			array('username, password', 'length', 'max' => 255)	
		);
	}

	/**
	 * Attribute labels
	 * @return array
	 */
	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('ciims.models.InviteForm', 'ID'),
			'username' => Yii::t('ciims.models.InviteForm', 'Username'),
			'password' => Yii::t('ciims.models.InviteForm', 'Password'),
		);
	}

	/**
	 * Actually creates the users
	 * @param  int $user_id    The id of the user that was created
	 * @return bool            If the user was created
	 */
	public function acceptInvite()
	{
		if (!$this->validate())
			return false;
		
		$user = Users::model()->findByPk($this->id);

		// Bcrypt the initial password instead of just using the basic hashing mechanism
		$hash = Users::model()->encryptHash($this->email, $this->password, Yii::app()->params['encryptionKey']);
		$cost = Cii::getBcryptCost();

		$this->password = password_hash($hash, PASSWORD_BCRYPT, array('cost' => $cost));

		$user->attributes = array(
			'email'			=> $this->email,
			'password'		=> $this->password,
			'username'	    => $this->username,
			'status'		=> Users::ACTIVE
		);

		return $user->save();
	}

}